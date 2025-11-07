<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UpConsumo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UpConsumosApiController extends Controller
{
    /**
     * Get consumos with date filtering
     */
    public function index(Request $request)
    {
        try {
            $query = UpConsumo::query();

            // Date filtering
            if ($request->has('fecha_desde')) {
                $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
                $query->where('created_at', '>=', $fechaDesde);
            }

            if ($request->has('fecha_hasta')) {
                $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
                $query->where('created_at', '<=', $fechaHasta);
            }

            // Additional filters
            if ($request->has('estado_flujo')) {
                $query->where('estado_flujo', $request->estado_flujo);
            }

            if ($request->has('codigo_afiliado')) {
                $query->where('codigo_afiliado', 'like', '%' . $request->codigo_afiliado . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 50);
            $consumos = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $consumos->items(),
                'pagination' => [
                    'current_page' => $consumos->currentPage(),
                    'last_page' => $consumos->lastPage(),
                    'per_page' => $consumos->perPage(),
                    'total' => $consumos->total(),
                ],
                'filters_applied' => [
                    'fecha_desde' => $request->fecha_desde,
                    'fecha_hasta' => $request->fecha_hasta,
                    'estado_flujo' => $request->estado_flujo,
                    'codigo_afiliado' => $request->codigo_afiliado,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener consumos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single consumo by ID
     */
    public function show($id)
    {
        try {
            $consumo = UpConsumo::with(['elegibilidad', 'autorizacion', 'anulacion'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $consumo,
                'workflow_status' => [
                    'puede_elg' => $consumo->puedeEjecutarELG(),
                    'puede_ap' => $consumo->puedeEjecutarAP(),
                    'puede_entrega' => $consumo->puedeValidarEntrega(),
                    'puede_anular' => in_array($consumo->estado_flujo, ['aprobado', 'entregado'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Consumo no encontrado: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update consumo observations
     */
    public function updateObservations(Request $request, $id)
    {
        try {
            $consumo = UpConsumo::findOrFail($id);
            
            $consumo->update([
                'observaciones_flujo' => $request->input('observaciones', ''),
                'usuario_observaciones' => $request->input('usuario', 'API'),
                'fecha_observaciones' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Observaciones actualizadas correctamente',
                'data' => $consumo->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar observaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow statistics
     */
    public function stats(Request $request)
    {
        try {
            $query = UpConsumo::query();

            // Apply date filters if provided
            if ($request->has('fecha_desde')) {
                $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
                $query->where('created_at', '>=', $fechaDesde);
            }

            if ($request->has('fecha_hasta')) {
                $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
                $query->where('created_at', '<=', $fechaHasta);
            }

            $stats = [
                'total' => $query->count(),
                'por_estado' => $query->groupBy('estado_flujo')
                    ->selectRaw('estado_flujo, count(*) as total')
                    ->pluck('total', 'estado_flujo'),
                'entregados_hoy' => UpConsumo::whereDate('fecha_entrega', today())->count(),
                'pendientes' => UpConsumo::where('estado_flujo', 'pendiente')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
