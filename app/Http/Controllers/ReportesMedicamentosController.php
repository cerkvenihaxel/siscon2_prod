<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CotizacionConvenio;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportesMedicamentosExport;

class ReportesMedicamentosController extends Controller
{
    public function obtenerReportes(Request $request)
    {
        $startDate = $request->input('startDate', now()->startOfMonth()->toDateString());
        $endDate = $request->input('endDate', now()->endOfMonth()->toDateString());

        $reportes = CotizacionConvenio::query()
            ->select([
                'cotizacion_convenio.id_pedido',
                'cotizacion_convenio.nro_factura',
                'punto_retiro.nombre AS Punto_de_Retiro',
                'cotizacion_convenio_detail.created_at AS Fecha_de_Carga',
                'lin_pedido.item',
                'cotizacion_convenio_detail.articuloZafiro_id',
                'articulosZafiro.presentacion_completa AS Articulo',
                'patologias.nombre',
                'articulosZafiro.des_monodroga',
                'cotizacion_convenio_detail.cantidad AS Cantidad',
                'cotizacion_convenio_detail.precio',
                'cotizacion_convenio_detail.descuento',
                'cotizacion_convenio_detail.total',
                'cotizacion_convenio.fecha_comprobante AS Fecha_Comprobante'
            ])
            ->leftJoin('cotizacion_convenio_detail', 'cotizacion_convenio.id', '=', 'cotizacion_convenio_detail.cotizacion_convenio_id')
            ->leftJoin('punto_retiro', 'cotizacion_convenio.punto_retiro_id', '=', 'punto_retiro.id')
            ->leftJoin('articulosZafiro', 'cotizacion_convenio_detail.articuloZafiro_id', '=', 'articulosZafiro.id')
            ->leftJoin('pedido_medicamento', 'cotizacion_convenio.nrosolicitud', '=', 'pedido_medicamento.nrosolicitud')
            ->leftJoin('patologias', 'patologias.id', '=', 'pedido_medicamento.patologia')
            ->leftJoin('lin_pedido', 'cotizacion_convenio.id_pedido', '=', 'lin_pedido.id_pedido')
            ->whereNotNull('cotizacion_convenio.nro_factura')
            ->whereBetween('cotizacion_convenio_detail.created_at', [$startDate, $endDate])
            ->orderByDesc('cotizacion_convenio.id')
            ->paginate(15);

        return response()->json($reportes);
    }

    public function exportarExcel(Request $request)
    {
        $startDate = $request->input('startDate', now()->startOfMonth()->toDateString());
        $endDate = $request->input('endDate', now()->endOfMonth()->toDateString());

        return Excel::download(new ReportesMedicamentosExport($startDate, $endDate), 'reporte_medicamentos'.now().'.xlsx');
    }

}
