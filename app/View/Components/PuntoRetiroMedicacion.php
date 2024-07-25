<?php

namespace App\View\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class PuntoRetiroMedicacion extends Component
{
    public $result;
    public $puntosRetiro;
    public function __construct($startDate = '2024-01-01', $endDate = '2024-12-31', $tableIdSuffix = '')
    {
        $resultData = DB::table('cotizacion_convenio_detail as ccd')
            ->select(
                'az.presentacion_completa as nombreMedicacion',
                DB::raw('COUNT(*) as cantidadMedicacion'),
                'pr.nombre as nombrePuntoRetiro'
            )
            ->leftJoin('articulosZafiro as az', 'ccd.articuloZafiro_id', '=', 'az.id')
            ->leftJoin('cotizacion_convenio as cc', 'ccd.cotizacion_convenio_id', '=', 'cc.id')
            ->leftJoin('punto_retiro as pr', 'cc.punto_retiro_id', '=', 'pr.id')
            ->whereBetween('cc.created_at', [$startDate, $endDate])
            ->groupBy('ccd.articuloZafiro_id', 'pr.nombre')
            ->orderBy('cantidadMedicacion', 'DESC')
            ->get();

        $this->result = $resultData;
        $this->tableId = 'table' . $tableIdSuffix;
        $puntosRetiro = DB::table('punto_retiro')->select('id', 'nombre')->get();
        $this->puntosRetiro = $puntosRetiro;

    }

    public function render()
    {
        return view('components.punto-retiro-medicacion');
    }
}
