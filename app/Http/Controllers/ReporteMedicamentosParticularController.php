<?php

namespace App\Http\Controllers;

use App\Exports\ReporteMedicamentosParticularExport;
use App\Models\CotizacionConvenio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReporteMedicamentosParticularController extends Controller
{
    public function obtenerReportes(Request $request)
    {
        $startDate = $request->input('startDate', now()->startOfMonth()->toDateString());
        $endDate = $request->input('endDate', now()->endOfMonth()->toDateString());
        $puntoRetiroId = $request->input('puntoRetiroId');

        Log::debug('PuntoRetiroId: '.$puntoRetiroId);
        $reportes = CotizacionConvenio::query()
            ->select([
                'punto_retiro.nombre AS Sucursal',
                'cotizacion_convenio_detail.articuloZafiro_id AS Id_Articulo',
                'articulosZafiro.presentacion_completa AS Artículo',
                'cotizacion_convenio_detail.cantidad AS Cantidad',
                'cotizacion_convenio.nombreyapellido AS Afiliado',
                'cotizacion_convenio.nroAfiliado AS Numero_de_Afiliado',
                'cotizacion_convenio.id_pedido',
                'cotizacion_convenio.nro_remito AS Remito',
                'cotizacion_convenio_detail.created_at AS Fecha_de_Carga',
                'patologias.nombre AS Patología'
            ])
            ->leftJoin('cotizacion_convenio_detail', 'cotizacion_convenio.id', '=', 'cotizacion_convenio_detail.cotizacion_convenio_id')
            ->leftJoin('punto_retiro', 'cotizacion_convenio.punto_retiro_id', '=', 'punto_retiro.id')
            ->leftJoin('articulosZafiro', 'cotizacion_convenio_detail.articuloZafiro_id', '=', 'articulosZafiro.id')
            ->leftJoin('pedido_medicamento', 'cotizacion_convenio.nrosolicitud', '=', 'pedido_medicamento.nrosolicitud')
            ->leftJoin('patologias', 'patologias.id', '=', 'pedido_medicamento.patologia')
            ->whereNotNull('cotizacion_convenio.nro_remito')
            ->whereBetween('cotizacion_convenio_detail.created_at', [$startDate, $endDate])
            ->when($puntoRetiroId, function ($query, $puntoRetiroId) {
                return $query->where('cotizacion_convenio.punto_retiro_id', $puntoRetiroId);
            })
            ->orderByDesc('cotizacion_convenio.id')
            ->paginate(15);

        Log::debug('reporte output', ['reporte' => $reportes]);
        return response()->json($reportes);
    }

    public function exportarExcel(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $puntoRetiroId = $request->input('puntoRetiroId');

        return Excel::download(new ReporteMedicamentosParticularExport($startDate, $endDate, $puntoRetiroId), 'reportes_medicamentos_particular.xlsx');
    }
}
