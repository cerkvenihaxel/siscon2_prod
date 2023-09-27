<?php

namespace App\Http\Controllers;

use App\Models\Afiliados;
use App\Models\ArticulosZafiro;
use App\Models\Clinica;
use App\Models\Medico;
use App\Models\PedidoMedicamentoDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AfiliadosArticulos;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use App\Models\AfiliadosArticulosModel;
use App\Models\PedidoMedicamento;


class MedicoCrearPedidoMedicamentoController extends Controller
{

    public function index(){
        $id = CRUDBooster::myId();
        $privilege = CRUDBooster::myPrivilegeId();
        $pedidosMedicos = null;
        $nombreMedico = null;

        if($privilege == 6){
            $nombreMedico = User::where('id', $id)->value('name');
            $medicoID = DB::table('medicos')->where('nombremedico', 'LIKE', $nombreMedico)->value('id');
            $pedidosMedicos = DB::table('pedido_medicamento')->where('medicos_id', $medicoID)->get();
        }

        $patologias = DB::table('patologias')->get();

        return view('medicoCrearPedidoMedicamentoview', compact('pedidosMedicos', 'nombreMedico', 'patologias'));
    }
    public function buscarAfiliado(Request $request)
    {
        $search = $request->input('numeroAfiliado');
        $searchObraSocial = $request->input('obra_social_id');
        $searchPatologia = $request->input('patologia');

        $patologias = DB::table('patologias')->get();

        if (strlen($search) < 10) {
            $documento = Afiliados::where('documento', $search)->where('obra_social_id', $searchObraSocial)->value('nroAfiliado');
            $search = $documento;
        }

            $solicitud = AfiliadosArticulos::where('nro_afiliado', $search);


        if ($searchPatologia !== '0') {
            $solicitud = $solicitud->where('patologias', $searchPatologia);
        }

        $solicitud = $solicitud->get();

        $nombre = Afiliados::where('nroAfiliado', $search)->where('obra_social_id', $searchObraSocial)->value('apeynombres');
        $fechaNacimiento = Afiliados::where('nroAfiliado', $search)->where('obra_social_id', $searchObraSocial)->value('fecha_nacimiento');
        $edad = null;

        if ($fechaNacimiento) {
            $fechaNacimiento = Carbon::createFromFormat('Y-m-d', $fechaNacimiento);
            $edad = $fechaNacimiento->diffInYears(Carbon::now());
        }

        $localidad = Afiliados::where('nroAfiliado', $search)->where('obra_social_id', $searchObraSocial)->value('localidad');
        $telefono = Afiliados::where('nroAfiliado', $search)->where('obra_social_id', $searchObraSocial)->value('telefonos');
        $afiliado_id = Afiliados::where('nroAfiliado', $search)->where('obra_social_id', $searchObraSocial)->value('id');

        $obraSocialSolicitud = '';
        switch ($searchObraSocial){
            case 1: $obraSocialSolicitud = 'ISJ-MED'; break;
            case 2: $obraSocialSolicitud = 'FMS-MED'; break;
            case 3: $obraSocialSolicitud = 'APOS-MED'; break;
        }

        $nroSolicitud = $obraSocialSolicitud . date('dmHis');

        $clinicas = Clinica::all();
        $medicos = Medico::orderBy('nombremedico', 'asc')->get();
        $postdatada = DB::table('postdatada')->get();

        $id = CRUDBooster::myId();
        $privilege = CRUDBooster::myPrivilegeId();
        $pedidosMedicos = null;
        $nombreMedico = null;
        $stampuser = DB::table('cms_users')->where('id', $id)->value('email');

        if ($privilege == 6 || $privilege == 44) {
            $nombreMedico = User::where('id', $id)->value('name');
            $medicoID = DB::table('medicos')->where('nombremedico', 'LIKE', $nombreMedico)->value('id');
            $pedidosMedicos = DB::table('pedido_medicamento')->where('medicos_id', $medicoID)->get();
        }

        return view('medicoCrearPedidoMedicamentoview', compact('search', 'solicitud', 'nombre', 'localidad', 'telefono', 'pedidosMedicos', 'nombreMedico', 'patologias', 'afiliado_id', 'nroSolicitud', 'clinicas', 'medicos', 'postdatada', 'stampuser', 'searchPatologia', 'edad'));
    }

