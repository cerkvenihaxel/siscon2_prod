<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpAnulacion;
use CRUDBooster;
use DB;

class AnulacionesUpController extends Controller
{
    public function index(Request $request)
    {
        $query = UpAnulacion::orderBy('created_at', 'desc');

        // Filtro por usuario: Solo Super Admin y Administrador General ven todas
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $query->where('usuario_creador', CRUDBooster::myName());
        }

        // Filtros
        if ($request->codigo_afiliado) {
            $query->where('codigo_afiliado', 'like', '%' . $request->codigo_afiliado . '%');
        }
        if ($request->afi_nombre) {
            $query->where('afi_nombre', 'like', '%' . $request->afi_nombre . '%')
                  ->orWhere('afi_apellido', 'like', '%' . $request->afi_nombre . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->tipoidanul) {
            $query->where('tipoidanul', $request->tipoidanul);
        }
        if ($request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $anulaciones = $query->paginate(20);

        // Contadores con filtro por usuario
        $contadoresQuery = UpAnulacion::query();
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $contadoresQuery->where('usuario_creador', CRUDBooster::myName());
        }

        // Contadores
        $contadores = [
            'total' => (clone $contadoresQuery)->count(),
            'exitosas' => (clone $contadoresQuery)->where('status', 'OK')->count(),
            'fallidas' => (clone $contadoresQuery)->whereIn('status', ['NO', 'ERROR'])->count(),
            'pendientes' => (clone $contadoresQuery)->where('status', 'PEND')->count(),
        ];

        return view('anulaciones_up.index', compact('anulaciones', 'contadores'));
    }

    public function detalle($id)
    {
        try {
            $anulacion = UpAnulacion::findOrFail($id);
            
            // Verificar que el usuario puede ver esta anulación
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $anulacion->usuario_creador != CRUDBooster::myName()) {
                return redirect('/admin/anulaciones-up')->with('error', 'No tiene permisos para ver esta anulación');
            }
            
            return view('anulaciones_up.detalle', compact('anulacion'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Anulación no encontrada');
        }
    }

    public function imprimir($id)
    {
        try {
            $anulacion = UpAnulacion::findOrFail($id);
            
            // Verificar que el usuario puede imprimir esta anulación
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $anulacion->usuario_creador != CRUDBooster::myName()) {
                return redirect('/admin/anulaciones-up')->with('error', 'No tiene permisos para imprimir esta anulación');
            }
            
            return view('anulaciones_up.imprimir', compact('anulacion'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Anulación no encontrada');
        }
    }

    public function verXml($id)
    {
        try {
            $anulacion = UpAnulacion::findOrFail($id);
            
            // Verificar que el usuario puede ver el XML de esta anulación
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $anulacion->usuario_creador != CRUDBooster::myName()) {
                return redirect('/admin/anulaciones-up')->with('error', 'No tiene permisos para ver el XML de esta anulación');
            }
            
            // Buscar XML en transacciones SOAP por diferentes criterios
            $transaccion = DB::table('up_transacciones_soap')
                ->where(function($query) use ($anulacion) {
                    // Buscar por IDTRAN si existe
                    if ($anulacion->idtran) {
                        $query->where('idtran', $anulacion->idtran);
                    }
                    // O buscar por MSGID
                    if ($anulacion->msgid) {
                        $query->orWhere('msgid', $anulacion->msgid);
                    }
                })
                ->where('transaction_type', 'ATR')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Si no encuentra, buscar por fecha cercana y afiliado
            if (!$transaccion) {
                $transaccion = DB::table('up_transacciones_soap')
                    ->where('transaction_type', 'ATR')
                    ->where('created_at', '>=', $anulacion->created_at->subMinutes(5))
                    ->where('created_at', '<=', $anulacion->created_at->addMinutes(5))
                    ->whereRaw('request_xml LIKE ?', ['%' . $anulacion->codigo_afiliado . '%'])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
            
            $xmlData = [
                'request' => $transaccion->request_xml ?? 'No disponible',
                'response' => $transaccion->response_xml ?? 'No disponible'
            ];

            return view('anulaciones_up.ver_xml', compact('anulacion', 'xmlData'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar XML: ' . $e->getMessage());
        }
    }
}
