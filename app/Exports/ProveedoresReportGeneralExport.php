<?php

namespace App\Exports;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ProveedoresReportGeneralExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public function __construct($data, $data2, $data3, $data4, $data5)
    {
        $this->data = $data;
        $this->data2 = $data2;
        $this->data3 = $data3;
        $this->data4 = $data4;
        $this->data5 = $data5;

    }

    public function view(): View
    {
        return view('proveedoresGeneralExport', [
            'data' => $this->data, 'entrantes' => $this->data2, 'start_date' => $this->data3, 'end_date' => $this->data4, 'proveedores' => $this->data5
        ]);
    }

}
