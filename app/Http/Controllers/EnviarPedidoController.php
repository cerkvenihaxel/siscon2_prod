<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnviarPedidoController extends Controller
{
    public function dateRangeDeposito(Request $request){

        $dateRange = $request->input('daterange');
        $id = $request->input('opcion');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $data = DB::table('cotizacion_convenio')->where('punto_retiro_id', $id)->whereBetween('created_at', [$start_date, $end_date])->get();
        $entId = $data->pluck('id')->all();

        $entDetail = DB::table('cotizacion_convenio_detail')
            ->whereIn('cotizacion_convenio_id', $entId)
            ->groupBy('articuloZafiro_id')
            ->select('articuloZafiro_id', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('SUM(precio) as total_total'))
            ->get();

        $fecha = date('d-m-Y H:i:s');


        return view('envioPedido', compact('data', 'fecha', 'id', 'entDetail', 'start_date', 'end_date'));
    }
}
