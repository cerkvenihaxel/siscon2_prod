<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;
use App\Models\OficinaAutorizar;
use App\Models\PedidoMedicamento;
use App\Models\PedidoMedicamentoDetail;
use App\Models\User;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmaciaConvenioController extends Controller
{
    public function index(){


        $privilegio = CRUDBooster::myPrivilegeId();
        if($privilegio == 45){
            $id = CRUDBooster::myId();
            $farmaciaNombre = User::where('id', $id)->value('name');
            $puntoRetiro = DB::table('punto_retiro')->where('nombre', 'LIKE',  $farmaciaNombre)->value('id');
            $solicitudes = CotizacionConvenio::where('punto_retiro_id', $puntoRetiro)->get();
            return view('farmaciasconvenio.farmaciaPedidoMedicamentoView', compact('solicitudes'));
        }
        else{
            $solicitudes = CotizacionConvenio::all();
            return view('farmaciasconvenio.farmaciaPedidoMedicamentoView', compact('solicitudes'));
        }
    }

    public function verPedido($id)
    {
        $pedido =  CotizacionConvenio::findOrFail($id);
        $pedido_medicamento_id = DB::table('pedido_medicamento')->where('nrosolicitud', $pedido->nrosolicitud)->value('id');
        $detalles = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $pedido_medicamento_id)->get();
        $nombre = CotizacionConvenio::where('nrosolicitud', $pedido->nrosolicitud)->value('nombreyapellido');
        $nombremedico = DB::table('medicos')->where('id', $pedido->medicos_id)->value('nombremedico');

        return response()->json([
            'pedido' => $pedido,
            'detalles' => $detalles,
            'nombre' => $nombre,
            'nombremedico' => $nombremedico
        ]);
    }

    public function rechazarPedido(Request $request)
    {
        $id = $request->input('pedidoId');
        $pedido = CotizacionConvenio::findOrFail($id);
        $pedido->estado_solicitud_id = 14;
        $pedido->save();
        return redirect()->back()->with(['message' => 'Pedido rechazado correctamente']);
    }
    public function autorizarVerPedido($id)
    {

        $nroSolicitud = CotizacionConvenio::findOrFail($id)->nrosolicitud;
        $pedidoID = DB::table('cotizacion_convenio')->where('nrosolicitud', $nroSolicitud)->value('id');
        $pedidos = CotizacionConvenio::findOrFail($pedidoID);
        $medicamentos = CotizacionConvenioDetail::where('cotizacion_convenio_id', $pedidoID)->get();

        foreach ($medicamentos as $medicamento) {
            $articuloZafiroID = $medicamento->articuloZafiro_id;
            $descripcionMonodroga = DB::table('articulosZafiro')->where('id', $articuloZafiroID)->value('des_monodroga');
            $medicamento->des_monodroga = $descripcionMonodroga;
        }


        // Realiza las operaciones necesarias para obtener los detalles del pedido y la oficina de autorización
        $response = [
            'medicamentos' => $medicamentos,
            'pedido' => $pedidos,
        ];

        return response()->json($response);
    }

    public function autorizarGuardarPedido(Request $request){

        $medicamentos = $request->input('medicamentos');
        $nroSolicitud = $request->input('nrosolicitud');

        foreach ($medicamentos as $med){
            $medicamento = CotizacionConvenioDetail::findOrFail($med['id']);
            $medicamento->cantidad_entregada = $med['cantidad_entregada'];
            $medicamento->save();
        }

        OficinaAutorizar::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13])->update(['estado_pedido_id' => 1]);
        PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13]);
        CotizacionConvenio::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13])->update(['estado_pedido_id' => 1]);

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue autorizada con éxito!","success");

    }

    }
