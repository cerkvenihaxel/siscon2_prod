<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReporteProveedoresExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        // Define los nombres de las columnas aquí
        return [
            'Proveedor',
            'Mes',
            'Cotizadas',
            'Adjudicadas',
            'Finalizadas',
        ];
    }
}
