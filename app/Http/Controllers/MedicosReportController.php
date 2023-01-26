<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\exportPdf;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\MedicosReportExport;
use Illuminate\Support\Facades\DB;

class MedicosReportController extends Controller
{
    //

    public $data;
    use Exportable;
    
    
    //Put your functions below this line
    public function dateRangeMed(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('opcion');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        
        $data = DB::table('entrantes')->where('medicos_id', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        $entId = $data->pluck('id')->all();
        $entDetail = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $entId)->get();
        $adj = DB::table('adjudicaciones')->where('medicos_id', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        // // // your oth  er code here
        $fecha = date('d-m-Y H:i:s');


       return view('medicoViewReport', compact('data', 'fecha', 'id', 'entDetail', 'adj', 'start_date', 'end_date'));
    }

    public function exportExcelMedAll(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('opcion');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        
        $nombreMedico = DB::table('medicos')->where('id', $id)->value('nombremedico');
        $data = DB::table('entrantes')->where('medicos_id', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        $entId = $data->pluck('id')->all();
        $entDetail = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $entId)->get();
        $adj = DB::table('adjudicaciones')->where('medicos_id', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        // // // your oth  er code here
        $fecha = date('d-m-Y H:i:s');
        return $this->exportExcelMed($data, $fecha, $nombreMedico, $adj);
    }



    public function exportExcelMed($data, $fecha, $nombreMedico, $adj) {

        $filename = 'reporteMedicos_'.$nombreMedico.'_'.$fecha.'.xlsx';
        return Excel::download(new MedicosReportExport($data, $nombreMedico, $adj), $filename);
    }

}
