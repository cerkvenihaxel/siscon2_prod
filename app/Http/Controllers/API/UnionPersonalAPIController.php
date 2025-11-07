<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UnionPersonalSoapService;
use App\Models\UpSolicitud;
use App\Models\UpSolicitudItem;
use App\Models\AfiliadoConvenioUp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UnionPersonalAPIController extends Controller
{
    protected $soapService;

    public function __construct()
    {
        $this->soapService = new UnionPersonalSoapService();
    }

    /**
     * Test de conexión SOAP
     * GET /api/union-personal/test
     */
    public function test()
    {
        try {
            $resultado = $this->soapService->testWs();
            return response()->json($resultado, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en test de conexión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar elegibilidad (ELG)
     * POST /api/union-personal/elegibilidad
     *
     * 🌟 Autocrea el afiliado si no existe
     */
    public function elegibilidad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'afiliado_codigo' => 'required|string|max:20',
            'prestaciones' => 'nullable|array',
            'prestaciones.*.tipo' => 'required_with:prestaciones|in:P,M,D',
            'prestaciones.*.id' => 'required_with:prestaciones|string',
            'prestaciones.*.cant' => 'required_with:prestaciones|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $resultado = $this->soapService->ejecutarELG($request->all());

            // Si fue exitoso, el afiliado ya fue autocreado por el servicio SOAP 🌟
            if ($resultado['success']) {
                $afiliado = AfiliadoConvenioUp::where('codigo_afiliado', $request->afiliado_codigo)->first();
                if ($afiliado) {
                    $resultado['afiliado_creado'] = true;
                    $resultado['afiliado_nombre'] = $afiliado->nombre_completo;
                }
            }

            return response()->json($resultado, $resultado['success'] ? 200 : 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ejecutando ELG',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Autorizar prestación (AP)
     * POST /api/union-personal/autorizar-prestacion
     */
    public function autorizarPrestacion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'afiliado_codigo' => 'required|string|max:20',
            'prestaciones' => 'required|array|min:1',
            'prestaciones.*.tipo' => 'required|in:P,M,D',
            'prestaciones.*.id' => 'required|string',
            'prestaciones.*.cant' => 'required|integer|min:1',
            'contexto_tipo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $resultado = $this->soapService->ejecutarAP($request->all());
            return response()->json($resultado, $resultado['success'] ? 200 : 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ejecutando AP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Anular transacción (ATR)
     * POST /api/union-personal/anular-transaccion
     */
    public function anularTransaccion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'afiliado_codigo' => 'required|string|max:20',
            'tipoidanul' => 'required|in:IDTRAN,MSGID',
            'idanul' => 'required|string',
            'motivo' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $resultado = $this->soapService->ejecutarATR($request->all());
            return response()->json($resultado, $resultado['success'] ? 200 : 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ejecutando ATR',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar afiliado (desde BD local)
     * GET /api/union-personal/buscar-afiliado/{codigo}
     */
    public function buscarAfiliado($codigo)
    {
        try {
            $afiliado = AfiliadoConvenioUp::where('codigo_afiliado', $codigo)->first();

            if (!$afiliado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Afiliado no encontrado en base local. Use ELG para consultar y autocrear.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $afiliado,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error buscando afiliado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Flujo completo: Elegibilidad + Aprobación
     * POST /api/union-personal/flujo-completo
     */
    public function flujoCompleto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'afiliado_codigo' => 'required|string|max:20',
            'prestador_id' => 'nullable|string',
            'prestaciones' => 'required|array|min:1',
            'prestaciones.*.tipo' => 'required|in:P,M,D',
            'prestaciones.*.id' => 'required|string',
            'prestaciones.*.cant' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Crear solicitud
            $solicitud = UpSolicitud::create([
                'nro_solicitud' => UpSolicitud::generarNroSolicitud(),
                'codigo_afiliado' => $request->afiliado_codigo,
                'prestador_id' => $request->prestador_id,
                'estado' => 'borrador',
                'fecha_solicitud' => now(),
            ]);

            // 2. Agregar ítems
            foreach ($request->prestaciones as $prest) {
                UpSolicitudItem::create([
                    'solicitud_id' => $solicitud->id,
                    'tipo_prestacion' => $prest['tipo'],
                    'cod_prestacion' => $prest['id'],
                    'cantidad' => $prest['cant'],
                    'troquel' => $prest['troquel'] ?? null,
                    'cod_barra' => $prest['codbarra'] ?? null,
                ]);
            }

            // 3. Ejecutar ELG (autocrea afiliado 🌟)
            $resultadoELG = $this->soapService->ejecutarELG(array_merge(
                $request->all(),
                ['solicitud_id' => $solicitud->id]
            ));

            if (!$resultadoELG['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Elegibilidad rechazada',
                    'data' => $resultadoELG,
                ], 400);
            }

            // Actualizar solicitud con datos ELG
            $solicitud->update([
                'estado' => 'elegibilidad_ok',
                'msgid_elegibilidad' => $resultadoELG['data']['MSGID'] ?? null,
                'idtran_elegibilidad' => $resultadoELG['idtran'],
            ]);

            // 4. Ejecutar AP
            $resultadoAP = $this->soapService->ejecutarAP(array_merge(
                $request->all(),
                ['solicitud_id' => $solicitud->id]
            ));

            if (!$resultadoAP['success']) {
                $solicitud->update(['estado' => 'rechazada']);
                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Aprobación rechazada',
                    'elegibilidad' => $resultadoELG,
                    'aprobacion' => $resultadoAP,
                ], 400);
            }

            // Actualizar solicitud con datos AP
            $solicitud->update([
                'estado' => 'aprobada',
                'msgid_aprobacion' => $resultadoAP['data']['MSGID'] ?? null,
                'idtran_aprobacion' => $resultadoAP['idtran'],
                'idaut' => $resultadoAP['idaut'],
            ]);

            // Actualizar ítems con respuestas de prestaciones
            if (isset($resultadoAP['data']['PR'])) {
                $items = $solicitud->items;
                foreach ($resultadoAP['data']['PR'] as $index => $prData) {
                    if (isset($items[$index])) {
                        $items[$index]->actualizarDesdeRespuestaSOAP($prData);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud aprobada exitosamente',
                'solicitud' => $solicitud->fresh(['items', 'afiliado']),
                'elegibilidad' => $resultadoELG,
                'aprobacion' => $resultadoAP,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error en flujo completo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
