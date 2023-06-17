<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PedidoMedicamento;
use App\Models\PedidoMedicamentoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
