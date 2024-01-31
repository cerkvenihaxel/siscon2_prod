<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrante;
use Illuminate\Support\Facades\DB;
use App\Models\Necesidad;
use App\Enums\NecesidadEnums;
use App\Models\LinPedido;
use App\Models\PedidoC;

		class ApiPedidoController extends Controller
        {

         public function obtenerPedidos(){
             $pedidos = PedidoC::all();

             //obtengo las lineas de pedido para cada pedido

             foreach($pedidos as $pedido){
                 $lineasPedido = LinPedido::where('id_pedido', $pedido->id_pedido)->get();
                 $pedido->lin_pedido = $lineasPedido;
             }

                return response()->json($pedidos);

         }

        }
