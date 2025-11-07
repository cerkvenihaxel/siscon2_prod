<?php

// BOTONES DE ACCIÓN ACTUALIZADOS PARA AdminUpConsumosController
// Reemplazar la sección $this->addaction en el método cbInit()

$this->addaction = array();

// Botón: Consultar Elegibilidad (ELG)
$this->addaction[] = [
    'label' => 'Consultar Elegibilidad', 
    'url' => CRUDBooster::mainpath('consultar-elegibilidad/[id]'),
    'icon' => 'fa fa-user-check',
    'color' => 'info',
    'showIf' => "[estado_flujo] == 'pendiente' || [estado_flujo] == 'elegibilidad_no'",
    'confirmation' => true
];

// Botón: Aprobar Prestación (AP)
$this->addaction[] = [
    'label' => 'Aprobar Prestación', 
    'url' => CRUDBooster::mainpath('aprobar-prestacion/[id]'),
    'icon' => 'fa fa-check-circle',
    'color' => 'success',
    'showIf' => "[estado_flujo] == 'elegibilidad_ok'",
    'confirmation' => true
];

// Botón: Generar Validación de Entrega
$this->addaction[] = [
    'label' => 'Generar Validación de Entrega', 
    'url' => CRUDBooster::mainpath('generar-validacion-entrega/[id]'),
    'icon' => 'fa fa-clipboard-check',
    'color' => 'primary',
    'showIf' => "[estado_flujo] == 'aprobado'"
];

// Botón: Ver Historial SOAP
$this->addaction[] = [
    'label' => 'Ver Historial SOAP',
    'url' => CRUDBooster::mainpath('ver-historial-soap/[id]'),
    'icon' => 'fa fa-history',
    'color' => 'default'
];

// Botón: Anular Transacción (ATR)
$this->addaction[] = [
    'label' => 'Anular Transacción', 
    'url' => CRUDBooster::mainpath('anular-transaccion/[id]'),
    'icon' => 'fa fa-times-circle',
    'color' => 'danger',
    'showIf' => "[estado_flujo] == 'aprobado' || [estado_flujo] == 'entregado'",
    'confirmation' => true
];

// MÉTODOS PARA AGREGAR AL CONTROLADOR

/**
 * Consultar Elegibilidad (ELG)
 */
public function getConsultarElegibilidad($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if (!$consumo->puedeConsultarElegibilidad()) {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Este consumo no puede consultar elegibilidad', 'warning');
    }

    $params = [
        'afiliado_codigo' => $consumo->afiliado,
        'plan' => $consumo->modelo_plan,
        'prestaciones' => [[
            'tipo' => $consumo->tipo_pres,
            'id' => $consumo->cod_prestacion,
            'cant' => $consumo->cant,
        ]],
    ];

    $resultado = $this->soapService->ejecutarELG($params);
    
    if ($resultado['success']) {
        $consumo->update([
            'estado_flujo' => 'elegibilidad_ok',
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => CRUDBooster::myEmail(),
        ]);
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad confirmada. Puede aprobar la prestación.', 'success');
    } else {
        $consumo->update(['estado_flujo' => 'elegibilidad_no']);
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad rechazada: ' . $resultado['message'], 'danger');
    }
}

/**
 * Generar Validación de Entrega
 */
public function getGenerarValidacionEntrega($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if ($consumo->estado_flujo !== 'aprobado') {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Solo se pueden generar validaciones para consumos aprobados', 'warning');
    }

    // Crear validación de entrega
    $validacion = UpValidacionEntrega::create([
        'consumo_id' => $consumo->id,
        'afiliado_codigo' => $consumo->afiliado,
        'afiliado_nombre' => $consumo->nombres,
        'afiliado_apellido' => $consumo->apellidos,
        'medicamento_codigo' => $consumo->cod_prestacion,
        'medicamento_descripcion' => $consumo->desc,
        'cantidad' => $consumo->cant,
        'importe_autorizado' => $consumo->imptot,
        'idaut' => $consumo->idaut,
        'fecha_entrega' => now(),
        'usuario_entrega' => CRUDBooster::myEmail(),
        'entrega_completa' => true,
    ]);

    $consumo->update([
        'estado_flujo' => 'entregado',
        'validacion_entrega_id' => $validacion->id,
        'fecha_validacion_entrega' => now(),
        'usuario_validacion_entrega' => CRUDBooster::myEmail(),
    ]);

    CRUDBooster::redirect(CRUDBooster::mainpath(), 'Validación de entrega generada exitosamente', 'success');
}
