<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuscadorAfiliadoConvenioController extends Controller
{

    public function buscarAfiliadoMed(Request $request){

        $nroAfiliado = $request->input('busqueda');
        $data = DB::table('afiliados_articulos')->where('nro_afiliado', $nroAfiliado)->get();
        
        return view('buscadorAfiliadoConvenio', compact('data'));
    
    }

    
    //
}
