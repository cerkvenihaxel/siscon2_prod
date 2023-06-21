<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OficinaAutorizar;
use App\Models\OficinaAutorizarDetail;
use App\Models\PedidoMedicamento;
use App\Models\PedidoMedicamentoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class OficinaAutorizarPedidoMedicamentoController extends Controller
{
    //
    public function index(){

        $solicitudes = PedidoMedicamento::all();
        return view('autorizacionpedido.oficinaAutorizarPedidoMedicamentoView', compact('solicitudes'));
    }

    public function verPedido($id)
    {
        $pedido = PedidoMedicamento::findOrFail($id);
        $detalles = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $id)->get();
        $nombre = DB::table('afiliados')->where('id', $pedido->afiliados_id)->value('apeynombres');
        $nombremedico = DB::table('medicos')->where('id', $pedido->medicos_id)->value('nombremedico');

        return response()->json([
            'pedido' => $pedido,
            'detalles' => $detalles,
            'nombre' => $nombre,
            'nombremedico' => $nombremedico
        ]);
    }

    public function autorizarVerPedido($id)
    {
        $pedidoID = PedidoMedicamento::findOrFail($id)->id;
        $pedidos = PedidoMedicamento::findOrFail($id);
        $medicamentos = PedidoMedicamentoDetail::where('pedido_medicamento_id', $pedidoID)->get();

        // Realiza las operaciones necesarias para obtener los detalles del pedido y la oficina de autorización
        $response = [
            'medicamentos' => $medicamentos,
            'pedido' => $pedidos
        ];

        return response()->json($response);
    }

    public function autorizarGuardarPedido(Request $request){
        $medicamentos = $request->input('medicamentos');
        $nroSolicitud = $request->input('nroSolicitud');
        $nroAfiliado = $request->input('nroAfiliado');
        $observaciones = $request->input('observaciones');
        $myID = CRUDBooster::myId();
        $stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');

        $pedidoMedicamento = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->first();
        $pedidoMedicamento->estado_solicitud_id = 3;

        $oficinaAutorizar = new OficinaAutorizar();
        $oficinaAutorizar->afiliados_id = $pedidoMedicamento->afiliados_id;
        $oficinaAutorizar->medicos_id = $pedidoMedicamento->medicos_id;
        $oficinaAutorizar->edad = $pedidoMedicamento->edad;
        $oficinaAutorizar->obra_social = $pedidoMedicamento->obra_social;
        $oficinaAutorizar->nrosolicitud = $nroSolicitud;
        $oficinaAutorizar->nroAfiliado = $nroAfiliado;
        $oficinaAutorizar->clinicas_id = $pedidoMedicamento->clinicas_id;
        $oficinaAutorizar->zona_residencia = $pedidoMedicamento->zona_residencia;
        //$oficinaAutorizar = $pedidoMedicamento->email;
        $oficinaAutorizar->fecha_receta = $pedidoMedicamento->fecha_receta;
        $oficinaAutorizar->postdatada = $pedidoMedicamento->postdatada;
        $oficinaAutorizar->fecha_vencimiento = $pedidoMedicamento->fecha_vencimiento;
        $oficinaAutorizar->observaciones = $observaciones;
        $oficinaAutorizar->estado_solicitud_id = 3;
        $oficinaAutorizar->stamp_user = $stamp_user;
        $oficinaAutorizar->discapacidad = $pedidoMedicamento->discapacidad;
        $oficinaAutorizar->provincia = $pedidoMedicamento->provincia;

        $oficinaAutorizar->save();

        $oficinaAutorizarD = [];
        foreach ($medicamentos as $med) {
            $oficinaAutorizarDetail = new OficinaAutorizarDetail();
            $oficinaAutorizarDetail->convenio_oficina_os_id = $oficinaAutorizar->id;
            $oficinaAutorizarDetail->articuloszafiro_id = $med['articuloZafiro_id'];
            $oficinaAutorizarDetail->cantidad = $med['cantidad'];
            $oficinaAutorizarDetail->banda_descuento = $med['banda_descuento'];
            $oficinaAutorizarDetail->proveedores_convenio_id = $med['proveedor_convenio_id'];
            $oficinaAutorizarDetail->presentacion = $med['presentacion'];
            $oficinaAutorizarDetail->nrosolicitud = $nroSolicitud;
            $oficinaAutorizarDetail->observaciones = $observaciones;
            $oficinaAutorizarDetail->nroAfiliado = $nroAfiliado;

            $oficinaAutorizarD[] = $oficinaAutorizarDetail;
        }

        $oficinaAutorizar->oficinaAutorizarDetail()->saveMany($oficinaAutorizarD);

        $pedidoMedicamento->save();

        return redirect()->back()->with(['message' => 'Pedido autorizado correctamente']);

    }

    public function rechazarPedido(Request $request){
        $id = $request->input('pedidoId');
        $pedido = PedidoMedicamento::findOrFail($id);

        $pedido->estado_solicitud_id = 5;
        $pedido->save();

        return redirect()->back()->with(['message' => 'Pedido rechazado correctamente']);


    }


}
