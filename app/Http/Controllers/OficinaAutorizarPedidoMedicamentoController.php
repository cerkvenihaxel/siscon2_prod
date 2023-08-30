<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArticulosZafiro;
use App\Models\OficinaAutorizar;
use App\Models\OficinaAutorizarDetail;
use App\Models\PedidoMedicamento;
use App\Models\PedidoMedicamentoDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster;


class OficinaAutorizarPedidoMedicamentoController extends Controller
{
    //
    public function index()
    {
        $privilegio = CRUDBooster::myPrivilegeId();
        $id = CRUDBooster::myId();
        $solicitudes = PedidoMedicamento::with('detalles', 'afiliados', 'patologias', 'medicos', 'estadoSolicitud', 'patologiasName')->get();
        $stamp_userConvenio = null;
        $auditor = $privilegio == 40;

        if ($privilegio == 41) {
            $stamp_userConvenio = User::where('id', $id)->pluck('email')->first();
            $solicitudesPedido = OficinaAutorizar::where('stamp_user', $stamp_userConvenio)->pluck('nrosolicitud');
            $solicitudesAutorizadas = PedidoMedicamento::whereIn('nrosolicitud', $solicitudesPedido)->get();
        } else {
            $solicitudesAutorizadas = OficinaAutorizar::all();
        }

        return view('autorizacionpedido.oficinaAutorizarPedidoMedicamentoView', compact('solicitudes', 'auditor', 'stamp_userConvenio', 'solicitudesAutorizadas'));
    }

    public function verPedidoMedico($id)
    {
        $pedido = PedidoMedicamento::findOrFail($id);
        $detalles = PedidoMedicamentoDetail::where('pedido_medicamento_id', $id)->get();
        $nombre = DB::table('afiliados')->where('id', $pedido->afiliados_id)->value('apeynombres');
        $nombremedico = DB::table('medicos')->where('id', $pedido->medicos_id)->value('nombremedico');

        return response()->json([
            'pedido' => $pedido,
            'detalles' => $detalles,
            'nombre' => $nombre,
            'nombremedico' => $nombremedico
        ]);
    }

    public function verPedido($id)
    {
        $pedido = OficinaAutorizar::findOrFail($id);
        $detalles = OficinaAutorizarDetail::where('convenio_oficina_os_id', $id)->get();
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

        $medicamentosActualizados = []; // Arreglo para almacenar los medicamentos actualizados

        foreach ($medicamentos as $medicamento) {

            // ! REVISAR EL NÚMERO DE ARTÍCULO QUE SE ESTÁ GUARDANDO EN LA BASE DE DATOS
            $numeroArticulo =  str_pad($medicamento->articuloZafiro_id, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda
            $idArticulo = DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('id_articulo');
            $medicamento->banda_descuento = DB::table('banda_descuentos')->where('id_articulo', $idArticulo)->value('banda_descuento');
            // Agregar el medicamento actualizado al arreglo
            $medicamentosActualizados[] = $medicamento;
        }

        // Realiza las operaciones necesarias para obtener los detalles del pedido y la oficina de autorización
        $response = [
            'medicamentos' => $medicamentosActualizados,
            'pedido' => $pedidos
        ];

        return response()->json($response);
    }

    public function autorizarGuardarPedido(Request $request)
    {
        $medicamentos = $request->input('medicamentos');
        $nroSolicitud = $request->input('nroSolicitud');
        $nroAfiliado = $request->input('nroAfiliado');
        $observaciones = $request->input('observaciones');
        $myID = CRUDBooster::myId();
        $stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');

        $pedidoMedicamento = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->first();
        $pedidoMedicamento->estado_solicitud_id = 4;
        $pedidoMedicamento->updated_at = now();

        $proveedores = [];

        // Agrupar medicamentos por proveedor
        foreach ($medicamentos as $med) {
            $proveedorId = $med['proveedor_convenio_id'];

            if (!isset($proveedores[$proveedorId])) {
                $proveedores[$proveedorId] = [];
            }

            $proveedores[$proveedorId][] = $med;
        }

        // Crear y guardar objetos OficinaAutorizar y OficinaAutorizarDetail para cada grupo de medicamentos
        foreach ($proveedores as $proveedorId => $medicamentosProveedor) {
            $oficinaAutorizar = new OficinaAutorizar();
            $oficinaAutorizar->afiliados_id = $pedidoMedicamento->afiliados_id;
            $oficinaAutorizar->medicos_id = $pedidoMedicamento->medicos_id;
            $oficinaAutorizar->edad = $pedidoMedicamento->edad;
            $oficinaAutorizar->obra_social = $pedidoMedicamento->obra_social;
            $oficinaAutorizar->nrosolicitud = $nroSolicitud;
            $oficinaAutorizar->nroAfiliado = $nroAfiliado;
            $oficinaAutorizar->clinicas_id = $pedidoMedicamento->clinicas_id;
            $oficinaAutorizar->zona_residencia = $pedidoMedicamento->zona_residencia;
            $oficinaAutorizar->fecha_receta = $pedidoMedicamento->fecha_receta;
            $oficinaAutorizar->postdatada = $pedidoMedicamento->postdatada;
            $oficinaAutorizar->patologia = $pedidoMedicamento->patologia;
            $oficinaAutorizar->fecha_vencimiento = $pedidoMedicamento->fecha_vencimiento;
            $oficinaAutorizar->observaciones = $observaciones;
            $oficinaAutorizar->estado_solicitud_id = 4;
            $oficinaAutorizar->tel_afiliado = $pedidoMedicamento->tel_afiliado;
            $oficinaAutorizar->stamp_user = $stamp_user;
            $oficinaAutorizar->discapacidad = $pedidoMedicamento->discapacidad;
            $oficinaAutorizar->provincia = $pedidoMedicamento->provincia;
            $oficinaAutorizar->proveedor = $proveedorId;

            $oficinaAutorizar->save();

            $oficinaAutorizarD = [];
            foreach ($medicamentosProveedor as $med) {
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
        }

        $pedidoMedicamento->save();

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "La solicitud fue autorizada con éxito!", "success");
    }


    public function rechazarPedido(Request $request){
        $id = $request->input('pedidoId');
        $pedido = PedidoMedicamento::findOrFail($id);

        $pedido->estado_solicitud_id = 5;
        $pedido->save();

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue rechazada con éxito!","danger");

    }

    public function auditarPedido(Request $request){
        $id = $request->input('pedidoId');
        $pedido = PedidoMedicamento::findOrFail($id);
        $pedido->estado_solicitud_id = 8;
        $pedido->save();

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue rechazada con éxito!","success");

    }


}
