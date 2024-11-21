<?php

namespace App\Exports;

use App\Models\CotizacionConvenio;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportesMedicamentosExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    // Constructor para recibir las fechas
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Query principal para filtrar los datos
    public function query()
    {
        return CotizacionConvenio::query()
            ->select([
                'cotizacion_convenio.id_pedido',
                'cotizacion_convenio.nro_factura',
                'punto_retiro.nombre AS Punto_de_Retiro',
                'cotizacion_convenio_detail.created_at AS Fecha_de_Carga',
                'lin_pedido.item',
                'cotizacion_convenio_detail.articuloZafiro_id',
                'articulosZafiro.presentacion_completa AS Articulo',
                'patologias.nombre AS Patologia',
                'articulosZafiro.des_monodroga',
                'cotizacion_convenio_detail.cantidad AS Cantidad',
                'cotizacion_convenio_detail.precio',
                'cotizacion_convenio_detail.descuento',
                'cotizacion_convenio_detail.total'
            ])
            ->leftJoin('cotizacion_convenio_detail', 'cotizacion_convenio.id', '=', 'cotizacion_convenio_detail.cotizacion_convenio_id')
            ->leftJoin('punto_retiro', 'cotizacion_convenio.punto_retiro_id', '=', 'punto_retiro.id')
            ->leftJoin('articulosZafiro', 'cotizacion_convenio_detail.articuloZafiro_id', '=', 'articulosZafiro.id')
            ->leftJoin('pedido_medicamento', 'cotizacion_convenio.nrosolicitud', '=', 'pedido_medicamento.nrosolicitud')
            ->leftJoin('patologias', 'patologias.id', '=', 'pedido_medicamento.patologia')
            ->leftJoin('lin_pedido', 'cotizacion_convenio.id_pedido', '=', 'lin_pedido.id_pedido')
            ->whereNotNull('cotizacion_convenio.nro_factura')
            ->whereBetween('cotizacion_convenio_detail.created_at', [$this->startDate, $this->endDate])
            ->orderByDesc('cotizacion_convenio.id');
    }

    // Encabezados para el archivo Excel
    public function headings(): array
    {
        return [
            'ID Pedido',
            'Nro Factura',
            'Punto de Retiro',
            'Fecha de Carga',
            'Item',
            'ID Artículo Zafiro',
            'Artículo',
            'Patología',
            'Monodroga',
            'Cantidad',
            'Precio',
            'Descuento',
            'Total'
        ];
    }

    // Mapear los datos en el formato deseado
    public function map($reporte): array
    {
        return [
            $reporte->id_pedido,
            $reporte->nro_factura,
            $reporte->Punto_de_Retiro,
            $reporte->Fecha_de_Carga,
            $reporte->item,
            $reporte->articuloZafiro_id,
            $reporte->Articulo,
            $reporte->Patologia,
            $reporte->des_monodroga,
            $reporte->Cantidad,
            $reporte->precio,
            $reporte->descuento,
            $reporte->total
        ];
    }
}
