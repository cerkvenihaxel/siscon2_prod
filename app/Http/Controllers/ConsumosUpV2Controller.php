<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpConsumo;
use App\Services\UnionPersonalSoapService;
use CRUDBooster;

class ConsumosUpV2Controller extends Controller
{
    private $soapService;

    public function __construct()
    {
        $this->soapService = new UnionPersonalSoapService();
    }

    public function index(Request $request)
    {
        $query = UpConsumo::query();

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
            $query->where('estado_flujo', $request->estado_flujo);
        }
        if ($request->fecha_desde) {
            $query->whereDate('fecha_tran', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_tran', '<=', $request->fecha_hasta);
        }

        // Sorting
        $sortBy = $request->get('sort', 'fecha_tran');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $consumos = $query->paginate(20);

        // Contadores en tiempo real
        $contadores = [
            'pendientes' => UpConsumo::where('estado_flujo', 'pendiente')->count(),
            'aprobados' => UpConsumo::where('estado_flujo', 'aprobado')->count(),
            'entregados' => UpConsumo::where('estado_flujo', 'entregado')->count(),
        ];

        return view('consumos_up_v2.index', compact('consumos', 'contadores'));
    }

    public function show($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            return response()->json($consumo);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Consumo no encontrado'], 404);
        }
    }

    public function consultarElegibilidad($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            if (!in_array($consumo->estado_flujo, ['pendiente', 'elegibilidad_no'])) {
                return redirect()->back()->with('error', 'Estado no válido para elegibilidad');
            }

            // Redirigir a la vista de elegibilidad de CRUDBooster
            return redirect('/admin/up_elegibilidad/add?consumo_id=' . $id);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function actualizarElegibilidad($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            $consumo->update([
                'estado_flujo' => 'elegibilidad_ok',
                'fecha_elegibilidad' => now(),
                'usuario_elegibilidad' => CRUDBooster::myName() ?? 'sistema',
            ]);
            
            return response()->json(['success' => true, 'message' => 'Elegibilidad verificada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function aprobarPrestacion($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            if (!in_array($consumo->estado_flujo, ['elegibilidad_ok', 'pendiente'])) {
                return response()->json(['success' => false, 'message' => 'Estado no válido para aprobación']);
            }

            $consumo->update([
                'estado_flujo' => 'aprobado',
                'idaut' => 'AUTH_' . time(),
                'fecha_aprobacion' => now(),
                'usuario_aprobacion' => CRUDBooster::myName() ?? 'sistema',
            ]);
            
            return response()->json(['success' => true, 'message' => 'Prestación aprobada. IDAUT: ' . $consumo->idaut]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function aprobarConElegibilidad($id)
    {
        try {
            \Log::info('Buscando consumo con ID: ' . $id);
            $consumo = UpConsumo::findOrFail($id);
            \Log::info('Consumo encontrado: ' . $consumo->id);

            $consumo->estado_flujo = 'aprobado';
            $consumo->idaut = 'AUTH_ELG_' . time();
            $consumo->fecha_aprobacion = now();
            $consumo->save();
            
            return response()->json(['success' => true, 'message' => 'Prestación aprobada. IDAUT: ' . $consumo->idaut]);
            
        } catch (\Exception $e) {
            \Log::error('Error en aprobarConElegibilidad: ' . $e->getMessage());
            \Log::error('ID recibido: ' . $id);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function aprobarDirecto($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            if (!in_array($consumo->estado_flujo, ['pendiente', 'elegibilidad_no'])) {
                return response()->json(['success' => false, 'message' => 'Estado no válido para aprobación directa']);
            }

            $consumo->update([
                'estado_flujo' => 'aprobado',
                'idaut' => 'AUTH_DIRECTO_' . time(),
                'fecha_aprobacion' => now(),
                'usuario_aprobacion' => CRUDBooster::myName() ?? 'sistema',
                'observaciones' => 'Aprobación directa sin verificación de elegibilidad'
            ]);
            
            return response()->json(['success' => true, 'message' => 'Prestación aprobada directamente. IDAUT: ' . $consumo->idaut]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function generarValidacion($id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            if ($consumo->estado_flujo !== 'aprobado') {
                return response()->json(['success' => false, 'message' => 'Solo se pueden validar consumos aprobados']);
            }

            // Redirigir a la vista de validación con los datos del consumo
            $url = '/admin/consumos_up_v2/' . $id . '/validacion';
            return response()->json(['success' => true, 'redirect' => $url, 'message' => 'Redirigiendo a validación de entrega...']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function mostrarValidacion($id)
    {
        $consumo = UpConsumo::findOrFail($id);
        
        if ($consumo->estado_flujo !== 'aprobado') {
            return redirect()->back()->with('error', 'Solo se pueden validar consumos aprobados');
        }

        return view('consumos_up_v2.validacion', compact('consumo'));
    }

    public function procesarValidacion($id, Request $request)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            if ($consumo->estado_flujo !== 'aprobado') {
                return redirect()->back()->with('error', 'Solo se pueden validar consumos aprobados');
            }

            // Actualizar el consumo
            $consumo->update([
                'estado_flujo' => 'entregado'
            ]);

            // Crear registro de validación de entrega
            \DB::table('up_validaciones_entrega')->insert([
                'consumo_id' => $consumo->id,
                'codigo_afiliado' => $consumo->afiliado,
                'afiliado_nombre' => $consumo->nombres . ' ' . $consumo->apellidos,
                'medicamento_codigo' => $consumo->cod_prestacion,
                'medicamento_descripcion' => $consumo->desc,
                'cantidad_solicitada' => $consumo->cant,
                'cantidad_entregada' => $request->cantidad_entregada ?? $consumo->cant,
                'fecha_entrega' => $request->fecha_entrega,
                'estado_entrega' => $request->estado_entrega,
                'lugar_entrega' => $request->lugar_entrega,
                'observaciones' => $request->observaciones,
                'usuario_validador' => CRUDBooster::myName() ?? 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Validación de entrega registrada exitosamente']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function mostrarEntregas(Request $request)
    {
        $query = \DB::table('up_validaciones_entrega');

        // Filtros
        if ($request->codigo_afiliado) {
            $query->where('codigo_afiliado', 'like', '%' . $request->codigo_afiliado . '%');
        }
        if ($request->nombre) {
            $query->where('afiliado_nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->estado_entrega) {
            $query->where('estado_entrega', $request->estado_entrega);
        }
        if ($request->fecha_desde) {
            $query->whereDate('fecha_entrega', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_entrega', '<=', $request->fecha_hasta);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $entregas = $query->paginate(20);

        // Contadores
        $contadores = [
            'completas' => \DB::table('up_validaciones_entrega')->where('estado_entrega', 'completa')->count(),
            'parciales' => \DB::table('up_validaciones_entrega')->where('estado_entrega', 'parcial')->count(),
            'no_entregados' => \DB::table('up_validaciones_entrega')->where('estado_entrega', 'no_entregado')->count(),
        ];

        return view('consumos_up_v2.entregas', compact('entregas', 'contadores'));
    }
}