    public function edit($id)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
        return view('pm.edit', compact('afiliadoArticulo'));
    }
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario si es necesario
        $request->validate([
            'cantidad' => 'required|integer',
        ]);

        // Obtener el afiliado y sus datos actuales
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);


        // Actualizar la cantidad solamente, los demás valores permanecen iguales
        $afiliadoArticulo->cantidad = $request->cantidad;
        $afiliadoArticulo->save();

        // Redireccionar a la página de búsqueda conservando los datos del formulario
        return redirect()->back()->withInput()->with('success', 'Cantidad actualizada correctamente');
    }

    public function destroy($id){
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
        $afiliadoArticulo->delete();
        return json_encode(['success' => true]);
    }

    public function guardarPedido(Request $request){

        $validatedData = $request->validate([
            'nroAfiliado' => 'required',
            'nombre' => 'required',
            // Añade las validaciones para los demás campos del formulario
        ]);

        $nroAfiliado = $request->input('nroAfiliado');
        $afiliadoNuevo = PedidoMedicamento::where('nroAfiliado', $nroAfiliado)->first();

        if($afiliadoNuevo){
            $estado_solicitud = 8;
        }
        else{
            $estado_solicitud = 8;
        }

        // Crear una nueva instancia del modelo PedidoMedicamento y asignar los valores del formulario
        $pedido = new PedidoMedicamento;
        $pedido->created_at = date('Y-m-d H:i:s');
        $pedido->afiliados_id = $request->input('afiliado_id');
        $pedido->nroAfiliado = $nroAfiliado;
        $pedido->edad = $request->input('edad');
        $pedido->nrosolicitud = $request->input('nrosolicitud');
        $pedido->clinicas_id = $request->input('clinicas_id');
        $pedido->medicos_id = $request->input('medicos_id');
        $pedido->obra_social = 'APOS';
        $pedido->zona_residencia = $request->input('zona_residencia');
        $pedido->fecha_receta = $request->input('fecha_receta');
        $pedido->postdatada = $request->input('postdatada');
        $pedido->fecha_vencimiento = $request->input('fecha_venicimiento');
        $pedido->estado_solicitud_id = $estado_solicitud;
        $pedido->tel_afiliado = $request->input('tel_afiliado');
        $pedido->patologia = $request->input('patologias');
        $pedido->provincia = 11;
        $pedido->discapacidad = $request->input('discapacidad');
        $pedido->diagnostico = $request->input('diagnostico');
        $pedido->observaciones = $request->input('observaciones');
        $pedido->stamp_user = $request->input('stamp_user');

        // Guardar el pedido en la base de datos
        $pedido->save();

        $detalles = []; // Arreglo de detalles

        $articulos = DB::table('afiliados_articulos')
            ->where('nro_afiliado', $request->input('nroAfiliado'))
            ->get();

        foreach ($articulos as $articulo) {
            $detalle = new PedidoMedicamentoDetail();
            $detalle->pedido_medicamento_id = $pedido->id;
            $detalle->articuloZafiro_id = $articulo->id_articulo;
            $detalle->cantidad = $articulo->cantidad;
            $detalle->presentacion = ArticulosZafiro::where('id_articulo', $articulo->id_articulo)->value('presentacion_completa');

            $detalles[] = $detalle;

        }

        // Guardar los detalles en la base de datos
        foreach ($detalles as $detalle) {
            $detalle->save();
        }
        // Redireccionar a una página de éxito o mostrar un mensaje de éxito
        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue creada con éxito!","success");

    }






}
