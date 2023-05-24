<?php

namespace App\Http\Controllers;

use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;
use Barryvdh\DomPDF\Facade as PDF;

class PDFController extends Controller
{
    public function generarPDF($id){

        // Crear una instancia de TCPDF

        $pedido = CotizacionConvenio::where('id', $id)->first();
        $detalles = CotizacionConvenioDetail::where('cotizacion_convenio_id', $id)->get();

        //$pdf = PDF::loadView('pdf.tabla', compact('pedido', 'detalles'));
        //return $pdf->download('cotizacion.pdf');

        return view('pdf.tabla', compact('pedido', 'detalles'));
    }
}
