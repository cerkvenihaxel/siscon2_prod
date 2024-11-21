<?php

namespace App\Exports;

use App\Models\CotizacionConvenio;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteMedicamentosParticularExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $puntoRetiroId;

    // Constructor para recibir las fechas y punto de retiro
    public function __construct($startDate, $endDate, $puntoRetiroId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->puntoRetiroId = $puntoRetiroId;
    }

    // Query principal para filtrar los datos
    public function query()
    {
        $query = CotizacionConvenio::query()
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
            ->whereBetween('cotizacion_convenio_detail.created_at', [$this->startDate, $this->endDate])
            ->when($this->puntoRetiroId, function ($query) {
                return $query->where('cotizacion_convenio.punto_retiro_id', $this->puntoRetiroId);
            })
            ->orderByDesc('cotizacion_convenio.id');

        return $query;
    }

    // Encabezados para el archivo Excel
    public function headings(): array
    {
        return [
            'Sucursal',
            'ID Artículo',
            'Artículo',
            'Cantidad',
            'Afiliado',
            'Número de Afiliado',
            'ID Pedido',
            'Remito',
            'Fecha de Carga',
            'Patología',
        ];
    }

    // Mapear los datos en el formato deseado
    public function map($reporte): array
    {
        return [
            $reporte->Sucursal,
            $reporte->Id_Articulo,
            $reporte->Articulo,
            $reporte->Cantidad,
            $reporte->Afiliado,
            $reporte->Numero_de_Afiliado,
            $reporte->id_pedido,
            $reporte->Remito,
            $reporte->Fecha_de_Carga,
            $reporte->Patología,
        ];
    }
}
