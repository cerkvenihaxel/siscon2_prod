<?php

namespace App\Http\Controllers\ComponentsController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function updateCharts(Request $request)
    {
        // Validar las fechas recibidas
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Crear una instancia del componente DispensaChart con las fechas recibidas
        $dispensaChart = new DispensaChart($startDate, $endDate);

        // Renderizar la vista principal con el gráfico actualizado
        return view('reports_graphs.convenio', [
            'dispensaChart' => $dispensaChart
        ]);
    }
}
