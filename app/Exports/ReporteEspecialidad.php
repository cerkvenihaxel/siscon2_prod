<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReporteEspecialidad implements FromCollection, WithHeadings
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
        return [
            'Especialidad',
            'INGRESO ENERO',
            'INGRESO FEBRERO',
            'INGRESO MARZO',
            'INGRESO ABRIL',
            'INGRESO MAYO',
            'INGRESO JUNIO',
            'INGRESO JULIO',
            'INGRESO AGOSTO',
            'INGRESO SEPTIEMBRE',
            'INGRESO OCTUBRE',
            'INGRESO NOVIEMBRE',
            'INGRESO DICIEMBRE',
        ];
    }

}
