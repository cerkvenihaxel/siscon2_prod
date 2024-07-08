<?php

namespace App\View\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class DispensaChart extends Component
{
    public $labels;
    public $dataset;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($startDate = '2024-01-01', $endDate = '2024-12-31')
    {
        $solicitudes = DB::table('cotizacion_convenio as cc')
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

        $this->labels = $solicitudes->pluck('Nombre_de_Farmacia')->toArray();
        $this->dataset = $solicitudes->pluck('Cantidad_de_Dispensa_Asignada')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dispensa-chart', [
            'labels' => $this->labels,
            'dataset' => $this->dataset
        ]);
    }
}
