<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;
use App\Models\LinPedido;
use App\Models\OficinaAutorizar;
use App\Models\OficinaAutorizarDetail;
use App\Models\PedidoC;
use App\Models\PedidoMedicamento;
use App\Models\PedidoMedicamentoDetail;
use App\Models\ProveedoresConvenio;
use App\Models\ProveedoresConvenioDetail;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProveedorConvenioOficina extends Controller
{
    //
    public function index(){

        $solicitudes = OficinaAutorizar::all();
        $cotizadas = CotizacionConvenio::all();

        foreach ($solicitudes as $solicitud) {
            $solicitud->nombre = DB::table('afiliados')->where('id', $solicitud->afiliados_id)->value('apeynombres');
        }

        return view('proveedorconvenio.oficinaProveedorPedidoMedicamentoView', compact('solicitudes', 'cotizadas'));
    }

    public function verPedido($id)
    {
        $pedido =  OficinaAutorizar::findOrFail($id);
        $convenio_oficina_os_id = OficinaAutorizar::findOrFail($id)->id;
        $detalles = OficinaAutorizarDetail::where('convenio_oficina_os_id', $convenio_oficina_os_id)->get();
        $nombre = DB::table('afiliados')->where('id', $pedido->afiliados_id)->value('apeynombres');
        $nombremedico = DB::table('medicos')->where('id', $pedido->medicos_id)->value('nombremedico');

        return response()->json([
            'pedido' => $pedido,
            'detalles' => $detalles,
            'nombre' => $nombre,
            'nombremedico' => $nombremedico
        ]);
    }

    public function verPedidoProveedor($id)
    {
        $pedido =  ProveedoresConvenio::findOrFail($id);
        $pedido_medicamento_id = DB::table('convenio_oficina_os')->where('nrosolicitud', $pedido->nrosolicitud)->value('id');
        $detalles = OficinaAutorizarDetail::where('convenio_oficina_os_id', $pedido_medicamento_id)->get();
        $nombre = $pedido->nombreyapellido;
        $nombremedico = DB::table('medicos')->where('id', $pedido->medicos_id)->value('nombremedico');


        return response()->json([
            'pedido' => $pedido,
            'detalles' => $detalles,
            'nombre' => $nombre,
            'nombremedico' => $nombremedico
        ]);
    }

    public function rechazarPedido(Request $request){
        $id = $request->input('pedidoId');
        $pedido = OficinaAutorizar::findOrFail($id);

        $pedido->estado_solicitud_id = 10;
        $pedido->save();

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue rechazada con éxito!","success");

    }

    public function autorizarVerPedido($id)
    {
        $nroSolicitud = OficinaAutorizar::findOrFail($id)->nrosolicitud;
        $pedidos = OficinaAutorizar::findOrFail($id); // Hago un get de todos los pedidos con ese ID
        $medicamentos = OficinaAutorizarDetail::where('convenio_oficina_os_id', $id)->get();

        $puntosRetiro = DB::table('punto_retiro')->get(); // Hago un get de todas las sucursales
        $pedidos->zonaRetiro = DB::table('punto_retiro')->where('nombre', 'LIKE', '%' . $pedidos->zona_residencia . '%')->first('id');
        // Le agrego una variable zonaRetiro al array de los pedidos

        foreach ($medicamentos as $medicamento) {
            $articuloZafiroID = $medicamento->articuloszafiro_id; // 57400 INT
            $numeroArticulo =  str_pad($articuloZafiroID, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda
            $descripcionMonodroga = DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('des_monodroga');
            $medicamento->des_monodroga = $descripcionMonodroga;
        }
        // Realiza las operaciones necesarias para obtener los detalles del pedido y la oficina de autorización
        $response = [
            'medicamentos' => $medicamentos,
            'pedido' => $pedidos,
            'puntosRetiro'=>$puntosRetiro,
        ];
        return response()->json($response);
    }

    public function autorizarGuardarPedido(Request $request){

        $medicamentos = $request->input('medicamentos');
        $nroSolicitud = $request->input('nroSolicitud');
        $nroAfiliado = $request->input('nroAfiliado');
        $punto_retiro = $request->input('punto_retiro');
        $observaciones = $request->input('observaciones');
        $idCotizacion = $request->input('idCotizacion');
        $myID = CRUDBooster::myId();
        $stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');

        $proveedorConvenio = new CotizacionConvenio();
        $proveedorConvenio->nombreyapellido = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('apeynombres');
        $proveedorConvenio->documento = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('documento');
        $proveedorConvenio->nroAfiliado = $nroAfiliado;
        $proveedorConvenio->edad = DB::table('pedido_medicamento')->where('nrosolicitud', $nroSolicitud)->value('edad');
        $proveedorConvenio->nrosolicitud = $nroSolicitud;
        $proveedorConvenio->clinicas_id = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('clinicas_id');
        $proveedorConvenio->medicos_id = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('medicos_id');
        $proveedorConvenio->zona_residencia = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('zona_residencia');
        $proveedorConvenio->tel_afiliado = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('tel_afiliado');
        $proveedorConvenio->email = Afiliados::where('nroAfiliado', $nroAfiliado)->value('email');
        $proveedorConvenio->fecha_receta = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('fecha_receta');
        $postdatada = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('postdatada');
        $proveedorConvenio->postdatada = DB::table('postdatada')->where('id', $postdatada)->value('cantidad');
        $proveedorConvenio->fecha_vencimiento = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('fecha_vencimiento');
        $proveedorConvenio->estado_solicitud_id = 11;
        $proveedorConvenio->discapacidad = PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->value('discapacidad');
        $proveedorConvenio->estado_pedido_id = 5;
        $proveedorConvenio->punto_retiro_id = $punto_retiro;
        $proveedorConvenio->proveedor = DB::table('proveedores_convenio')->where('id', 2)->value('nombre');
        $proveedorConvenio->stamp_user = $stamp_user;
        $proveedorConvenio->observaciones = $observaciones;
        $proveedorConvenio->direccion_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('direccion');
        $proveedorConvenio->localidad_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('localidad');
        $proveedorConvenio->tel_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('telefono');

        $proveedorConvenio->save();


        $proveedorConvenioD = [];
        foreach ($medicamentos as $med) {
            $proveedorConvenioDetail = new CotizacionConvenioDetail();
            $proveedorConvenioDetail->cotizacion_convenio_id = $proveedorConvenio->id;
            $proveedorConvenioDetail->articuloZafiro_id = $med['articuloZafiro_id'];
            $proveedorConvenioDetail->cantidad = $med['cantidad'];
            $proveedorConvenioDetail->descuento = $med['banda_descuento'];
            $proveedorConvenioDetail->laboratorio = $med['laboratorio'];
            $proveedorConvenioDetail->presentacion = $med['presentacion'];
            $proveedorConvenioDetail->precio = $med['precio'];
            $proveedorConvenioDetail->total = $med['total'];

            $proveedorConvenioD[] = $proveedorConvenioDetail;

        }

        $proveedorConvenio->oficinaAutorizarDetail()->saveMany($proveedorConvenioD);
        OficinaAutorizar::where('id', $idCotizacion)->update(['estado_solicitud_id' => 11]);
        PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 11]);

        $this->enviarPedidoSingular($proveedorConvenio->id);
        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue autorizada con éxito!","success");

    }



    public function enviarPedidoSingular($id){

        $numero = $this->generatePedidoNumber();
        DB::table('cotizacion_convenio')->where('id', $id)->update(['id_pedido' => $numero]);
        DB::table('cotizacion_convenio')->where('id', $id)->update(['estado_pedido_id' => 5]);
        $nroSolicitud = DB::table('cotizacion_convenio')->where('id', $id)->value('nrosolicitud');
        $observaciones = CotizacionConvenio::where('id', $id)->value('observaciones');
        $nroAfiliado = CotizacionConvenio::where('id', $id)->value('nroAfiliado');

        $id_solicitud = $id;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $id_empresa = 2;
        $id_pedido = $numero;
        $fecha_pedido = date('Y-m-d H:i:s');
        $origen_id_sucursal = 99;
        $id_punto = DB::table('cotizacion_convenio')->where('id', $id_solicitud)->value('punto_retiro_id');
        $id_cliente = DB::table('punto_retiro')->where('id', $id_punto)->value('id_cliente');

        $linpedidos = DB::table('cotizacion_convenio_detail')->where('cotizacion_convenio_id', $id_solicitud)->get();

        $lin_pedidos = [];

        foreach ($linpedidos as $key => $linpedido) {

            $articuloID = $linpedido->articuloZafiro_id;
            $numeroArticulo =  str_pad($articuloID, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda

            $lin_pedidos[] = [
                'created_at' => $fecha_pedido,
                'updated_at' => $fecha_pedido,
                'id_pedido' => $id_pedido,
                'item' => $key+1,
                'id_articulo' => $numeroArticulo,
                'cantidad' => $linpedido->cantidad,
                'des_articulo' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('des_articulo'),
                'presentacion' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('presentacion'),
                'pcio_vta_unisiva' => $linpedido->precio,
                'pcio_iva_comsiva' => $linpedido->total,
            ];
        }

        $objeto = [
            [
                "id" => 1,
                "created_at" => $created_at,
                "updated_at" => $created_at,
                "id_empresa" => $id_empresa,
                "id_pedido" => $id_pedido,
                "estado_pedido" => "EM",
                "fecha_pedido" => $fecha_pedido,
                "_origen_id_sucursal" => $origen_id_sucursal,
                "id_cliente" => $id_cliente,
                "lin_pedido" => $lin_pedidos

            ]
        ];


        $pedido = new PedidoC();
        $pedido->created_at = $created_at;
        $pedido->updated_at = $updated_at;
        $pedido->id_empresa = $id_empresa;
        $pedido->id_pedido = $id_pedido;
        $pedido->fecha_pedido = $fecha_pedido;
        $pedido->estado_pedido = 'EM'; // Estado "EM" = "Enviado a Mostrador
        $pedido->_origen_id_sucursal = $origen_id_sucursal;
        $pedido->id_cliente = $id_cliente; // Valor va cambiando conforme el cliente
        $pedido->observaciones = $observaciones;
        $pedido->nrosolicitud = $nroSolicitud;
        $pedido->nroAfiliado = $nroAfiliado;
        $pedido->save();

// Insertar en la tabla lin_pedido
        foreach ($objeto[0]['lin_pedido'] as $linpedido) {
            $linPedido = new LinPedido();
            $linPedido->created_at = $linpedido['created_at'];
            $linPedido->updated_at = $linpedido['updated_at'];
            $linPedido->id_pedido = $linpedido['id_pedido'];
            $linPedido->item = $linpedido['item'];
            $linPedido->id_articulo = $linpedido['id_articulo'];
            $linPedido->cantidad = $linpedido['cantidad'];
            $linPedido->des_articulo = $linpedido['des_articulo'];
            $linPedido->presentacion = $linpedido['presentacion'];
            $linPedido->pcio_vta_uni_siva = $linpedido['pcio_vta_unisiva'];
            $linPedido->pcio_com_uni_siva = $linpedido['pcio_iva_comsiva'];
            $linPedido->save();
        }


        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"El pedido fue cargado con éxito!","success");

    }

    private function generatePedidoNumber()
    {
        $lastPedido = PedidoC::latest()->first();

        if ($lastPedido) {
            $lastNumber = substr($lastPedido->id_pedido, 7); // Suponiendo que el número de pedido siempre comienza con "PC-"
            $newNumber = str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT); // Incrementa el número y rellena con ceros a la izquierda
        } else {
            $newNumber = '00000001'; // Si no hay pedidos anteriores, comienza desde el número 1
        }

        return 'PE0090-' . $newNumber;
    }

    //Carga masiva index

    public function indexCM(){
        $solicitudes = OficinaAutorizar::all();
        foreach ($solicitudes as $solicitud) {
            $solicitud->nombre = DB::table('afiliados')->where('id', $solicitud->afiliados_id)->value('apeynombres');
        }
        return view('proveedorconvenio.cargaMasiva', compact('solicitudes'));
    }

    public function cargaMasivaProv(Request $request): \Illuminate\Http\JsonResponse
    {
        // Obtener los IDs seleccionados del modal
        $ids = $request->input('selected_ids');

        $medicamento = OficinaAutorizarDetail::whereIn('convenio_oficina_os_id', $ids)
            ->groupBy('articuloszafiro_id', 'presentacion')
            ->select('presentacion','articuloszafiro_id',\DB::raw('SUM(cantidad) as cantidad_total'), \DB::raw('MAX(banda_descuento) as banda_descuento'))
            ->get();

        $medicamentos = $medicamento->groupBy('articuloszafiro_id')->toArray();

        $pedido = OficinaAutorizar::whereIn('id', $ids)->get();
        $nroSolicitud = OficinaAutorizar::whereIn('id', $ids)->get('nrosolicitud');
        $nroAfiliado = OficinaAutorizar::whereIn('id', $ids)->get('afiliados_id');
        $puntosRetiro = DB::table('punto_retiro')->get(); // Hago un get de todas las sucursales


        $response = [
            'medicamentos' => $medicamentos,
            'pedido' => $pedido,
            'ids' => $ids,
            'nroSolicitud' => $nroSolicitud,
            'nroAfiliado' => $nroAfiliado,
            'puntosRetiro' => $puntosRetiro
        ];

        return response()->json($response);
    }

    public function autorizarGuardarPedidoMasivo(Request $request){

        $medicamentos = $request->input('medicamentos');
        $nroSolicitud = $request->input('nroSolicitud');
        $punto_retiro = $request->input('punto_retiro');
        $observaciones = $request->input('observaciones');
        $idCotizacion = $request->input('id');
        $myID = CRUDBooster::myId();
        $stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');

        $pedidoMedicamento = OficinaAutorizar::whereIn('id', $idCotizacion)->get();
        $pedidoMedicamentoDetail = OficinaAutorizarDetail::whereIn('convenio_oficina_os_id', $idCotizacion)->get();


        $idsPedidos = [];

        foreach ($pedidoMedicamento as $pm) {
            $proveedorConvenio = new CotizacionConvenio();
            $proveedorConvenio->nombreyapellido = DB::table('afiliados')->where('nroAfiliado', $pm->nroAfiliado)->value('apeynombres');
            $proveedorConvenio->documento = DB::table('afiliados')->where('nroAfiliado', $pm->nroAfiliado)->value('documento');
            $proveedorConvenio->nroAfiliado = $pm->nroAfiliado;
            $proveedorConvenio->edad = $pm->edad;
            $proveedorConvenio->nrosolicitud = $pm->nrosolicitud;
            $proveedorConvenio->clinicas_id = $pm->clinicas_id;
            $proveedorConvenio->medicos_id = $pm->medicos_id;
            $proveedorConvenio->zona_residencia = $pm->zona_residencia;
            $proveedorConvenio->tel_afiliado = $pm->tel_afiliado;
            $proveedorConvenio->email = Afiliados::where('nroAfiliado', $pm->nroAfiliado)->value('email');
            $proveedorConvenio->fecha_receta = $pm->fecha_receta;
            $postdatada = $pm->postdatada;
            $proveedorConvenio->postdatada = DB::table('postdatada')->where('id', $postdatada)->value('cantidad');
            $proveedorConvenio->fecha_vencimiento = $pm->fecha_vencimiento;
            $proveedorConvenio->estado_solicitud_id = 11;
            $proveedorConvenio->discapacidad = $pm->discapacidad;
            $proveedorConvenio->estado_pedido_id = 5;
            $proveedorConvenio->punto_retiro_id = $punto_retiro;
            $proveedorConvenio->proveedor = DB::table('proveedores_convenio')->where('id', 2)->value('nombre');
            $proveedorConvenio->stamp_user = $stamp_user;
            $proveedorConvenio->observaciones = $observaciones;
            $proveedorConvenio->direccion_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('direccion');
            $proveedorConvenio->localidad_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('localidad');
            $proveedorConvenio->tel_retiro = DB::table('punto_retiro')->where('id', $punto_retiro)->value('telefono');

            $proveedorConvenio->save();

            $idPedido = $proveedorConvenio->id;
            $idsPedidos[] = $idPedido;

        }

        $proveedorConvenioD = [];


        foreach ($pedidoMedicamentoDetail as $pmd) {

                $proveedorConvenioDetail = new CotizacionConvenioDetail();
                $articuloZafiro_id = $pmd->articuloszafiro_id;

                foreach ($medicamentos as $med) {

                    // Verificar si se encontró el medicamento en el arreglo 'medicamentos'
                    if ($med['articuloZafiro_id'] == $articuloZafiro_id) {
                        $proveedorConvenioDetail->articuloZafiro_id = $articuloZafiro_id;
                        $proveedorConvenioDetail->cantidad = $med['cantidad'];
                        $proveedorConvenioDetail->descuento = $med['banda_descuento'];
                        $proveedorConvenioDetail->laboratorio = $med['laboratorio'];
                        $proveedorConvenioDetail->presentacion = $med['presentacion'];
                        $proveedorConvenioDetail->precio = $med['precio'];
                        // Calcular el subtotal
                        $subtotal = $proveedorConvenioDetail->cantidad * $proveedorConvenioDetail->precio;
                        // Aplicar el descuento
                        $descuento = ($subtotal * $proveedorConvenioDetail->descuento) / 100;
                        $proveedorConvenioDetail->total = $subtotal - $descuento;

                    }
                }

                $proveedorConvenioD[] = $proveedorConvenioDetail;
                $proveedorConvenioDetail->save();
        }

        //$idsPedidos = Esto es un array con los ids de los pedidos que se crearon
        //$proveedorConvenioD = Esto es un array con los detalles de los pedidos que se crearon


        OficinaAutorizar::whereIn('id', $idCotizacion)->update(['estado_solicitud_id' => 11]);
        PedidoMedicamento::whereIn('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 11]);
        //$this->enviarPedidoSingular($proveedorConvenio->id);

        $this->enviarPedidoMultiple($idsPedidos, $proveedorConvenioD, $punto_retiro);

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue autorizada con éxito!","success");

    }

    //ENVIAR PEDIDO MULTIPLE

    public function enviarPedidoMultiple($id, $provDetail, $ptoRetiro){

        $numero = $this->generatePedidoNumber();


        DB::table('cotizacion_convenio')->whereIn('id', $id)->update(['id_pedido' => $numero]);
        DB::table('cotizacion_convenio')->whereIn('id', $id)->update(['estado_pedido_id' => 5]);
        //$nroSolicitud = DB::table('cotizacion_convenio')->whereIn('id', $id)->value('nrosolicitud');
        $observaciones = CotizacionConvenio::where('id', $id)->value('observaciones');

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $id_empresa = 2;
        $id_pedido = $numero;
        $fecha_pedido = date('Y-m-d H:i:s');
        $origen_id_sucursal = 99;
        $id_punto = $ptoRetiro;
        $id_cliente = DB::table('punto_retiro')->where('id', $id_punto)->value('id_cliente');

        $linpedidos = $provDetail;

        $lin_pedidos = [];

        foreach ($linpedidos as $key => $linpedido) {

            $articuloID = $linpedido->articuloZafiro_id;
            $numeroArticulo =  str_pad($articuloID, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda

            $lin_pedidos[] = [
                'created_at' => $fecha_pedido,
                'updated_at' => $fecha_pedido,
                'id_pedido' => $id_pedido,
                'item' => $key+1,
                'id_articulo' => $numeroArticulo,
                'cantidad' => $linpedido->cantidad,
                'des_articulo' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('des_articulo'),
                'presentacion' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('presentacion'),
                'pcio_vta_unisiva' => $linpedido->precio,
                'pcio_iva_comsiva' => $linpedido->total,
            ];
        }

        $objeto = [
            [
                "id" => 1,
                "created_at" => $created_at,
                "updated_at" => $created_at,
                "id_empresa" => $id_empresa,
                "id_pedido" => $id_pedido,
                "estado_pedido" => "EM",
                "fecha_pedido" => $fecha_pedido,
                "_origen_id_sucursal" => $origen_id_sucursal,
                "id_cliente" => $id_cliente,
                "lin_pedido" => $lin_pedidos

            ]
        ];


        $pedido = new PedidoC();
        $pedido->created_at = $created_at;
        $pedido->updated_at = $updated_at;
        $pedido->id_empresa = $id_empresa;
        $pedido->id_pedido = $id_pedido;
        $pedido->fecha_pedido = $fecha_pedido;
        $pedido->estado_pedido = 'EM'; // Estado "EM" = "Enviado a Mostrador
        $pedido->_origen_id_sucursal = $origen_id_sucursal;
        $pedido->id_cliente = $id_cliente; // Valor va cambiando conforme el cliente
        $pedido->observaciones = $observaciones;
        //$pedido->nrosolicitud = $nroSolicitud;
        $pedido->save();

// Insertar en la tabla lin_pedido
        foreach ($objeto[0]['lin_pedido'] as $linpedido) {
            $linPedido = new LinPedido();
            $linPedido->created_at = $linpedido['created_at'];
            $linPedido->updated_at = $linpedido['updated_at'];
            $linPedido->id_pedido = $linpedido['id_pedido'];
            $linPedido->item = $linpedido['item'];
            $linPedido->id_articulo = $linpedido['id_articulo'];
            $linPedido->cantidad = $linpedido['cantidad'];
            $linPedido->des_articulo = $linpedido['des_articulo'];
            $linPedido->presentacion = $linpedido['presentacion'];
            $linPedido->pcio_vta_uni_siva = $linpedido['pcio_vta_unisiva'];
            $linPedido->pcio_com_uni_siva = $linpedido['pcio_iva_comsiva'];
            $linPedido->save();
        }

        CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"El pedido fue cargado con éxito!","success");

    }

}
