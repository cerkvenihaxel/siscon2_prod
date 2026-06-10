<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Reporte de entregas OSPLAD para facturación.
 * Una fila por consumo entregado; el admin puede agrupar por remito en Excel.
 */
class OspladEntregasExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected Collection $consumos;

    public function __construct(Collection $consumos)
    {
        $this->consumos = $consumos;
    }

    public function collection(): Collection
    {
        return $this->consumos;
    }

    public function headings(): array
    {
        return [
            'Remito',
            'Fecha entrega',
            'Afiliado',
            'DNI',
            'Artículo',
            'Cantidad',
            'N° pedido Zafiro',
            'Farmacia',
            'Localidad',
            'Provincia',
        ];
    }

    public function map($c): array
    {
        return [
            $c->id_remito,
            optional($c->fecha_validacion)->format('d/m/Y'),
            $c->afiliado,
            $c->dni,
            $c->articulo,
            $c->cantidad,
            $c->id_pedido_zafiro,
            $c->cliente,
            $c->localidad,
            $c->provincia,
        ];
    }
}
