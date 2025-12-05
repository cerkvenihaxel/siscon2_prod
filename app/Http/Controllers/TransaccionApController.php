<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpConsumo;
use App\Models\ArticulosZafiro;
use App\Services\UnionPersonalSoapService;
use App\Services\TwilioSender;
use App\Enums\CodigosAlfabetaPermitidos;
use CRUDBooster;
use DB;

class TransaccionApController extends Controller
{
    private $soapService;
    private $twilioSender;

    public function __construct()
    {
        try {
            $this->soapService = new UnionPersonalSoapService();
        } catch (\Exception $e) {
            \Log::error('SOAP Service initialization failed: ' . $e->getMessage());
            $this->soapService = null;
        }
        $this->twilioSender = new TwilioSender();
    }

    public function index()
    {
        return view('transaccion_ap.index');
    }

    public function consultarElegibilidad(Request $request)
    {
        try {
            $startTime = microtime(true);

            $codigoAfiliado = $request->input('afiliado');
            if (empty($codigoAfiliado)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El código de afiliado es requerido'
                ], 400);
            }

            $params = [
                'start_time' => $startTime,
                'msgid' => 'SISCON2_TRANSAP_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
                'afiliado_codigo' => $codigoAfiliado,
                'prestador_id' => config('union_personal.prestador_id'),
                'usrid' => config('union_personal.user_id'),
                'usrpass' => config('union_personal.user_pass'),
                'verifid' => 'MANUAL',
            ];

            if (!empty($request->input('token'))) {
                $params['token'] = $request->input('token');
            } else {
                $params['plan'] = $request->input('plan') ?? null;
                $params['vercred'] = $request->input('vercred') ?? null;
            }

