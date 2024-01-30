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
            'INGRESO ENERO 2023',
            'INGRESO FEBRERO 2023',
            'INGRESO MARZO 2023',
            'INGRESO ABRIL 2023',
            'INGRESO MAYO 2023',
            'INGRESO JUNIO 2023',
            'INGRESO JULIO 2023',
            'INGRESO AGOSTO 2023',
            'INGRESO SEPTIEMBRE 2023',
            'INGRESO OCTUBRE 2023',
            'INGRESO NOVIEMBRE 2023',
            'INGRESO DICIEMBRE 2023',
            'INGRESO ENERO 2024',
            'INGRESO FEBRERO 2024',
            'INGRESO MARZO 2024',
            'INGRESO ABRIL 2024',
            'INGRESO MAYO 2024',
            'INGRESO JUNIO 2024',
            'INGRESO JULIO 2024',
            'INGRESO AGOSTO 2024',
            'INGRESO SEPTIEMBRE 2024',
            'INGRESO OCTUBRE 2024',
            'INGRESO NOVIEMBRE 2024',
            'INGRESO DICIEMBRE 2024',
        ];
    }

}
