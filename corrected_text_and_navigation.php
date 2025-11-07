<?php
// Texto corregido con formato apropiado
$infoText = "
👥 Afiliados de Prueba:
• 54715500 - Plan 150 (Accord) - VerCred: 45
• 54715300 - Plan 2 - VerCred: 31

🔑 TOKEN: 9999 (desarrollo)

🏥 Prestaciones de Prueba:
• 1420107 - Consulta Especializada
• 1420101 - Consulta Médica

📍 Contextos: A=Ambulatorio, I=Internado, U=Urgencia
";

// Navegación corregida
$navigation = array();

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
    'label' => 'Anulaciones (ATR)',
    'url' => CRUDBooster::adminPath('up_anulaciones'),
    'icon' => 'fa fa-times-circle',
    'color' => 'danger'
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