            try {
                $soapService = new UnionPersonalSoapService();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'SOAP extension not available: ' . $e->getMessage()
                ], 500);
            }
            
            // Capturar el XML de solicitud
            $xmlSolicitud = $this->construirXmlElegibilidad($params);
            
            // Ejecutar ELG y capturar respuesta
            $resultado = $soapService->ejecutarELG($params);
            
            // Intentar obtener XML de respuesta de diferentes fuentes
            $xmlRespuesta = 'No disponible';
            
            // 1. Desde el resultado directo
            if (isset($resultado['response_xml'])) {
                $xmlRespuesta = $resultado['response_xml'];
            }
            // 2. Desde la tabla de logs (buscar por tiempo reciente)
            else {
                $ultimaTransaccion = \DB::table('up_transacciones_soap')
                    ->where('transaction_type', 'ELG')
                    ->where('created_at', '>=', now()->subMinutes(2))
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($ultimaTransaccion && $ultimaTransaccion->response_xml) {
                    $xmlRespuesta = $ultimaTransaccion->response_xml;
                }
            }

            return response()->json([
                'success' => $resultado['success'],
                'message' => $resultado['message'],
                'status' => $resultado['data']['STATUS'] ?? ($resultado['success'] ? 'OK' : 'ERROR'),
                'idtran' => $resultado['idtran'] ?? null,
                'data' => $resultado['data'] ?? null,
                'xml_request' => $xmlSolicitud,
                'xml_response' => $xmlRespuesta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buscarArticulos(Request $request)
    {
        try {
            $query = ArticulosZafiro::select('id_articulo', 'presentacion_completa', 'nro_registro_alfabeta', 'pcio_vta_siva')
                ->whereNotNull('nro_registro_alfabeta')
                ->where('nro_registro_alfabeta', '!=', '')
                ->whereIn('nro_registro_alfabeta', CodigosAlfabetaPermitidos::CODIGOS);

            if ($request->search) {
                $query->where('presentacion_completa', 'like', '%' . $request->search . '%');
            }

            $articulos = $query->orderBy('presentacion_completa')
                ->limit(20)
                ->get();

            return response()->json($articulos->map(function($articulo) {
                return [
                    'id' => $articulo->id_articulo,
                    'text' => $articulo->presentacion_completa . ' - ' . $articulo->nro_registro_alfabeta,
                    'codigo' => $articulo->nro_registro_alfabeta,
                    'descripcion' => $articulo->presentacion_completa,
                    'precio' => $articulo->pcio_vta_siva ?? 0
                ];
            }));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function validarMedicamento(Request $request)
    {
        try {
            $codigo = $request->input('codigo');
            
            if (empty($codigo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código de medicamento requerido'
                ], 400);
            }

            $articulo = ArticulosZafiro::select('id_articulo', 'presentacion_completa', 'nro_registro_alfabeta', 'pcio_vta_siva')
                ->where('nro_registro_alfabeta', $codigo)
                ->first();

            if (!$articulo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medicamento no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $articulo->id_articulo,
                    'codigo' => $articulo->nro_registro_alfabeta,
                    'descripcion' => $articulo->presentacion_completa,
                    'precio' => $articulo->pcio_vta_siva ?? 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function procesarMultiplesAP(Request $request)
    {
        try {
            $medicamentos = $request->input('medicamentos', []);
            $afiliadoData = $request->input('afiliado_data', []);
            $prescripcionData = $request->input('prescripcion', []);

            if (empty($medicamentos)) {
                return response()->json(['success' => false, 'message' => 'No hay medicamentos para procesar']);
            }

            $resultados = [];

            foreach ($medicamentos as $index => $medicamento) {
                $xmlSolicitud = $this->construirXmlSolicitudAP([
                    'afiliado' => $afiliadoData['codigo'],
                    'token' => $afiliadoData['token'],
                    'plan' => $afiliadoData['plan'],
                    'vercred' => $afiliadoData['vercred'],
                    'tipo' => $medicamento['tipo'] ?? 'M', // Usar el tipo del medicamento
                    'codigo_prestacion' => $medicamento['codigo'],
                    'cantidad' => $medicamento['cantidad'],
                    'tipo_matricula' => $prescripcionData['tipo_matricula'] ?? '',
                    'matricula' => $prescripcionData['matricula'] ?? '',
                    'fecha_receta' => $prescripcionData['fecha_receta'] ?? ''
                ]);

                try {
                    if (!$this->soapService) {
                        throw new \Exception('SOAP service not available');
                    }
                    $responseXML = $this->soapService->enviarTransaccionAP($xmlSolicitud);
                    $xmlResponse = simplexml_load_string($responseXML);

                    $resultado = [
                        'medicamento' => [
                            'tipo' => $medicamento['tipo'] ?? 'M',
                            'codigo' => (string)($xmlResponse->PR->ID ?? $medicamento['codigo']),
                            'descripcion' => (string)($xmlResponse->PR->DESCRIPCION ?? $medicamento['descripcion'] ?? 'Medicamento manual'),
                            'cantidad' => (int)($xmlResponse->PR->CANT ?? $medicamento['cantidad'])
                        ],
                        'success' => true,
                        'status' => (string)($xmlResponse->PR->STATUS ?? $xmlResponse->STATUS),
                        'idtran' => (string)$xmlResponse->IDTRAN,
                        'idaut' => (string)$xmlResponse->IDAUT ?? null,
                        'mensaje' => (string)$xmlResponse->RSPMSGG,
                        'pr_status' => (string)$xmlResponse->PR->STATUS ?? null,
                        'pr_mensaje' => (string)$xmlResponse->PR->RSPMSGP ?? null,
                        'pr_adicional' => (string)$xmlResponse->PR->RSPMSGPADIC ?? null,
                        'xml_response' => $responseXML
                    ];

                    // Si status es OK, insertar en up_consumos
                    if ($resultado['status'] === 'OK') {
                        try {
                            $consumo = $this->insertarConsumoDesdeAP($xmlResponse, $medicamento, $afiliadoData);
                            $resultado['consumo_id'] = $consumo->id;
                            $resultado['consumo_created'] = true;
                        } catch (\Exception $e) {
                            $resultado['consumo_created'] = false;
                            $resultado['consumo_error'] = $e->getMessage();
                            \Log::error('Error al crear consumo desde AP: ' . $e->getMessage());
                        }
                    }

                    $resultados[] = $resultado;
                } catch (\Exception $e) {
                    $resultados[] = [
                        'medicamento' => $medicamento,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'resultados' => $resultados
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Insertar consumo desde transacción AP
     */
    private function insertarConsumoDesdeAP($xmlResponse, $medicamento, $afiliadoData)
    {
        // Extraer nodo PR
        $pr = $xmlResponse->PR ?? null;

        // Calcular edad desde fecha de nacimiento si está disponible
        $edad = 0;
        if (isset($xmlResponse->AFIFECNAC)) {
            try {
                $fechaNac = \DateTime::createFromFormat('d/m/Y', (string)$xmlResponse->AFIFECNAC);
                if ($fechaNac) {
                    $hoy = new \DateTime();
                    $edad = $hoy->diff($fechaNac)->y;
                }
            } catch (\Exception $e) {
                // Si falla el parseo, edad queda en 0
            }
        }

        // Extraer localidad y provincia del domicilio
        $provincia = (string)($xmlResponse->AFIPROV ?? '');
        $localidad = (string)($xmlResponse->AFILOC ?? '');
        
        // Si no vienen AFIPROV/AFILOC, parsear AFIDOM
        if (empty($localidad) && empty($provincia) && !empty($xmlResponse->AFIDOM)) {
            $parsed = $this->parsearDomicilioArgentino((string)$xmlResponse->AFIDOM);
            $localidad = $parsed['localidad'];
            $provincia = $parsed['provincia'];
        }

        // Obtener USRID del usuario actual
        $currentPrivilege = \CRUDBooster::myPrivilegeName();
        $currentUser = \CRUDBooster::me();
        $currentEmail = $currentUser ? $currentUser->email : null;
        $isFarmaciaUp = $currentPrivilege == 'Farmacias UP';
        
        $usrid = config('union_personal.' . config('union_personal.ambiente') . '.prestador_id');
        if ($isFarmaciaUp && $currentEmail) {
            $emailParts = explode('@', $currentEmail);
            $usrid = $emailParts[0];
        }

        $datos = [
            // Datos del afiliado - Estructura correcta del XML de AP
            'afiliado' => (string)($xmlResponse->AFICODIGO ?? $afiliadoData['codigo']),
            'apellidos' => (string)($xmlResponse->AFIAPE ?? ''),
            'nombres' => (string)($xmlResponse->AFINOM ?? ''),
            'modelo_plan' => (string)($xmlResponse->AFIPLAN ?? ''),
            'nombre_modelo_plan' => (string)($xmlResponse->AFIPLANNOM ?? ''),
            'codigopostal' => (string)($xmlResponse->AFICP ?? ''),
            'localidad' => $localidad,
            'provincia' => $provincia,
            'edad' => $edad,
            'tipo_afiliado' => (string)($xmlResponse->AFIAFIL ?? ''),
            'titofam' => (string)($xmlResponse->AFITITOFAM ?? ''),

            // Datos de la prestación
            'fecha_tran' => now(),
            'prestacion' => null, // Campo numérico de orden
            'tipo_pres' => (string)($pr->TIPO ?? 'M'),
            'cod_prestacion' => (string)($pr->ID ?? $medicamento['codigo']),
            'cant' => (int)($pr->CANT ?? $medicamento['cantidad']),
            'desc' => (string)($pr->DESCRIPCION ?? $medicamento['descripcion'] ?? ''),
            'status' => (string)$xmlResponse->STATUS,

            // Datos económicos del PR
            'cargo' => (float)($pr->CARGO ?? 0),
            'impos' => (float)($pr->IMPOS ?? 0),
            'impot' => 0, // No viene IMPOT en el XML, viene IMPTOTAL
            'imptot' => (float)($pr->IMPTOTAL ?? 0),
            'adic' => 0, // No viene en el XML de respuesta

            // Datos de la transacción
            'num_tran' => (string)$xmlResponse->IDTRAN,
            'idtran_aprobacion' => (string)$xmlResponse->IDTRAN,
            'idaut' => (string)($xmlResponse->IDAUT ?? ''),
            'emisor_app' => config('union_personal.app_name'),
            'cod_prestador' => $usrid,

            // Campos de flujo - Como viene de AP ya aprobado
            'estado_flujo' => 'aprobado',
            'fecha_aprobacion' => now(),
            'usuario_aprobacion' => auth()->check() ? auth()->user()->email : (CRUDBooster::myName() ?? 'sistema'),
        ];

        $consumo = UpConsumo::create($datos);

        // Enviar notificación de aprobación si el status es OK
        if ((string)$xmlResponse->STATUS === 'OK') {
            try {
                $nombreCompleto = trim($datos['nombres'] . ' ' . $datos['apellidos']);
                $fechaProceso = now()->format('d/m/Y');
                
                $this->twilioSender->sendNotificationApproved(
                    $datos['afiliado'],
                    $nombreCompleto,
                    $fechaProceso
                );
            } catch (\Exception $e) {
                \Log::error('Error enviando notificación AP aprobada: ' . $e->getMessage());
            }
        }

        return $consumo;
    }

    public function imprimirTicketRechazo(Request $request)
    {
        try {
            $data = [
                'fecha' => now()->format('d/m/Y H:i:s'),
                'afiliado' => $request->input('afiliado'),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'plan' => $request->input('plan'),
                'medicamento' => $request->input('medicamento'),
                'codigo_prestacion' => $request->input('codigo_prestacion'),
                'cantidad' => $request->input('cantidad'),
                'idtran' => $request->input('idtran'),
                'motivo_rechazo' => $request->input('motivo_rechazo'),
                'detalle_rechazo' => $request->input('detalle_rechazo'),
                'codigo_error' => $request->input('codigo_error')
            ];

            return view('transaccion_ap.ticket_rechazo', $data);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function construirXmlSolicitudAP($params)
    {
        $config = config('union_personal');
        $ambiente = $config['ambiente'];
        $ambienteConfig = $config[$ambiente];
        
        $currentPrivilege = \CRUDBooster::myPrivilegeName();
        $currentUser = \CRUDBooster::me();
        $currentEmail = $currentUser ? $currentUser->email : 'no-email';
        
        $isFarmaciaUp = $currentPrivilege == 'Farmacias UP';
        
        if ($isFarmaciaUp && $currentEmail && $currentEmail != 'no-email') {
            $emailParts = explode('@', $currentEmail);
            $usrid = $emailParts[0];
            $usrpass = 'DIAB';
            $idprestador = $usrid;
        } else {
            $usrid = $ambienteConfig['user_id'];
            $usrpass = $ambienteConfig['user_pass'];
            $idprestador = $ambienteConfig['prestador_id'];
        }
        
        $msgId = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $fecha = date('Y-m-d');
        $time = date('Y-m-d\TH:i:s');

        $pidContent = "<TIPOID>CODIGO</TIPOID><ID>{$params['afiliado']}</ID>";
        
        if (!empty($params['token'])) {
            $pidContent .= "<TOKEN>{$params['token']}</TOKEN>";
        } else {
            if (!empty($params['plan'])) $pidContent .= "<PLAN>{$params['plan']}</PLAN>";
            if (!empty($params['vercred'])) $pidContent .= "<VERCRED>{$params['vercred']}</VERCRED>";
        }
        
        $pidContent .= "<VERIFID>AUTO</VERIFID>";

        // Usar el tipo del parámetro (M o P)
        $tipo = $params['tipo'] ?? 'M';

        // Si es tipo M (Medicamento), obtener datos del medicamento desde ArticulosZafiro
        $troquel = '';
        $codbarra = '';
        $nomprod = '';
        $nompres = '';
        
        if ($tipo === 'M') {
            $articulo = ArticulosZafiro::where('nro_registro_alfabeta', $params['codigo_prestacion'])->first();
            if ($articulo) {
                $troquel = $articulo->nro_troquel ?? '';
                $codbarra = ''; // No hay campo código de barras en la tabla
                $nomprod = $articulo->des_articulo ?? '';
                $nompres = $articulo->presentacion ?? '';
            }
        }

        // Datos de prescripción desde el frontend
        $orgPrescripcion = $params['tipo_matricula'] ?? 'MP 12';
        $matPrescripcion = $params['matricula'] ?? '507';
        $fechaPrescripcion = $params['fecha_receta'] ?? $fecha;
        
        // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd si viene del frontend
        if (!empty($params['fecha_receta']) && strpos($params['fecha_receta'], '/') !== false) {
            $fechaParts = explode('/', $params['fecha_receta']);
            if (count($fechaParts) === 3) {
                $fechaPrescripcion = $fechaParts[2] . '-' . str_pad($fechaParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($fechaParts[0], 2, '0', STR_PAD_LEFT);
            }
        }

        // Construir el nodo PR según el tipo
        $prContent = "<TIPO>{$tipo}</TIPO><CANT>{$params['cantidad']}</CANT><ID>{$params['codigo_prestacion']}</ID>";
        
        if ($tipo === 'M') {
            // Para medicamentos, agregar campos específicos
            $prContent .= "<TROQUEL>{$troquel}</TROQUEL><CODBARRA>{$codbarra}</CODBARRA><NOMPROD>{$nomprod}</NOMPROD><NOMPRES>{$nompres}</NOMPRES>";
        }
        // Para prestaciones (P), solo se necesita TIPO, CANT e ID

        return "<SOLICITUD>
<EMISOR>
    <ID>hms-ca-web.osup.com.ar</ID>
    <PROT>CA_V20</PROT>
    <MSGID>{$msgId}</MSGID>
    <TER>Web</TER>
    <APP>{$config['app_name']}</APP>
    <TIME>{$time}</TIME>
</EMISOR>
<SEGURIDAD>
    <TIPOAUT>U</TIPOAUT>
    <USRID>{$usrid}</USRID>
    <USRPASS>{$usrpass}</USRPASS>
    <TIPOCON>PRES</TIPOCON>
</SEGURIDAD>
<OPER>
    <TIPO>AP</TIPO>
    <IDASEG>UP</IDASEG>
    <IDPRESTADOR>{$idprestador}</IDPRESTADOR>
    <FECHA>{$fecha}</FECHA>
</OPER>
<PID>
    {$pidContent}
</PID>
<CONTEXTO>
    <TIPO>A</TIPO>
</CONTEXTO>
<PRESCRIP>
    <ORG>{$orgPrescripcion}</ORG>
    <MAT>{$matPrescripcion}</MAT>
    <FECHA>{$fechaPrescripcion}</FECHA>
</PRESCRIP>
<PR>
    {$prContent}
</PR>
</SOLICITUD>";
    }

    private function construirXmlElegibilidad($params)
    {
        $config = config('union_personal');
        $ambiente = $config['ambiente'];
        $ambienteConfig = $config[$ambiente];
        
        // Verificar si el usuario tiene privilegio "Farmacias UP"
        $currentPrivilege = \CRUDBooster::myPrivilegeName();
        $currentUser = \CRUDBooster::me(); // Usar CRUDBooster::me() para obtener datos del usuario
        $currentEmail = $currentUser ? $currentUser->email : 'no-email';
        
        // Debug temporal
        \Log::info('Debug XML Elegibilidad', [
            'privilege' => $currentPrivilege,
            'email' => $currentEmail,
            'user_data' => $currentUser,
            'is_farmacias_up' => $currentPrivilege == 'Farmacias UP'
        ]);
        
        $isFarmaciaUp = $currentPrivilege == 'Farmacias UP';
        
        if ($isFarmaciaUp && $currentEmail && $currentEmail != 'no-email') {
            // Extraer USRID del email (parte antes del @)
            $emailParts = explode('@', $currentEmail);
            $usrid = $emailParts[0];
            $usrpass = 'DIAB';
            $idprestador = $usrid;
            
            \Log::info('Using Farmacias UP credentials', [
                'usrid' => $usrid,
                'usrpass' => $usrpass,
                'idprestador' => $idprestador
            ]);
        } else {
            // Usar credenciales del environment
            $usrid = $ambienteConfig['user_id'];
            $usrpass = $ambienteConfig['user_pass'];
            $idprestador = $ambienteConfig['prestador_id'];
            
            \Log::info('Using environment credentials', [
                'usrid' => $usrid,
                'usrpass' => $usrpass,
                'idprestador' => $idprestador
            ]);
        }
        
        $msgId = $params['msgid'];
        $fecha = date('Y-m-d');

        $pidContent = "<TIPOID>CODIGO_AFI</TIPOID><ID>{$params['afiliado_codigo']}</ID>";
        
        if (!empty($params['token'])) {
            $pidContent .= "<TOKEN>{$params['token']}</TOKEN>";
        } else {
            if (!empty($params['plan'])) $pidContent .= "<PLAN>{$params['plan']}</PLAN>";
            if (!empty($params['vercred'])) $pidContent .= "<VERCRED>{$params['vercred']}</VERCRED>";
        }
        
        $pidContent .= "<VERIFID>{$params['verifid']}</VERIFID>";

        return "
        <SOLICITUD>
            <EMISOR>
                <ID>INTEGRACION-UP</ID>
                <PROT>CA_V20</PROT>
                <TER>01</TER>
                <APP>{$config['app_name']}</APP>
                <MSGID>{$msgId}</MSGID>
            </EMISOR>
            <SEGURIDAD>
                <TIPOAUT>U</TIPOAUT>
                <TIPOCON>PRES</TIPOCON>
                <USRID>{$usrid}</USRID>
                <USRPASS>{$usrpass}</USRPASS>
            </SEGURIDAD>
            <OPER>
                <TIPO>ELG</TIPO>
                <IDASEG>UP</IDASEG>
                <IDPRESTADOR>{$idprestador}</IDPRESTADOR>
                <FECHA>{$fecha}</FECHA>
            </OPER>
            <PID>
                {$pidContent}
            </PID>
        </SOLICITUD>";
    }

    public function getXML($idtran)
    {
        try {
            // Buscar XML en la tabla de transacciones SOAP UP
            $transaccion = DB::table('up_transacciones_soap')
                ->where('idtran', $idtran)
                ->first();
            
            if ($transaccion) {
                return response()->json([
                    'success' => true,
                    'xml_request' => $transaccion->request_xml,
                    'xml_response' => $transaccion->response_xml
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información XML para esta transacción'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function parsearDomicilioArgentino($domicilio)
    {
        $provincias = [
            'BUENOS AIRES', 'CATAMARCA', 'CHACO', 'CHUBUT', 'CORDOBA', 'CORRIENTES',
            'ENTRE RIOS', 'FORMOSA', 'JUJUY', 'LA PAMPA', 'LA RIOJA', 'MENDOZA',
            'MISIONES', 'NEUQUEN', 'RIO NEGRO', 'SALTA', 'SAN JUAN', 'SAN LUIS',
            'SANTA CRUZ', 'SANTA FE', 'SANTIAGO DEL ESTERO', 'TIERRA DEL FUEGO',
            'TUCUMAN', 'CABA', 'CIUDAD AUTONOMA DE BUENOS AIRES'
        ];

        $domicilioUpper = strtoupper(trim($domicilio));
        
        foreach ($provincias as $prov) {
            if (strpos($domicilioUpper, $prov) !== false) {
                $localidad = trim(str_replace($prov, '', $domicilioUpper));
                return ['localidad' => $localidad, 'provincia' => $prov];
            }
        }

        return ['localidad' => $domicilio, 'provincia' => ''];
    }
}
