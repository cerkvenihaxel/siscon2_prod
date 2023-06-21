<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CotizacionConvenio;
use App\Models\OficinaAutorizar;
use Illuminate\Http\Request;

class ProveedorConvenioOficina extends Controller
{
    //
    public function index(){

        $solicitudes = OficinaAutorizar::all();
        $cotizadas = CotizacionConvenio::all();

        return view('proveedorconvenio.oficinaProveedorPedidoMedicamentoView', compact('solicitudes', 'cotizadas'));
    }
}
