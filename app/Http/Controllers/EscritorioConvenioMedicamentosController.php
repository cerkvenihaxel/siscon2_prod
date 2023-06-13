<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EscritorioConvenioMedicamentosController extends Controller
{
    public function deskView(){
        $nroEntrantes = DB::table('pedido_medicamento')->where('estado_solicitud_id', 1)->count();
        $nroAutorizados = DB::table('pedido_medicamento')->where('estado_solicitud_id', 3)->count();
        $nroRechazados = DB::table('pedido_medicamento')->whereIn('estado_solicitud_id', [5, 9])->count();

        $nroAsignados = DB::table('convenio_oficina_os')->where('proveedor', 2)->count();
        $nroProcesados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->count();
        $nroEntregados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->where('estado_pedido_id', 1)->count();



        $patologiasName = DB::table('patologias')->get();


        return view('escritorioConvenioMedicamentosView', compact('nroEntrantes', 'nroAutorizados', 'nroRechazados', 'nroAsignados', 'nroProcesados', 'nroEntregados', 'patologiasName'));
    }
}
