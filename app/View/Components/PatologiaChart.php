<?php

namespace App\View\Components;

use App\Models\AfiliadosArticulos;
use App\Models\Patologias;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class PatologiaChart extends Component
{
    public $labels;
    public $dataset;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {

        $afiliadosPatologia = Patologias::select('patologias.nombre',
            DB::raw('count(distinct afiliados_articulos.nro_afiliado) as total'))
            ->join('afiliados_articulos', 'patologias.id', '=', 'afiliados_articulos.patologias')
            ->groupBy('patologias.nombre')
            ->get();

        $this->labels = $afiliadosPatologia->pluck('nombre')->toArray();
        $this->dataset = $afiliadosPatologia->pluck('total')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.patologia-chart');
    }
}
