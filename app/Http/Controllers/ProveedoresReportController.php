<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\exportPdf;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\ProveedoresReportExport;
use Illuminate\Support\Facades\DB;
use App\Exports\ProveedoresReportGeneralExport;

class ProveedoresReportController extends Controller
{
    public $data;
    use Exportable;


    //Put your functions below this line
    public function dateRangeProv(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('opcion');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        $date = Carbon::parse($date)->format('Y-m-d');


        $data = DB::table('cotizaciones')->where('proveedor', $id)->whereBetween('created_at', [$start_date, $end_date])->get();

        $cotId = $data->pluck('id')->all();
        $cotDetail = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $cotId)->get();

        $adj = DB::table('adjudicaciones')->where('adjudicatario', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        // // your oth  er code here
        $fecha = date('d-m-Y H:i:s');


       return view('proveedorViewReport', compact('data', 'id', 'cotDetail', 'start_date', 'end_date', 'fecha', 'adj'));
    }

    public function exportExcelProvAll(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('opcion');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $data = DB::table('cotizaciones')->where('proveedor', $id)->whereBetween('created_at', [$start_date, $end_date])->get();

        $cotId = $data->pluck('id')->all();
        $cotDetail = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $cotId)->get();
        $adj = DB::table('adjudicaciones')->where('adjudicatario', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        $total = DB::table('cotizaciones')->where('proveedor', $id)->whereBetween('created_at', [$start_date, $end_date])->sum('total');
        $fecha = date('d-m-Y H:i:s');

          return $this->exportExcelProv($data, $fecha, $id, $cotDetail, $adj, $total);

    }

    public function exportExcelProvGeneralAll (Request $request){
        $dateRange = $request->input('daterange');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $proveedores = DB::table('cotizaciones')->whereBetween('created_at', [$start_date, $end_date])->distinct('proveedor')->get('proveedor');

        $proveedores = $proveedores->pluck('proveedor')->all();
        $data = DB::table('cotizaciones')->whereBetween('created_at', [$start_date, $end_date])->get();

        $fecha = date('d-m-Y H:i:s');
        $entrantes = DB::table('entrantes')->whereBetween('created_at', [$start_date, $end_date])->get();


        return $this->exportExcelProvGeneral($data, $entrantes, $fecha, $start_date, $end_date, $proveedores);
    }


    public function exportExcelProv($data, $fecha, $id, $cotDetail,$adj, $total) {

        $filename = 'reporteProveedores_'.$id.'_'.$fecha.'.xlsx';
        return Excel::download(new ProveedoresReportExport($data, $id, $cotDetail, $adj, $total), $filename);
    }

    public function exportExcelProvGeneral($data, $entrantes, $fecha ,$start_date, $end_date, $proveedores){
        $filename = 'reporteProveedoresGeneral_'.$fecha.'.xlsx';
        return Excel::download(new ProveedoresReportGeneralExport($data, $entrantes, $start_date, $end_date, $proveedores), $filename);
    }





}
