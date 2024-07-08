<?php

namespace App\View\Components;

use App\Models\Patologias;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;


class BarChart extends Component
{
    public $labels;
    public $dataset;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($startDate = '2022-01-01', $endDate = '2029-12-31')
    {
        $solicitudesPorPatologia = Patologias::select('nombre', DB::raw('count(*) as total'))
            ->join('pedido_medicamento', 'patologias.id', '=', 'pedido_medicamento.patologia')
            ->whereBetween('pedido_medicamento.created_at', [$startDate, $endDate])
            ->groupBy('nombre')
            ->get();

        $this->labels = $solicitudesPorPatologia->pluck('nombre')->toArray();
        $this->dataset = $solicitudesPorPatologia->pluck('total')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bar-chart');
    }
}
