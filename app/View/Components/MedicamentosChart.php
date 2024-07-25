<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\Debugbar\Facades\Debugbar;

class MedicamentosChart extends Component
{
    public $result;


    public function __construct($startDate = '2024-01-01', $endDate = '2024-12-31', $tableIdSuffix = '')
    {
        $resultData = DB::table('pedido_medicamento_detail as pmd')
            ->select('az.presentacion_completa as nombreMedicacion', DB::raw('COUNT(*) as cantidadMedicacion'))
            ->leftJoin('articulosZafiro as az', 'pmd.articuloZafiro_id', '=', 'az.id')
            ->leftJoin('pedido_medicamento as pm', 'pmd.pedido_medicamento_id'  ,'=', 'pm.id')
            ->whereBetween('pm.created_at', [$startDate, $endDate])
            ->groupBy('pmd.articuloZafiro_id')
            ->orderBy('cantidadMedicacion', 'DESC')
            ->get();

        $this->result = $resultData;
        $this->tableId = 'table' . $tableIdSuffix;

    }


    public function render()
    {
        return view('components.medicamentos-chart');
    }
}
