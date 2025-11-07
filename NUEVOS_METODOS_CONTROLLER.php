<?php

// NUEVOS MÉTODOS PARA AdminUpConsumosController
// Estos métodos deben agregarse al controlador existente

/**
 * Método mejorado: Consultar Elegibilidad (reemplaza getVerificarElegibilidad)
 */
public function getConsultarElegibilidad($id)
{
    if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
        CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
    }

    $consumo = UpConsumo::findOrFail($id);

    if (!$consumo->puedeConsultarElegibilidad()) {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Este consumo no puede consultar elegibilidad en su estado actual', 'warning');
        return;
    }

    $startTime = microtime(true);

    // Preparar params para ELG
    $params = [
        'start_time' => $startTime,
        'msgid' => 'SISCON2_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
        'afiliado_codigo' => $consumo->afiliado,
        'plan' => $consumo->modelo_plan,
        'vercred' => null,
        'prestador_id' => $consumo->cod_prestador ?? config('union_personal.prestador_id'),
        'usrid' => config('union_personal.user_id'),
        'usrpass' => config('union_personal.user_pass'),
        'verifid' => 'FARMACIA',
        'prestaciones' => [[
            'tipo' => $consumo->tipo_pres,
            'id' => $consumo->cod_prestacion,
            'cant' => $consumo->cant,
        ]],
    ];

    // Ejecutar ELG
    $resultado = $this->soapService->ejecutarELG($params);

    // Crear registro de elegibilidad
    $elegibilidad = UpElegibilidad::crearDesdeRespuestaSOAP($params, $resultado);

    if ($resultado['success']) {
        // Actualizar consumo
        $consumo->update([
            'estado_flujo' => 'elegibilidad_ok',
            'elegibilidad_id' => $elegibilidad->id,
            'idtran_elegibilidad' => $resultado['idtran'],
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => CRUDBooster::myEmail(),
        ]);

        CRUDBooster::redirect(CRUDBooster::mainpath(), 
            'Elegibilidad confirmada exitosamente. ID TRAN: ' . $resultado['idtran'] . '. El afiliado puede recibir el medicamento. Proceda a Aprobar la Prestación.', 
            'success');
    } else {
        $consumo->update([
            'estado_flujo' => 'elegibilidad_no',
            'elegibilidad_id' => $elegibilidad->id,
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => CRUDBooster::myEmail(),
        ]);
        CRUDBooster::redirect(CRUDBooster::mainpath(), 
            'Elegibilidad rechazada: ' . $resultado['message'] . '. Verifique los datos del afiliado y el medicamento.', 
            'danger');
    }
}

/**
 * Método nuevo: Generar Validación de Entrega
 */
public function getGenerarValidacionEntrega($id)
{
    if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
        CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
    }

    $consumo = UpConsumo::with(['autorizacion', 'afiliadoUp'])->findOrFail($id);

    if (!$consumo->puedeGenerarValidacionEntrega()) {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 
            'Este consumo debe estar aprobado y tener número de autorización para generar validación de entrega', 
            'warning');
        return;
    }

    $data = [];
    $data['page_title'] = 'Generar Validación de Entrega - ' . $consumo->desc;
    $data['consumo'] = $consumo;
    $data['afiliado'] = $consumo->afiliadoUp;
    $data['autorizacion'] = $consumo->autorizacion;

    return view('up_consumos.generar_validacion_entrega', $data);
}

/**
 * POST: Procesar validación de entrega
 */
