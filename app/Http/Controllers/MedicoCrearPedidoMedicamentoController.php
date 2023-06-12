<?php

namespace App\Http\Controllers;

use App\Models\Afiliados;
use Illuminate\Http\Request;
use App\Models\AfiliadosArticulos;


class MedicoCrearPedidoMedicamentoController extends Controller
{
    public function buscarAfiliado(Request $request)
    {
        $search = $request->input('numeroAfiliado');
        $solicitud = AfiliadosArticulos::with('afiliadonumber')->get();
        $nombre = $solicitud;
        dd($nombre);

        //return view('medicoCrearpedidoMedicamentoview', compact('search', 'solicitud'));
    }
}
