<?php

namespace App\Http\Controllers;

use App\Models\User;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EscritorioConvenioMedicamentosController extends Controller
{
    public function deskView(){

        $id = CRUDBooster::myId();
        $privilegio = User::where('id', $id)->value('id_cms_privileges');


        $nroEntrantes = DB::table('pedido_medicamento')->where('estado_solicitud_id', 1)->count();
        $nroAutorizados = DB::table('pedido_medicamento')->where('estado_solicitud_id', 3)->count();
        $nroRechazados = DB::table('pedido_medicamento')->whereIn('estado_solicitud_id', [5, 9])->count();
        $nroAuditados = DB::table('pedido_medicamento')->where('estado_solicitud_id', 8)->count();

        $nroAsignados = DB::table('convenio_oficina_os')->where('proveedor', 2)->count();
        $nroProcesados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->count();
        $nroEntregados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->where('estado_pedido_id', 1)->count();
        $nroRechazadosGlobal = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->whereIn('estado_solicitud_id', [10, 5])->count();

        $patologiasName = DB::table('patologias')->get();


        return view('escritorioConvenioMedicamentosView', compact('nroEntrantes', 'nroAutorizados', 'nroRechazados', 'privilegio','nroAsignados', 'nroAuditados','nroProcesados', 'nroRechazadosGlobal','nroEntregados', 'patologiasName'));
    }


    public function verMas(Request $request)
    {
        $id = $request->id;
        $patologia = DB::table('patologias')->where('id', $id)->value('nombre');

        $afiliados = DB::table('afiliados_articulos')
            ->select('nro_afiliado', 'nombre')
            ->where('patologias', $id)
            ->distinct('nro_afiliado', 'nombre')
            ->get();




        return response()->json([
            'afiliados' => $afiliados,
            'patologia' => $patologia,

        ]);
    }


}
