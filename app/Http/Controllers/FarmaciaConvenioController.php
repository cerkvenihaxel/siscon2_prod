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

            $privilageId = CRUDBooster::myPrivilegeId();

            if($privilageId == 45){
                $nombre = CRUDBooster::myName();
                $puntoRetiroId = DB::table('punto_retiro')->where('nombre', 'LIKE', $nombre)->value('id');
                $solicitudes = CotizacionConvenio::where('punto_retiro_id', $puntoRetiroId)->get();
                return view('farmaciasconvenio.farmaciaPedidoMedicamentoView', compact('solicitudes','puntoRetiroId'));
            }

            $solicitudes = CotizacionConvenio::all();
            return view('farmaciasconvenio.farmaciaPedidoMedicamentoView', compact('solicitudes'));

    }

    public function verPedido($id)
    {
        $pedido =  CotizacionConvenio::findOrFail($id);
        $detalles = CotizacionConvenioDetail::where('cotizacion_convenio_id', $id)->get();
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

        OficinaAutorizar::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13]);
        OficinaAutorizar::where('nrosolicitud', $nroSolicitud)->update(['estado_pedido_id' => 1]);
        PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13]);
        CotizacionConvenio::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 13]);
        CotizacionConvenio::where('nrosolicitud', $nroSolicitud)->update(['estado_pedido_id' => 1]);

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue autorizada con éxito!","success");

    }

    }
