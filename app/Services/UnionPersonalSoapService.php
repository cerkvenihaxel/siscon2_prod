<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use App\Models\AfiliadoConvenioUp;
use App\Models\UpTransaccionSoap;
use Illuminate\Support\Facades\Log;
use Exception;

class UnionPersonalSoapService
{
    private $client;
    private $config;
    private $ambiente;
    private $startTime;

    public function __construct()
    {
        if (!extension_loaded('soap')) {
            throw new \Exception('SOAP extension is not installed. Please install php-soap extension.');
        }
        
        $this->config = config('union_personal');
        $this->ambiente = $this->config['ambiente'];
        $this->initSoapClient();
    }

    /**
     * Inicializar cliente SOAP
     */
    private function initSoapClient()
    {
        try {
            $ambienteConfig = $this->config[$this->ambiente];

            $options = [
                'trace' => 1,
                'exceptions' => true,
                'cache_wsdl' => $this->config['cache_wsdl'],
                'connection_timeout' => $this->config['connection_timeout'],
                'features' => defined('SOAP_SINGLE_ELEMENT_ARRAYS') ? SOAP_SINGLE_ELEMENT_ARRAYS : 1,
                'soap_version' => defined('SOAP_1_1') ? SOAP_1_1 : 1,
                'encoding' => 'UTF-8',
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ],
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'PHP SOAP Client'
                    ]
                ])
            ];

            $this->client = new SoapClient($ambienteConfig['wsdl'], $options);

            if ($this->config['log_enabled']) {
                Log::info('SOAP Client inicializado', [
                    'ambiente' => $this->ambiente,
                    'endpoint' => $ambienteConfig['endpoint']
                ]);
            }
        } catch (SoapFault $e) {
            Log::error('Error inicializando SOAP Client', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Si falla en producción, intentar con test
            if ($this->ambiente === 'produccion') {
                Log::warning('Intentando conectar con ambiente de test como fallback');
                $this->ambiente = 'test';
                $this->initSoapClient();
                return;
            }
            
            throw $e;
        }
    }

    /**
     * Test de conexión con el servicio
     */
    public function testWs()
    {
        $this->startTime = microtime(true);

        try {
            $response = $this->client->TestWs(['param' => 'SISCON2_TEST']);

            $executionTime = (microtime(true) - $this->startTime) * 1000;

            $this->logTransaccion([
                'transaction_type' => 'TestWs',
                'status' => 'OK',
                'response_message' => $response->return ?? 'Test exitoso',
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => true,
                'message' => $response->return ?? 'Conexión exitosa',
                'data' => $response,
            ];
        } catch (Exception $e) {
            return $this->handleError($e, 'TestWs');
        }
    }

    /**
     * Ejecutar operación ELG (Elegibilidad)
     * 🌟 CON AUTOCREACIÓN DE AFILIADOS
     */
    public function ejecutarELG(array $params)
    {
        $this->startTime = microtime(true);

        try {
            // Generar MSGID único
            $msgid = $this->generarMsgId('ELG');

            // Construir XML de solicitud
            $solicitudXML = $this->buildSolicitudXML('ELG', array_merge($params, ['msgid' => $msgid]));

            // Log del request
            if ($this->config['log_enabled']) {
                Log::info('ELG Request XML', ['xml' => $solicitudXML]);
            }

            // Ejecutar llamada SOAP con estructura correcta usando SoapVar
            $soapVar = new \SoapVar($solicitudXML, XSD_STRING);
            $response = $this->client->transaccionStr($soapVar);

            // Log del request y response crudos
            if ($this->config['log_enabled']) {
                Log::info('SOAP Raw Request', ['xml' => $this->client->__getLastRequest()]);
                Log::info('SOAP Raw Response', ['xml' => $this->client->__getLastResponse()]);
            }

            // Log de la respuesta
            if ($this->config['log_enabled']) {
                Log::info('ELG Response', ['response' => $response]);
            }

            // Validar respuesta - verificar si viene en response->return o directamente
            $responseXML = null;
            if (isset($response->return)) {
                $responseXML = $response->return;
            } elseif (is_string($response)) {
                $responseXML = $response;
            } else {
                // Intentar extraer de la estructura SOAP
                if (is_object($response) && property_exists($response, 'transaccionStrResponse')) {
                    $responseXML = $response->transaccionStrResponse->return ?? null;
                }
            }

            if (empty($responseXML)) {
                throw new \Exception('Respuesta SOAP vacía o inválida (no se pudo extraer XML de respuesta)');
            }

            // Parsear respuesta
            $resultado = $this->parseRespuestaSOAP($responseXML);

            $executionTime = (microtime(true) - $this->startTime) * 1000;

            // Determinar si fue exitoso
            $status = $resultado['data']['STATUS'] ?? '';
            $isSuccess = ($status === 'OK');

            // 🌟 AUTOCREACIÓN DE AFILIADOS (solo si STATUS=OK)
            if ($isSuccess && $this->config['autocrear_afiliados']) {
                $this->autocrearAfiliado($resultado['data']);
            }

            // Logging
            $this->logTransaccion([
                'solicitud_id' => $params['solicitud_id'] ?? null,
                'transaction_type' => 'ELG',
                'msgid' => $msgid,
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
                'afiliado_codigo' => $params['afiliado_codigo'] ?? null,
                'request_xml' => $solicitudXML,
                'response_xml' => $responseXML,
                'status' => $status,
                'response_code' => $resultado['data']['RSPCODG'] ?? null,
                'response_message' => $resultado['data']['RSPMSGG'] ?? null,
                'response_data' => $resultado['data'],
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => $isSuccess,
                'message' => $resultado['data']['RSPMSGG'] ?? ($isSuccess ? 'Operación completada' : 'Error en la transacción'),
                'status' => $status,
                'data' => $resultado['data'],
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
                'response_time' => number_format($executionTime / 1000, 3),
            ];

        } catch (Exception $e) {
            return $this->handleError($e, 'ELG', $params);
        }
    }

    /**
     * Ejecutar operación AP (Autorizar Prestación)
     */
    public function ejecutarAP(array $params)
    {
        $this->startTime = microtime(true);

        try {
            $msgid = $this->generarMsgId('AP');
            $solicitudXML = $this->buildSolicitudXML('AP', array_merge($params, ['msgid' => $msgid]));

            $response = $this->client->transaccionStr(new \SoapVar($solicitudXML, XSD_STRING));

            // Validar respuesta - verificar si viene en response->return o directamente
            $responseXML = null;
            if (isset($response->return)) {
                $responseXML = $response->return;
            } elseif (is_string($response)) {
                $responseXML = $response;
            } else {
                // Intentar extraer de la estructura SOAP
                if (is_object($response) && property_exists($response, 'transaccionStrResponse')) {
                    $responseXML = $response->transaccionStrResponse->return ?? null;
                }
            }

            if (empty($responseXML)) {
                throw new \Exception('Respuesta SOAP vacía o inválida (no se pudo extraer XML de respuesta)');
            }

            $resultado = $this->parseRespuestaSOAP($responseXML);

            $executionTime = (microtime(true) - $this->startTime) * 1000;

            $this->logTransaccion([
                'solicitud_id' => $params['solicitud_id'] ?? null,
                'transaction_type' => 'AP',
                'msgid' => $msgid,
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
                'idaut' => $resultado['data']['IDAUT'] ?? null,
                'afiliado_codigo' => $params['afiliado_codigo'] ?? null,
                'request_xml' => $solicitudXML,
                'response_xml' => $responseXML,
                'status' => $resultado['status'],
                'response_code' => $resultado['data']['RSPCODG'] ?? null,
                'response_message' => $resultado['data']['RSPMSGG'] ?? null,
                'response_data' => $resultado['data'],
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => $resultado['status'] === 'OK',
                'message' => $resultado['data']['RSPMSGG'] ?? 'Operación completada',
                'data' => $resultado['data'],
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
                'idaut' => $resultado['data']['IDAUT'] ?? null,
            ];

        } catch (Exception $e) {
            return $this->handleError($e, 'AP', $params);
        }
    }

    /**
     * Ejecutar operación ATR (Anulación)
     */
    public function ejecutarATR(array $params)
    {
        $this->startTime = microtime(true);

        try {
            $msgid = $this->generarMsgId('ATR');
            $solicitudXML = $this->buildSolicitudXML('ATR', array_merge($params, ['msgid' => $msgid]));

            $response = $this->client->transaccionStr(new \SoapVar($solicitudXML, XSD_STRING));

            // Validar respuesta - verificar si viene en response->return o directamente
            $responseXML = null;
            if (isset($response->return)) {
                $responseXML = $response->return;
            } elseif (is_string($response)) {
                $responseXML = $response;
            } else {
                // Intentar extraer de la estructura SOAP
                if (is_object($response) && property_exists($response, 'transaccionStrResponse')) {
                    $responseXML = $response->transaccionStrResponse->return ?? null;
                }
            }

            if (empty($responseXML)) {
                throw new \Exception('Respuesta SOAP vacía o inválida (no se pudo extraer XML de respuesta)');
            }

            $resultado = $this->parseRespuestaSOAP($responseXML);

            $executionTime = (microtime(true) - $this->startTime) * 1000;

            $this->logTransaccion([
                'solicitud_id' => $params['solicitud_id'] ?? null,
                'transaction_type' => 'ATR',
                'msgid' => $msgid,
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
                'afiliado_codigo' => $params['afiliado_codigo'] ?? null,
                'request_xml' => $solicitudXML,
                'response_xml' => $responseXML,
                'status' => $resultado['status'],
                'response_code' => $resultado['data']['RSPCODG'] ?? null,
                'response_message' => $resultado['data']['RSPMSGG'] ?? null,
                'response_data' => $resultado['data'],
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => $resultado['status'] === 'OK',
                'message' => $resultado['data']['RSPMSGG'] ?? 'Anulación completada',
                'data' => $resultado['data'],
                'idtran' => $resultado['data']['IDTRAN'] ?? null,
            ];

        } catch (Exception $e) {
            return $this->handleError($e, 'ATR', $params);
        }
    }

    /**
     * 🌟 AUTOCREACIÓN DE AFILIADOS desde respuesta SOAP
     */
    private function autocrearAfiliado(array $soapData)
    {
        try {
            if (empty($soapData['AFICODIGO'])) {
                return null;
            }

            $afiliado = AfiliadoConvenioUp::createOrUpdateFromSoap($soapData);

            if ($this->config['log_enabled']) {
                Log::info('Afiliado autocreado/actualizado desde SOAP', [
                    'codigo_afiliado' => $afiliado->codigo_afiliado,
                    'nombre_completo' => $afiliado->nombre_completo,
                ]);
            }

            return $afiliado;
        } catch (Exception $e) {
            Log::error('Error autocreando afiliado', [
                'error' => $e->getMessage(),
                'soap_data' => $soapData,
            ]);
            return null;
        }
    }

    /**
     * Construir XML de solicitud
     */
    private function buildSolicitudXML(string $tipo, array $params)
    {
        // Pasar el tipo de operación a los parámetros
        $params['tipo_operacion'] = $tipo;

        // Verificar si el usuario tiene privilegio "Farmacias UP"
        $currentPrivilege = \CRUDBooster::myPrivilegeName();
        $currentUser = \CRUDBooster::me();
        $currentEmail = $currentUser ? $currentUser->email : null;
        
        $isFarmaciaUp = $currentPrivilege == 'Farmacias UP';
        
        if ($isFarmaciaUp && $currentEmail) {
            // Extraer USRID del email (parte antes del @)
            $emailParts = explode('@', $currentEmail);
            $usrid = $emailParts[0];
            $params['usrid'] = $usrid;
            $params['usrpass'] = 'DIAB';
            $params['prestador_id'] = $usrid; // El IDPRESTADOR es el mismo que USRID
        }

        $emisor = $this->buildEmisorSegment($params['msgid']);
        $seguridad = $this->buildSeguridadSegment($params);
        $oper = $this->buildOperSegment($tipo, $params);
        $pid = $this->buildPidSegment($params);

        // CONTEXTO solo para AP, va después de PID y antes de PR
        $contexto = '';
        if ($tipo === 'AP') {
            $contexto = $this->buildContextoSegment($params);
        }

        $pr = isset($params['prestaciones']) ? $this->buildPrSegment($params['prestaciones']) : '';

        return "<SOLICITUD>{$emisor}{$seguridad}{$oper}{$pid}{$contexto}{$pr}</SOLICITUD>";
    }

    /**
     * Construir segmento EMISOR
     */
    private function buildEmisorSegment(string $msgid)
    {
        $emisorId = $this->config['emisor_id'];
        $protocolo = $this->config['protocolo'];
        $appName = $this->config['app_name'];
        $terminal = $this->config['terminal'];

        return "<EMISOR>" .
               "<ID>{$emisorId}</ID>" .
               "<PROT>{$protocolo}</PROT>" .
               "<TER>{$terminal}</TER>" .
               "<APP>{$appName}</APP>" .
               "<MSGID>{$msgid}</MSGID>" .
               "</EMISOR>";
    }

    /**
     * Construir segmento SEGURIDAD
     */
    private function buildSeguridadSegment(array $params)
    {
        $ambienteConfig = $this->config[$this->ambiente];
        $usrid = $params['usrid'] ?? $ambienteConfig['user_id'];
        $usrpass = $params['usrpass'] ?? $ambienteConfig['user_pass'];
        $tipoaut = $params['tipoaut'] ?? 'U';
        $tipocon = $params['tipocon'] ?? 'PRES';

        return "<SEGURIDAD>" .
               "<TIPOAUT>{$tipoaut}</TIPOAUT>" .
               "<TIPOCON>{$tipocon}</TIPOCON>" .
               "<USRID>{$usrid}</USRID>" .
               "<USRPASS>{$usrpass}</USRPASS>" .
               "</SEGURIDAD>";
    }

    /**
     * Construir segmento OPER
     */
    private function buildOperSegment(string $tipo, array $params)
    {
        $fecha = $params['fecha'] ?? now()->format('Y-m-d');
        $idaseg = 'UP'; // Código de aseguradora, NO el código del afiliado
        $ambienteConfig = $this->config[$this->ambiente];
        $idprestador = $params['prestador_id'] ?? $ambienteConfig['prestador_id'];

        $xml = "<OPER>" .
               "<TIPO>{$tipo}</TIPO>" .
               "<IDASEG>{$idaseg}</IDASEG>" .
               "<IDPRESTADOR>{$idprestador}</IDPRESTADOR>";

        // Para ATR: TIPOIDANUL e IDANUL van ANTES de FECHA
        if ($tipo === 'ATR') {
            $tipoidanul = $params['tipoidanul'] ?? 'IDTRAN';
            $idanul = $params['idanul'] ?? '';
            $xml .= "<TIPOIDANUL>{$tipoidanul}</TIPOIDANUL>";
            $xml .= "<IDANUL>{$idanul}</IDANUL>";
        }

        // FECHA siempre al final de OPER
        $xml .= "<FECHA>{$fecha}</FECHA>";

        // Motivo para ATR (opcional, solo si está presente)
        if ($tipo === 'ATR' && !empty($params['motivo'])) {
            $xml .= "<MOTIVO><![CDATA[{$params['motivo']}]]></MOTIVO>";
        }

        $xml .= "</OPER>";

        return $xml;
    }

    /**
     * Construir segmento CONTEXTO (solo para AP)
     */
    private function buildContextoSegment(array $params)
    {
        $tipo = $params['contexto_tipo'] ?? 'A'; // A=Ambulatorio, I=Internado, U=Urgencia

        return "<CONTEXTO><TIPO>{$tipo}</TIPO></CONTEXTO>";
    }

    /**
     * Construir segmento PID (Datos del afiliado)
     */
    private function buildPidSegment(array $params)
    {
        $id = $params['afiliado_codigo'] ?? '';
        $tipo = $params['tipo_operacion'] ?? 'ELG'; // Detectar tipo de operación

        // Orden correcto: TIPOID, ID, TOKEN/PLAN, VERIFID (solo ELG/AP), VERCRED
        $xml = "<PID>";
        $xml .= "<TIPOID>CODIGO_AFI</TIPOID>";
        $xml .= "<ID>{$id}</ID>";

        // Usar TOKEN o Plan/VerCred
        if (!empty($params['token'])) {
            $xml .= "<TOKEN>{$params['token']}</TOKEN>";
        } elseif (!empty($params['plan'])) {
            $xml .= "<PLAN>{$params['plan']}</PLAN>";
        }

        // VERCRED solo si no usa TOKEN
        if (empty($params['token']) && !empty($params['vercred'])) {
            $xml .= "<VERCRED>{$params['vercred']}</VERCRED>";
        }

        // VERIFID solo para ELG y AP (NO para ATR)
        if ($tipo !== 'ATR' && !empty($params['verifid'])) {
            $xml .= "<VERIFID>{$params['verifid']}</VERIFID>";
        }

        $xml .= "</PID>";

        return $xml;
    }

    /**
     * Construir segmento PR (Prestaciones)
     */
    private function buildPrSegment(array $prestaciones)
    {
        $xml = '';

        foreach ($prestaciones as $prest) {
            $xml .= "<PR>";
            $xml .= "<TIPO>{$prest['tipo']}</TIPO>";
            $xml .= "<ID>{$prest['id']}</ID>";
            $xml .= "<CANT>{$prest['cant']}</CANT>";

            // Para medicamentos
            if ($prest['tipo'] === 'M') {
                if (!empty($prest['troquel'])) {
                    $xml .= "<TROQUEL>{$prest['troquel']}</TROQUEL>";
                }
                if (!empty($prest['codbarra'])) {
                    $xml .= "<CODBARRA>{$prest['codbarra']}</CODBARRA>";
                }
            }

            $xml .= "</PR>";
        }

        return $xml;
    }

    /**
     * Parsear respuesta SOAP XML
     */
    private function parseRespuestaSOAP(string $xml)
    {
        try {
            $xmlObj = simplexml_load_string($xml);

            $data = [
                'IDTRAN' => (string)($xmlObj->IDTRAN ?? ''),
                'OPER' => (string)($xmlObj->OPER ?? ''),
                'STATUS' => (string)($xmlObj->STATUS ?? ''),
                'RSPCODG' => (string)($xmlObj->RSPCODG ?? ''),
                'RSPMSGG' => (string)($xmlObj->RSPMSGG ?? ''),
                'RSPMSGGADIC' => (string)($xmlObj->RSPMSGGADIC ?? ''),
            ];

            // Datos del afiliado (pueden estar en nivel raíz o en tag AFI)
            $afiData = isset($xmlObj->AFI) ? $xmlObj->AFI : $xmlObj;

            if (isset($afiData->AFICODIGO) || isset($afiData->CODIGO)) {
                $data['AFI'] = [
                    'CODIGO' => (string)($afiData->AFICODIGO ?? $afiData->CODIGO ?? ''),
                    'APELLIDO' => (string)($afiData->AFIAPE ?? $afiData->APE ?? ''),
                    'NOMBRE' => (string)($afiData->AFINOM ?? $afiData->NOM ?? ''),
                    'PLAN' => (string)($afiData->AFIPLAN ?? $afiData->PLAN ?? ''),
                    'PLAN_NOMBRE' => (string)($afiData->AFIPLANNOM ?? $afiData->PLANNOM ?? ''),
                    'SEXO' => (string)($afiData->SEXO ?? $afiData->AFISEXO ?? ''),
                    'FNAC' => (string)($afiData->AFIFECNAC ?? $afiData->FECNAC ?? ''),
                    'EDAD' => (string)($afiData->AFIEDAD ?? $afiData->EDAD ?? ''),
                    'CODPOS' => (string)($afiData->AFICP ?? $afiData->CP ?? ''),
                    'LOCALIDAD' => (string)($afiData->AFILOC ?? $afiData->LOC ?? ''),
                    'PROVINCIA' => (string)($afiData->AFIPROV ?? $afiData->PROV ?? ''),
                    'VERCRED' => (string)($afiData->AFIVERCRED ?? $afiData->VERCRED ?? ''),
                    'TIPOAFI' => (string)($afiData->AFIAFIL ?? $afiData->AFIL ?? ''),
                    'TITOFAM' => (string)($afiData->AFITITOFAM ?? $afiData->TITOFAM ?? ''),
                    'DOMICILIO' => (string)($afiData->AFIDOM ?? $afiData->DOM ?? ''),
                    'TIPODOC' => (string)($afiData->AFITIPODOC ?? $afiData->TIPODOC ?? ''),
                    'NRODOC' => (string)($afiData->AFINRODOC ?? $afiData->NRODOC ?? ''),
                ];
            }

            // ID de autorización (para AP)
            if (isset($xmlObj->IDAUT)) {
                $data['IDAUT'] = (string)$xmlObj->IDAUT;
            }

            // Prestaciones (PR)
            if (isset($xmlObj->PR)) {
                $data['PR'] = [];
                foreach ($xmlObj->PR as $pr) {
                    $data['PR'][] = [
                        'TIPO' => (string)($pr->TIPO ?? ''),
                        'ID' => (string)($pr->ID ?? ''),
                        'DESCRIPCION' => (string)($pr->DESCRIPCION ?? ''),
                        'STATUS' => (string)($pr->STATUS ?? ''),
                        'CANT' => (string)($pr->CANT ?? ''),
                        'CARGO' => (string)($pr->CARGO ?? ''),
                        'IMPOS' => (string)($pr->IMPOS ?? ''),
                        'IMPOT' => (string)($pr->IMPOT ?? ''),
                        'IMPTOTAL' => (string)($pr->IMPTOTAL ?? ''),
                        'ADIC' => (string)($pr->ADIC ?? ''),
                        'RSPCODP' => (string)($pr->RSPCODP ?? ''),
                        'RSPMSGP' => (string)($pr->RSPMSGP ?? ''),
                    ];
                }
            }

            return [
                'status' => $data['STATUS'],
                'data' => $data,
            ];

        } catch (Exception $e) {
            Log::error('Error parseando respuesta SOAP', [
                'error' => $e->getMessage(),
                'xml' => $xml,
            ]);

            return [
                'status' => 'ERROR',
                'data' => ['RSPMSGG' => 'Error parseando respuesta: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Generar MSGID único
     */
    private function generarMsgId(string $tipo)
    {
        return 'SISCON2_' . $tipo . '_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4));
    }

    /**
     * Logging de transacción en BD
     */
    private function logTransaccion(array $data)
    {
        if (!$this->config['db_log_enabled']) {
            return;
        }

        try {
            UpTransaccionSoap::registrar(array_merge($data, [
                'soap_endpoint' => $this->config[$this->ambiente]['endpoint'],
            ]));
        } catch (Exception $e) {
            Log::error('Error guardando transacción SOAP en BD', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Consultar elegibilidad de afiliado (método simplificado para TransaccionApController)
     */
    public function consultarElegibilidad(array $params)
    {
        return $this->ejecutarELG([
            'afiliado_codigo' => $params['afiliado'],
            'tipo_documento' => $params['tipo_documento'] ?? 'DNI',
            'verifid' => 'AUTO'
        ]);
    }

    /**
     * Enviar transacción AP (método simplificado para TransaccionApController)
     */
    public function enviarTransaccionAP(string $xmlSolicitud)
    {
        $this->startTime = microtime(true);

        try {
            // Log del request
            if ($this->config['log_enabled']) {
                Log::info('AP Request XML', ['xml' => $xmlSolicitud]);
            }

            // Ejecutar llamada SOAP
            $soapVar = new \SoapVar($xmlSolicitud, XSD_STRING);
            $response = $this->client->transaccionStr($soapVar);

            // Extraer XML de respuesta
            $responseXML = null;
            if (isset($response->return)) {
                $responseXML = $response->return;
            } elseif (is_string($response)) {
                $responseXML = $response;
            } else {
                if (is_object($response) && property_exists($response, 'transaccionStrResponse')) {
                    $responseXML = $response->transaccionStrResponse->return ?? null;
                }
            }

            if (empty($responseXML)) {
                throw new \Exception('Respuesta SOAP vacía o inválida');
            }

            // Log del response
            if ($this->config['log_enabled']) {
                Log::info('AP Response XML', ['xml' => $responseXML]);
            }

            $executionTime = (microtime(true) - $this->startTime) * 1000;

            // Parsear respuesta para extraer idtran
            $idtran = null;
            try {
                $xmlResponse = simplexml_load_string($responseXML);
                if ($xmlResponse && isset($xmlResponse->IDTRAN)) {
                    $idtran = (string)$xmlResponse->IDTRAN;
                }
            } catch (Exception $e) {
                Log::warning('No se pudo parsear IDTRAN de la respuesta', ['error' => $e->getMessage()]);
            }

            // Log de transacción
            $this->logTransaccion([
                'transaction_type' => 'AP',
                'idtran' => $idtran,
                'request_xml' => $xmlSolicitud,
                'response_xml' => $responseXML,
                'execution_time_ms' => $executionTime,
            ]);

            return $responseXML;

        } catch (Exception $e) {
            Log::error('Error en transacción AP', [
                'error' => $e->getMessage(),
                'xml' => $xmlSolicitud
            ]);
            throw $e;
        }
    }

    /**
     * Manejo de errores
     */
    private function handleError(Exception $e, string $tipo, array $params = [])
    {
        $executionTime = isset($this->startTime) ? (microtime(true) - $this->startTime) * 1000 : 0;

        Log::error("Error SOAP {$tipo}", [
            'error' => $e->getMessage(),
            'params' => $params,
            'trace' => $e->getTraceAsString(),
        ]);

        $this->logTransaccion([
            'solicitud_id' => $params['solicitud_id'] ?? null,
            'transaction_type' => $tipo,
            'afiliado_codigo' => $params['afiliado_codigo'] ?? null,
            'status' => 'ERROR',
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'execution_time_ms' => $executionTime,
        ]);

        return [
            'success' => false,
            'message' => 'Error en la transacción SOAP: ' . $e->getMessage(),
            'error' => $e->getMessage(),
        ];
    }
}
