<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReporteMedicamentosExport implements FromCollection, WithHeadings
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
            'Patología',
            'Nombre',
            'Documento',
            'Nro Afiliado',
            'Medicación',
            'Cantidad',
            'Zona de Residencia',
            'Fecha de Carga',
            'Nro Solicitud'
        ];
    }
}
