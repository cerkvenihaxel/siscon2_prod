<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpAnulacion;
use App\Services\UnionPersonalSoapService;
use DB;

class AnulacionUpController extends Controller
{
    private $soapService;

    public function __construct()
    {
        $this->soapService = new UnionPersonalSoapService();
    }

    public function index()
    {
        return view('anulacion_up.index');
    }

    public function procesarAnulacion(Request $request)
    {
        try {
            $startTime = microtime(true);

            $params = [
                'start_time' => $startTime,
                'msgid' => 'SISCON2_ATR_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
                'afiliado_codigo' => $request->input('afiliado'),
                'tipoidanul' => $request->input('tipo_anulacion'),
                'idanul' => $request->input('id_anulacion'),
                'motivo' => $request->input('motivo'),
                'prestador_id' => config('union_personal.prestador_id'),
                'usrid' => config('union_personal.user_id'),
                'usrpass' => config('union_personal.user_pass'),
            ];

            if (!empty($request->input('token'))) {
                $params['token'] = $request->input('token');
            } else {
                $params['plan'] = $request->input('plan') ?? null;
                $params['vercred'] = $request->input('vercred') ?? null;
            }

            // Construir XML de solicitud
            $xmlSolicitud = $this->construirXmlAnulacion($params);
            
            // Ejecutar ATR
            $resultado = $this->soapService->ejecutarATR($params);
            
            // Obtener XML de respuesta
            $xmlRespuesta = 'No disponible';
            if (isset($resultado['response_xml'])) {
                $xmlRespuesta = $resultado['response_xml'];
            } else {
                $ultimaTransaccion = DB::table('up_transacciones_soap')
                    ->where('transaction_type', 'ATR')
                    ->where('created_at', '>=', now()->subMinutes(2))
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($ultimaTransaccion && $ultimaTransaccion->response_xml) {
                    $xmlRespuesta = $ultimaTransaccion->response_xml;
                }
            }

            // Crear registro en up_anulaciones
            if ($resultado['success']) {
                UpAnulacion::crearDesdeRespuestaSOAP($params, $resultado);
                
                // Si la anulación fue exitosa y viene de un consumo, actualizar su estado
                if ($resultado['data']['STATUS'] === 'OK' && $request->input('consumo_id')) {
                    $consumo = \App\Models\UpConsumo::find($request->input('consumo_id'));
                    if ($consumo) {
                        $consumo->update([
                            'estado_flujo' => 'anulado',
                            'fecha_anulacion' => now(),
                            'usuario_anulacion' => \CRUDBooster::myName() ?? 'sistema',
                            'observaciones_flujo' => 'Anulado via ATR: ' . $params['motivo']
                        ]);
                    }
                }
            } else {
                // Si falla ATR pero es un consumo pendiente, anular localmente
                if ($request->input('consumo_id') && $request->input('id_anulacion') === 'PENDIENTE') {
                    $consumo = \App\Models\UpConsumo::find($request->input('consumo_id'));
                    if ($consumo && $consumo->estado_calculado === 'pendiente') {
                        $consumo->update([
                            'estado_flujo' => 'anulado',
                            'fecha_anulacion' => now(),
                            'usuario_anulacion' => \CRUDBooster::myName() ?? 'sistema',
                            'observaciones_flujo' => 'Anulado localmente: ' . $params['motivo']
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Consumo pendiente anulado localmente (sin ATR)',
                            'status' => 'OK',
                            'consumo_actualizado' => true,
                            'anulacion_local' => true
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => $resultado['success'],
                'message' => $resultado['message'],
                'status' => $resultado['data']['STATUS'] ?? ($resultado['success'] ? 'OK' : 'ERROR'),
                'idtran' => $resultado['idtran'] ?? null,
                'data' => $resultado['data'] ?? null,
                'xml_request' => $xmlSolicitud,
                'xml_response' => $xmlRespuesta,
                'consumo_actualizado' => $request->input('consumo_id') ? true : false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function construirXmlAnulacion($params)
    {
        $config = config('union_personal');
        $ambiente = $config['ambiente'];
        $ambienteConfig = $config[$ambiente];
        
        $msgId = $params['msgid'];
        $fecha = date('Y-m-d');
        $time = date('Y-m-d\TH:i:s');

        $pidContent = "<TIPOID>CODIGO_AFI</TIPOID><ID>{$params['afiliado_codigo']}</ID>";
        
        if (!empty($params['token'])) {
            $pidContent .= "<TOKEN>{$params['token']}</TOKEN>";
        } else {
            if (!empty($params['plan'])) $pidContent .= "<PLAN>{$params['plan']}</PLAN>";
            if (!empty($params['vercred'])) $pidContent .= "<VERCRED>{$params['vercred']}</VERCRED>";
        }
        
        $pidContent .= "<VERIFID>AUTO</VERIFID>";

        return "
        <SOLICITUD>
            <EMISOR>
                <ID>INTEGRACION-UP</ID>
                <PROT>CA_V20</PROT>
                <TER>01</TER>
                <APP>{$config['app_name']}</APP>
                <MSGID>{$msgId}</MSGID>
                <TIME>{$time}</TIME>
            </EMISOR>
            <SEGURIDAD>
                <TIPOAUT>U</TIPOAUT>
                <TIPOCON>PRES</TIPOCON>
                <USRID>{$ambienteConfig['user_id']}</USRID>
                <USRPASS>{$ambienteConfig['user_pass']}</USRPASS>
            </SEGURIDAD>
            <OPER>
                <TIPO>ATR</TIPO>
                <IDASEG>UP</IDASEG>
                <IDPRESTADOR>{$ambienteConfig['prestador_id']}</IDPRESTADOR>
                <FECHA>{$fecha}</FECHA>
            </OPER>
            <PID>
                {$pidContent}
            </PID>
            <ANULACION>
                <TIPOIDANUL>{$params['tipoidanul']}</TIPOIDANUL>
                <IDANUL>{$params['idanul']}</IDANUL>
                <MOTIVO>{$params['motivo']}</MOTIVO>
            </ANULACION>
        </SOLICITUD>";
    }

    public function getXML($idtran)
    {
        try {
            $transaccion = DB::table('up_transacciones_soap')
                ->where('idtran', $idtran)
                ->where('transaction_type', 'ATR')
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
                    'message' => 'No se encontró información XML para esta anulación'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
