<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReporteMes implements FromCollection, WithHeadings
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
            'Fecha de carga',
            'Mes de carga',
            'Nombre',
            'N° de afiliado',
            'Clínica',
            'Edad',
            'Número de solicitud',
            'Médico',
            'Estado paciente',
            'Estado solicitud',
            'Fecha de cirugía',
            'Sufrió accidente',
            'Necesidad',
            'Grupo artículos',
            'Fecha de expiración'
        ];
    }

}
