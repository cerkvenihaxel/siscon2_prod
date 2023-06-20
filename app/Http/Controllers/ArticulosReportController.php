<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\exportPdf;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\ArticulosReportExport;
use Illuminate\Support\Facades\DB;

class ArticulosReportController extends Controller
{
    //Put here your functions
    public $data;
    use Exportable;

    public function dateRange(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('busqueda');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        $date = Carbon::parse($date)->format('Y-m-d');
        // your other code here
        $nombreArticulo = DB::table('articulos')->where('id', $id)->value('des_articulo');

        //Table del médico
        $entrantes_detail = DB::table('entrantes_detail')->where('articulos_id', $id)->pluck('entrantes_id')->all();
        $data = DB::table('entrantes')->whereIn('id', $entrantes_detail)->whereBetween('created_at', [$start_date, $end_date])->get();
        $data2 = DB::table('entrantes_detail')->whereIn('entrantes_id', $entrantes_detail)->where('articulos_id', $id)->get();

        //Table de las cotizaciones
        $cotizaciones_detail = DB::table('cotizaciones_detail')->where('articulos_id', $id)->pluck('entrantes_id')->all();
        $cot = DB::table('cotizaciones')->whereIn('id', $cotizaciones_detail)->whereBetween('created_at', [$start_date, $end_date])->get();
        $cot2 = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $cotizaciones_detail)->where('articulos_id', $id)->get();



        $fecha = date('d-m-Y H:i:s');


       return view('your-view', compact('data', 'data2','fecha', 'id', 'entrantes_detail', 'cotizaciones_detail', 'nombreArticulo', 'cot', 'cot2'));
    }


    public function exportExcelAll(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('busqueda');


        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        $date = Carbon::parse($date)->format('Y-m-d');




        // your other code here
        $entrantes_detail = DB::table('entrantes_detail')->where('articulos_id', $id)->pluck('entrantes_id')->all();

        $data = DB::table('entrantes')->whereIn('id', $entrantes_detail)->whereBetween('created_at', [$start_date, $end_date])->get();



        $data2 = DB::table('entrantes_detail')->whereIn('entrantes_id', $entrantes_detail)->where('articulos_id', $id)->get();

        //Table de las cotizaciones
        $cotizaciones_detail = DB::table('cotizaciones_detail')->where('articulos_id', $id)->pluck('entrantes_id')->all();
        $cot = DB::table('cotizaciones')->whereIn('id', $cotizaciones_detail)->whereBetween('created_at', [$start_date, $end_date])->get();
        $cot2 = DB::table('cotizaciones_detail')->whereIn('entrantes_id', $cotizaciones_detail)->where('articulos_id', $id)->get();


        $nombreArticulo = DB::table('articulos')->where('id', $id)->value('des_articulo');
        $fecha = date('d-m-Y H:i:s');

        return $this->exportExcel($data, $nombreArticulo, $fecha, $id, $cot);
       //return view('your-view', compact('data', 'id'));
    }



  public function exportExcel($data, $nombreArticulo, $fecha, $id, $cot) {
        $filename = 'reporteArticulos_'.$nombreArticulo.'_'.$fecha.'.xlsx';

        return Excel::download(new ArticulosReportExport($data, $id, $cot), $filename);
    }




}

