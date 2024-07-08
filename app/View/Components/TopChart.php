<?php

namespace App\View\Components;

use App\Models\CotizacionConvenio;
use App\Models\Patologias;
use App\Models\PedidoMedicamento;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;


class TopChart extends Component
{
    public $inbox;
    public $processed;
    public $delivered;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($startDate = '2022-01-01', $endDate = '2029-12-31')
    {


        $cantidadEntrantes = PedidoMedicamento::whereBetween('created_at', [$startDate, $endDate])->count();

        $cantidadProcesadas = CotizacionConvenio::
            where('estado_solicitud_id', 11)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $cantidadFinalizadas = CotizacionConvenio::where('estado_solicitud_id', 13)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $this->inbox = $cantidadEntrantes;
        $this->processed = $cantidadProcesadas;
        $this->delivered = $cantidadFinalizadas;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.reporteconvenio.topchart');
    }
}