public function postGenerarValidacionEntrega()
{
    $request = Request::instance();
    $id = $request->input('consumo_id');
    
    $consumo = UpConsumo::findOrFail($id);

    if (!$consumo->puedeGenerarValidacionEntrega()) {
        return response()->json([
            'success' => false,
            'message' => 'Este consumo no puede generar validación de entrega en su estado actual'
        ], 400);
    }

    try {
        // Datos adicionales para la validación
        $datosValidacion = [
            'farmacia_codigo' => $request->input('farmacia_codigo'),
            'farmacia_nombre' => $request->input('farmacia_nombre'),
            'farmacia_direccion' => $request->input('farmacia_direccion'),
            'numero_receta' => $request->input('numero_receta'),
            'medico_prescriptor' => $request->input('medico_prescriptor'),
            'observaciones' => $request->input('observaciones'),
            'lote_medicamento' => $request->input('lote_medicamento'),
            'fecha_vencimiento' => $request->input('fecha_vencimiento'),
            'laboratorio' => $request->input('laboratorio'),
            'entrega_completa' => $request->input('entrega_completa', true),
            'cantidad_entregada' => $request->input('cantidad_entregada', $consumo->cant),
        ];

        // Generar validación de entrega
        $validacion = $consumo->generarValidacionEntrega($datosValidacion);

        // Actualizar datos adicionales del consumo
        $consumo->update([
            'farmacia_codigo' => $request->input('farmacia_codigo'),
            'farmacia_nombre' => $request->input('farmacia_nombre'),
            'numero_receta' => $request->input('numero_receta'),
            'notas_farmacia' => $request->input('observaciones'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Validación de entrega generada exitosamente. Número de comprobante: ' . $validacion->generarNumeroComprobante(),
            'validacion_id' => $validacion->id,
            'numero_comprobante' => $validacion->generarNumeroComprobante(),
            'redirect' => CRUDBooster::mainpath()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al generar validación de entrega: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Método nuevo: Verificar Stock
 */
public function getVerificarStock($id)
{
    if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
        CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
    }

    $consumo = UpConsumo::findOrFail($id);

    if (!$consumo->puedeVerificarStock()) {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 
            'Solo se puede verificar stock en consumos con elegibilidad confirmada o aprobados', 
            'warning');
        return;
    }

    $data = [];
    $data['page_title'] = 'Verificar Stock - ' . $consumo->desc;
    $data['consumo'] = $consumo;

    return view('up_consumos.verificar_stock', $data);
}

/**
 * POST: Actualizar estado de stock
 */
public function postVerificarStock()
{
    $request = Request::instance();
    $id = $request->input('consumo_id');
    $stockDisponible = $request->input('stock_disponible', false);
    $observaciones = $request->input('observaciones', '');

    $consumo = UpConsumo::findOrFail($id);

    $consumo->marcarStockVerificado($stockDisponible, CRUDBooster::myEmail());

    if ($observaciones) {
        $consumo->update([
            'notas_farmacia' => $observaciones,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => $stockDisponible ? 
            'Stock confirmado como disponible' : 
            'Stock marcado como no disponible',
        'stock_disponible' => $stockDisponible,
        'redirect' => CRUDBooster::mainpath()
    ]);
}

/**
 * Método nuevo: Dashboard de Farmacia
 */
public function getDashboardFarmacia()
{
    $data = [];
    $data['page_title'] = 'Dashboard - Farmacia UP';
    
    // Estadísticas del día
    $hoy = today();
    $data['stats_hoy'] = [
        'consumos_pendientes' => UpConsumo::whereDate('fecha_tran', $hoy)->pendientes()->count(),
        'elegibilidades_ok' => UpConsumo::whereDate('fecha_elegibilidad', $hoy)->where('estado_flujo', 'elegibilidad_ok')->count(),
        'aprobaciones' => UpConsumo::whereDate('fecha_aprobacion', $hoy)->where('estado_flujo', 'aprobado')->count(),
        'entregas' => UpConsumo::whereDate('fecha_validacion_entrega', $hoy)->entregados()->count(),
    ];
    
    // Estadísticas del mes
    $mesActual = now()->startOfMonth();
    $data['stats_mes'] = UpConsumo::estadisticasGenerales($mesActual, now());
    
    // Consumos pendientes de procesar
    $data['consumos_pendientes'] = UpConsumo::with(['afiliadoUp'])
        ->listosParaElegibilidad()
        ->orderByDesc('fecha_tran')
        ->limit(10)
        ->get();
    
    // Consumos listos para entrega
    $data['listos_entrega'] = UpConsumo::with(['afiliadoUp'])
        ->listosParaEntrega()
        ->orderByDesc('fecha_aprobacion')
        ->limit(10)
        ->get();
    
    // Estadísticas de validaciones de entrega
    $data['stats_entregas'] = UpValidacionEntrega::estadisticas($mesActual, now());
    
    return view('up_consumos.dashboard_farmacia', $data);
}

/**
 * Método nuevo: Buscar Consumos por Afiliado
 */
public function getBuscarPorAfiliado($codigoAfiliado = null)
{
    if (!$codigoAfiliado) {
        $data = [];
        $data['page_title'] = 'Buscar Consumos por Afiliado';
        return view('up_consumos.buscar_afiliado', $data);
    }

    $data = [];
    $data['page_title'] = 'Consumos del Afiliado: ' . $codigoAfiliado;
    $data['codigo_afiliado'] = $codigoAfiliado;
    
    // Buscar afiliado en tabla local
    $data['afiliado'] = AfiliadoConvenioUp::where('codigo_afiliado', $codigoAfiliado)->first();
    
    // Obtener historial de consumos
    $data['consumos'] = UpConsumo::historialAfiliado($codigoAfiliado, 100);
    
    // Estadísticas del afiliado
    $data['estadisticas'] = [
        'total_consumos' => $data['consumos']->count(),
        'pendientes' => $data['consumos']->where('estado_flujo', 'pendiente')->count(),
        'entregados' => $data['consumos']->where('estado_flujo', 'entregado')->count(),
        'importe_total' => $data['consumos']->sum('imptot'),
        'ultimo_consumo' => $data['consumos']->first()?->fecha_tran,
    ];
    
    return view('up_consumos.historial_afiliado', $data);
}

/**
 * Método nuevo: Ver Comprobante de Validación
 */
public function getComprobanteValidacion($validacionId)
{
    $validacion = UpValidacionEntrega::with(['consumo', 'afiliado'])->findOrFail($validacionId);
    
    $data = [];
    $data['page_title'] = 'Comprobante de Validación de Entrega';
    $data['validacion'] = $validacion;
    $data['consumo'] = $validacion->consumo;
    
    return view('up_consumos.comprobante_validacion', $data);
}

/**
 * Método nuevo: Exportar Validaciones
 */
public function getExportarValidaciones()
{
    $request = Request::instance();
    
    $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
    $fechaHasta = $request->input('fecha_hasta', now()->format('Y-m-d'));
    $farmacia = $request->input('farmacia_codigo');
    
    $query = UpValidacionEntrega::with(['consumo'])
        ->fechaEntre($fechaDesde, $fechaHasta);
    
    if ($farmacia) {
        $query->farmacia($farmacia);
    }
    
    $validaciones = $query->orderByDesc('fecha_entrega')->get();
    
    $filename = 'validaciones_entrega_' . $fechaDesde . '_' . $fechaHasta . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    $callback = function() use ($validaciones) {
        $file = fopen('php://output', 'w');
        
        // Encabezados CSV
        fputcsv($file, [
            'ID Validación',
            'Fecha Entrega',
            'Código Afiliado',
            'Nombre Afiliado',
            'Código Medicamento',
            'Descripción Medicamento',
            'Cantidad',
            'Importe',
            'IDAUT',
            'Farmacia',
            'Usuario Entrega',
            'Observaciones'
        ]);
        
        // Datos
        foreach ($validaciones as $validacion) {
            fputcsv($file, [
                $validacion->id,
                $validacion->fecha_entrega->format('d/m/Y H:i'),
                $validacion->afiliado_codigo,
                $validacion->nombre_completo,
                $validacion->medicamento_codigo,
                $validacion->medicamento_descripcion,
                $validacion->cantidad,
                $validacion->importe_autorizado,
                $validacion->idaut,
                $validacion->farmacia_nombre,
                $validacion->usuario_entrega,
                $validacion->observaciones,
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}
