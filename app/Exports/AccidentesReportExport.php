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


class AccidentesReportExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public function __construct($data, $data2)
    {
        $this->data = $data;
        $this->data2 = $data2;


    }

    public function view(): View
    {
        return view('accidentesExport', [
            'data' => $this->data, 'fecha' => $this->data2
        ]);
    }

}
