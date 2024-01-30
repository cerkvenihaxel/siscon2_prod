<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReporteMedicosExport implements FromCollection, WithHeadings
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
            'Nombre medico',
            'Enero 2023',
            'Febrero 2023',
            'Marzo 2023',
            'Abril 2023',
            'Mayo 2023',
            'Junio 2023',
            'Julio 2023',
            'Agosto 2023',
            'Septiembre 2023',
            'Octubre 2023',
            'Noviembre 2023',
            'Diciembre 2023',
            'Enero 2024',
            'Febrero 2024',
            'Marzo 2024',
            'Abril 2024',
            'Mayo 2024',
            'Junio 2024',
            'Julio 2024',
            'Agosto 2024',
            'Septiembre 2024',
            'Octubre 2024',
            'Noviembre 2024',
            'Diciembre 2024',
        ];
    }

}
