<?php

namespace App\View\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class ValidationChart extends Component
{
    public $labels;
    public $datasetAssignated;
    public $datasetValidate;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($startDate = '2024-01-01', $endDate = '2024-12-31')
    {
        // Obtener solicitudes asignadas
        $solicitudesAsignadas = DB::table('cotizacion_convenio as cc')
            ->select('pr.nombre as Nombre_de_Farmacia', DB::raw('COUNT(cc.punto_retiro_id) as Cantidad_de_Dispensa_Asignada'))
            ->leftJoin('punto_retiro as pr', 'cc.punto_retiro_id', '=', 'pr.id')
            ->where(function($query) {
                $query->whereBetween('cc.punto_retiro_id', [3, 12])
                    ->orWhereBetween('cc.punto_retiro_id', [14, 16]);
            })
            ->whereBetween('cc.created_at', [$startDate, $endDate])
            ->groupBy('cc.punto_retiro_id', 'pr.nombre')
            ->orderBy('cc.punto_retiro_id')
            ->get();

        // Obtener solicitudes validadas
        $solicitudesValidadas = DB::table('cotizacion_convenio as cc')
            ->select('pr.nombre as Nombre_de_Farmacia', DB::raw('COUNT(cc.punto_retiro_id) as Validaciones'))
            ->leftJoin('punto_retiro as pr', 'cc.punto_retiro_id', '=', 'pr.id')
            ->where(function($query) {
                $query->whereBetween('cc.punto_retiro_id', [3, 12])
                    ->orWhereBetween('cc.punto_retiro_id', [14, 16]);
            })
            ->where('cc.estado_solicitud_id', 13)
            ->whereBetween('cc.created_at', [$startDate, $endDate])
            ->groupBy('cc.punto_retiro_id', 'pr.nombre')
            ->orderBy('cc.punto_retiro_id')
            ->get();

        // Extraer las etiquetas y los datos de los resultados
        $this->labels = $solicitudesAsignadas->pluck('Nombre_de_Farmacia')->toArray();
        $this->datasetAssignated = $solicitudesAsignadas->pluck('Cantidad_de_Dispensa_Asignada')->map(fn($value) => $value * -1)->toArray();
        $this->datasetValidate = $solicitudesValidadas->pluck('Validaciones')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.validation-chart');
    }
}
