<?php
// Navegación corregida para módulos UP
$navigation = [];

$navigation[] = [
    'label' => 'Consumos UP',
    'url' => CRUDBooster::adminPath('up_consumos'),
    'icon' => 'fa fa-list',
    'color' => 'primary'
];

$navigation[] = [
    'label' => 'Elegibilidad (ELG)',
    'url' => CRUDBooster::adminPath('up_elegibilidad'),
    'icon' => 'fa fa-check-circle',
    'color' => 'info'
];

$navigation[] = [
    'label' => 'Autorización Previa (AP)',
    'url' => CRUDBooster::adminPath('up_autorizacion_previa'),
    'icon' => 'fa fa-check',
    'color' => 'success'
];

$navigation[] = [
    'label' => 'Manual de Usuario',
    'url' => CRUDBooster::adminPath('manual-flujo-up'),
    'icon' => 'fa fa-book',
    'color' => 'info'
];

$navigation[] = [
    'label' => 'Configurar Ambiente',
    'url' => CRUDBooster::adminPath('up_ambiente'),
    'icon' => 'fa fa-cogs',
    'color' => config('union_personal.ambiente') == 'test' ? 'success' : 'warning'
];

// ⚠️ IMPORTANTE: Las anulaciones son irreversibles
