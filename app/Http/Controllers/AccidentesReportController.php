<?php

namespace App\Http\Controllers;
use App\Exports\ProveedoresReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccidentesReportExport;
class AccidentesReportController extends Controller
{
    public function dateRangeAcc(Request $request)
    {

        $dateRange = $request->input('daterange');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        $data = DB::table('entrantes')->whereBetween('created_at', [$start_date, $end_date])->where('accidente', 'Si')->whereIn('estado_solicitud_id', [3, 4, 6])->get();




        return view('informeAccidenteViewReport', compact('data', 'start_date', 'end_date', 'total'));
    }

    public function exportExcelAccAll(Request $request){

        $dateRange = $request->input('daterange');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $fecha = date('d-m-Y H:i:s');

        $data = DB::table('entrantes')->whereBetween('created_at', [$start_date, $end_date])->where('accidente', 'Si')->whereIn('estado_solicitud_id', [3, 4, 6])->get();

        return $this->exportExcelAcc($data, $fecha);

    }



    public function exportExcelAcc($data, $fecha) {

        $filename = 'reporteAccidentes_'.'_'.$fecha.'.xlsx';
        return Excel::download(new AccidentesReportExport($data, $fecha), $filename);
    }
}
