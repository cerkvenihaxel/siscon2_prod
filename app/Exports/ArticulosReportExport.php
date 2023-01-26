<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ArticulosReportExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public function __construct($data, $data2, $cot)
    {
        $this->data = $data;
        $this->data2 = $data2;
        $this->cot = $cot;
    
    }

    public function view(): View
    {
        return view('articulosExport', [
            'data' => $this->data, 'id' => $this->data2, 'cot' => $this->cot
        ]);
    }
   
}
