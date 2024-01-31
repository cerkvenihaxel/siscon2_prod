<?php

namespace App\Http\Controllers\Backoffice\GlobalMedica;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use App\Models\ArticulosZafiro;
use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;
use App\Models\LinPedido;
use App\Models\PedidoC;
use App\Models\PedidoMedicamento;
use Barryvdh\Debugbar\Facades\Debugbar;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class PedidosConvenio extends Controller
{
    public function index()
    {
        $elementos = session('elementos', []);
        $farmacias = DB::table('punto_retiro')->get();
        //dd($elementos);
        return view('backoffice.global.pedidos-convenio.index', compact('elementos', 'farmacias'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'articulo' => 'required',
            'cantidad' => 'required',
        ]);

        $requ = $request->input('articulo');
        $precio = DB::table('articulosZafiro')->where('id_articulo', $requ)->value('pcio_vta_siva');
        $descuento = DB::table('banda_descuentos')->where('id_articulo', $requ)->value('banda_descuento') ?? 0;
        $total = $precio * $request->input('cantidad');
        $total = $total - ($total * $descuento / 100);
        // Crea un nuevo elemento en forma de arreglo JSON
        $elemento = [
            'nro_articulo' => $request->input('articulo'),
            'presentacion' => ArticulosZafiro::where('id_articulo', $requ)->first()->presentacion_completa,
            'monodroga' => ArticulosZafiro::where('id_articulo', $requ)->first()->des_monodroga,
            'laboratorio' => DB::table('banda_descuentos')->where('id_articulo', $requ)->value('laboratorio') ?? '',
            'precio' => round($precio, 2),
            'cantidad' => $request->input('cantidad'),
            'subtotal' => $precio * $request->input('cantidad'),
            'descuento' => round($descuento,2),
            'total' => round($total,2),
        ];



        // Obtén el arreglo JSON actual de elementos almacenado en la sesión
        $elementos = session('elementos', []);

        // Agrega el nuevo elemento al arreglo JSON
        $elementos[] = $elemento;

        // Almacena el arreglo JSON actualizado en la sesión
        session(['elementos' => $elementos]);

        // Redirige a la página de índice
       return redirect()->route('generar-pedido.index')->with('success', 'Elemento agregado exitosamente.');
    }

    public function enviarPedido(Request $request) : RedirectResponse
    {

        $request->validate([
            'farmacia' => 'required',
            'observaciones' => 'required'
        ]);

        $elementos = session('elementos', []);
        $punto_retiro = $request->input('farmacia');
        $observaciones = $request->input('observaciones');
        $myID = CRUDBooster::myId();
        $stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');
        $nroAfiliado = '00000000010';
        $nroSolicitud = 'APOS-MED-M-' . date('dmHis');



        $proveedorConvenio = new CotizacionConvenio();
        $proveedorConvenio->nombreyapellido = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('apeynombres');
        $proveedorConvenio->documento = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('documento');
        $proveedorConvenio->nroAfiliado = $nroAfiliado;
        $proveedorConvenio->edad = 27;
        $proveedorConvenio->nrosolicitud = $nroSolicitud;
        $proveedorConvenio->clinicas_id = 1;
        $proveedorConvenio->medicos_id = 1;
        $proveedorConvenio->zona_residencia = 1;
        $proveedorConvenio->tel_afiliado = Afiliados::where('nroAfiliado', $nroAfiliado)->value('telefonos');
        $proveedorConvenio->email = Afiliados::where('nroAfiliado', $nroAfiliado)->value('email');
        $proveedorConvenio->fecha_receta = date('Y-m-d');
        $proveedorConvenio->postdatada = 2;
        $proveedorConvenio->fecha_vencimiento = date('Y-m-d', strtotime('+1 month'));
        $proveedorConvenio->estado_solicitud_id = 11;
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
        foreach ($elementos as $med) {
            $proveedorConvenioDetail = new CotizacionConvenioDetail();
            $proveedorConvenioDetail->cotizacion_convenio_id = $proveedorConvenio->id;
            $proveedorConvenioDetail->articuloZafiro_id = $med['nro_articulo'];
            $proveedorConvenioDetail->cantidad = $med['cantidad'];
            $proveedorConvenioDetail->descuento = $med['descuento'];
            $proveedorConvenioDetail->laboratorio = $med['laboratorio'];
            $proveedorConvenioDetail->presentacion = $med['presentacion'];
            $proveedorConvenioDetail->precio = $med['precio'];
            $proveedorConvenioDetail->total = $med['total'];

            $proveedorConvenioD[] = $proveedorConvenioDetail;

        }

        $proveedorConvenio->oficinaAutorizarDetail()->saveMany($proveedorConvenioD);
        $this->enviarPedidoSingular($proveedorConvenio->id);

        session()->forget('elementos');

        // Redirige a la página de índice o a donde desees
        return redirect()->route('generar-pedido.index')->with('success', 'Pedido enviado y sesión limpiada.');
    }

    public function editarArticulo(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'cantidad' => 'required|numeric',
        ]);

        $index = $request->input('index');

        // Obtén el arreglo JSON actual de elementos almacenado en la sesión
        $elementos = session('elementos', []);

        // Actualiza la cantidad del artículo en el arreglo
        $elementos[$index]['cantidad'] = $request->input('cantidad');
        $elementos[$index]['laboratorio'] = $request->input('laboratorio');
        $elementos[$index]['descuento'] = $request->input('descuento');

        $subtotal = $elementos[$index]['precio'] * $request->input('cantidad');
        $elementos[$index]['subtotal'] = round($subtotal,2);
        $total = $elementos[$index]['precio'] * $request->input('cantidad');
        $total = $total - ($total * $request->input('descuento') / 100);
        $elementos[$index]['total'] = round($total,2);
        // Almacena el arreglo JSON actualizado en la sesión
        session(['elementos' => $elementos]);

        // Redirige a la página de índice
        return redirect()->route('generar-pedido.index')->with('success', 'Cantidad del artículo actualizada.');
    }

    public function eliminarArticulo($index): \Illuminate\Http\RedirectResponse
    {
        // Obtén el arreglo JSON actual de elementos almacenado en la sesión
        $elementos = session('elementos', []);

        // Elimina el artículo del arreglo
        unset($elementos[$index]);

        // Reindexa el arreglo para evitar índices vacíos
        $elementos = array_values($elementos);

        // Almacena el arreglo JSON actualizado en la sesión
        session(['elementos' => $elementos]);

        // Redirige a la página de índice
        return redirect()->route('generar-pedido.index')->with('success', 'Artículo eliminado.');
    }

    public function buscarArticulos(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->input('search');

        $articulos = ArticulosZafiro::where('presentacion_completa', 'LIKE', '%' . $search . '%')
            ->limit(10) // Limita la cantidad de resultados para evitar cargar demasiados datos.
            ->get();

        return response()->json($articulos);
    }

    public function vaciar () : RedirectResponse {
        session()->forget('elementos');
        return redirect()->route('generar-pedido.index')->with('success', 'Se vació el pedido');
    }

    public function getPrecio ($nroArticulo) {
        $precio = ArticulosZafiro::where('id_articulo', $nroArticulo)->first()->precio;
        return response()->json($precio);
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


        session()->forget('elementos');

        // Redirige a la página de índice o a donde desees
        return redirect()->route('generar-pedido.index')->with('success', 'Pedido enviado y sesión limpiada.');

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



}
