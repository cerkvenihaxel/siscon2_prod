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


class ProveedoresReportExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public function __construct($data, $data2, $cot, $adj, $total)
    {
        $this->data = $data;
        $this->data2 = $data2;
        $this->cot = $cot;
        $this->adj = $adj;
        $this->total = $total;
       
       
       
    
    }

    public function view(): View
    {
        return view('proveedoresExport', [
            'data' => $this->data, 'id' => $this->data2, 'cot' => $this->cot, 'adj' => $this->adj, 'total' => $this->total
        ]);
    }
    
}
