<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpConsumo;
use CRUDBooster;
use DB;

class ConsumosUpController extends Controller
{
    public function index(Request $request)
    {
        $query = UpConsumo::query();

        // Filtro por usuario: Solo Super Admin y Administrador General ven todos
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $query->where('usuario_aprobacion', CRUDBooster::myName());
        }

        // Filtros
        if ($request->afiliado) {
            $query->where('afiliado', 'like', '%' . $request->afiliado . '%');
        }
        if ($request->nombre) {
            $query->where(function($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidos', 'like', '%' . $request->nombre . '%');
            });
        }
        if ($request->estado_flujo) {
            $query->estadoCalculado($request->estado_flujo);
        }
        if ($request->fecha_desde) {
            $query->whereDate('fecha_tran', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_tran', '<=', $request->fecha_hasta);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'fecha_tran');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $consumos = $query->paginate(20);

        // Contadores con filtro por usuario
        $contadoresQuery = UpConsumo::query();
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $contadoresQuery->where('usuario_aprobacion', CRUDBooster::myName());
        }

        // Contadores con estados calculados
        $contadores = [
            'pendientes' => (clone $contadoresQuery)->estadoCalculado('pendiente')->count(),
            'procesados' => (clone $contadoresQuery)->estadoCalculado('procesado')->count(),
            'remitados' => (clone $contadoresQuery)->estadoCalculado('remitado')->count(),
            'en_transito' => (clone $contadoresQuery)->estadoCalculado('en_transito')->count(),
            'entregados' => (clone $contadoresQuery)->estadoCalculado('entregado')->count(),
            'anulados' => (clone $contadoresQuery)->estadoCalculado('anulado')->count(),
        ];

        return view('consumos_up.index', compact('consumos', 'contadores'));
    }

    public function show(Request $request, $id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);

            // Verificar que el usuario puede ver este consumo
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $consumo->usuario_aprobacion != CRUDBooster::myName()) {
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'No tiene permisos para ver este consumo'], 403);
                }
                return redirect('/admin/consumos-up')->with('error', 'No tiene permisos para ver este consumo');
            }

            // Si es petición AJAX, devolver JSON para el modal
            if ($request->ajax() || $request->wantsJson()) {
                $consumoArray = $consumo->toArray();
                $consumoArray['estado_calculado'] = $consumo->estado_calculado;
                return response()->json($consumoArray);
            }

            // Si es navegación normal, devolver vista HTML
            return view('consumos_up.show', compact('consumo'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Consumo no encontrado'], 404);
            }
            return redirect('/admin/consumos-up')->with('error', 'Consumo no encontrado');
        }
    }

    public function marcarEntregado(Request $request)
    {
        try {
            $consumo = UpConsumo::findOrFail($request->consumo_id);
            
            if ($consumo->estado_flujo !== 'aprobado') {
                return response()->json(['success' => false, 'message' => 'Solo se pueden marcar como entregados los consumos aprobados']);
            }

            // Actualizar el consumo
            $consumo->update([
                'estado_flujo' => 'entregado',
                'fecha_entrega' => $request->fecha_entrega,
                'usuario_entrega' => CRUDBooster::myName() ?? 'sistema'
            ]);

            // Crear registro de validación de entrega
            DB::table('up_validaciones_entrega')->insert([
                'consumo_id' => $consumo->id,
                'codigo_afiliado' => $consumo->afiliado,
                'afiliado_nombre' => $consumo->nombres . ' ' . $consumo->apellidos,
                'medicamento_codigo' => $consumo->cod_prestacion,
                'medicamento_descripcion' => $consumo->desc,
                'cantidad_solicitada' => $consumo->cant,
                'cantidad_entregada' => $request->cantidad_entregada ?? $consumo->cant,
                'fecha_entrega' => $request->fecha_entrega,
                'estado_entrega' => $request->estado_entrega,
                'observaciones' => $request->observaciones,
                'usuario_validador' => CRUDBooster::myName() ?? 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Entrega registrada exitosamente']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function anular(Request $request)
    {
        try {
            $consumo = UpConsumo::findOrFail($request->consumo_id);
            
            // Verificar que el usuario puede anular este consumo
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $consumo->usuario_aprobacion != CRUDBooster::myName()) {
                return response()->json(['success' => false, 'message' => 'No tiene permisos para anular este consumo']);
            }
            
            // No permitir anular consumos remitados o en tránsito
            if (in_array($consumo->estado_calculado, ['remitado', 'en_transito', 'entregado', 'anulado'])) {
                return response()->json(['success' => false, 'message' => 'No se puede anular un consumo en estado ' . strtoupper($consumo->estado_calculado)]);
            }

            // Todos los consumos van a la vista de anulación ATR
            $params = [
                'afiliado' => $consumo->afiliado,
                'tipo_anulacion' => 'IDTRAN',
                'id_anulacion' => $consumo->idtran_aprobacion ?: 'PENDIENTE',
                'motivo' => $request->motivo_anulacion ?? 'Anulación desde Consumos UP - ' . $consumo->desc,
                'plan' => $consumo->modelo_plan,
                'consumo_id' => $consumo->id
            ];

            $queryString = http_build_query($params);
            return response()->json([
                'success' => true, 
                'redirect' => '/admin/anulacion-up?' . $queryString,
                'message' => 'Redirigiendo a anulación ATR...'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function imprimir($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            return view('consumos_up.imprimir', compact('consumo'));
        } catch (\Exception $e) {
            return redirect('/admin/consumos-up')->with('error', 'Consumo no encontrado');
        }
    }

    public function getXML($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);

            if (!$consumo->num_tran && !$consumo->idtran_aprobacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta transacción no tiene XML disponible'
                ]);
            }

            // Buscar XML en la tabla de transacciones SOAP UP
            $idtran = $consumo->num_tran ?? $consumo->idtran_aprobacion;
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

    private function construirXmlAnulacion($consumo, $request)
    {
        $config = config('union_personal');
        $ambiente = $config['ambiente'];
        $ambienteConfig = $config[$ambiente];
        
        $msgId = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $fecha = date('Y-m-d');
        $time = date('Y-m-d\TH:i:s');

        return "
        <SOLICITUD>
            <EMISOR>
                <ID>INTEGRACION-UP</ID>
                <PROT>CA_V20</PROT>
                <MSGID>{$msgId}</MSGID>
                <TER>01</TER>
                <APP>HMS_CAWEB</APP>
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
                <TIPOIDANUL>IDTRAN</TIPOIDANUL>
                <IDANUL>{$consumo->idtran}</IDANUL>
                <FECHA>{$fecha}</FECHA>
                <MOTIVO><![CDATA[{$request->observaciones}]]></MOTIVO>
            </OPER>
            <PID>
                <TIPOID>CODIGO</TIPOID>
                <ID>{$consumo->afiliado}</ID>
                <TOKEN>9999</TOKEN>
                <VERIFID>AUTO</VERIFID>
            </PID>
        </SOLICITUD>";
    }
}
