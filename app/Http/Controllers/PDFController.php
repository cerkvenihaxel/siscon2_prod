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

    public function printPDF($id){

        // Crear una instancia de TCPDF

        $pedido = CotizacionConvenio::where('id', $id)->first();
        $detalles = CotizacionConvenioDetail::where('cotizacion_convenio_id', $id)->get();
        $fecha = now();

        $pdf = PDF::loadView('pdf.tablaprint', compact('pedido', 'detalles'));

        // Establecer el tamaño de fuente deseado (por ejemplo, 10)
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'Arial');
        $pdf->getDomPDF()->set_option('fontHeightRatio', 0.7);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isJavascriptEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Establecer el tamaño de fuente
        $pdf->getDomPDF()->set_option('font_size', 10);

        return $pdf->stream('PEDIDO-SISCON-'.$pedido->nombreyapellido.' ('.$fecha.').pdf');
    }

    public function printFarmaciaPDF($id){

        $pedido = CotizacionConvenio::where('id', $id)->first();
        $detalles = CotizacionConvenioDetail::where('cotizacion_convenio_id', $id)->get();
        $fecha = now();

        $pdf = PDF::loadView('pdf.tablaprintacuse', compact('pedido', 'detalles'));

        // Establecer el tamaño de fuente deseado (por ejemplo, 10)
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'Arial');
        $pdf->getDomPDF()->set_option('fontHeightRatio', 0.7);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isJavascriptEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Establecer el tamaño de fuente
        $pdf->getDomPDF()->set_option('font_size', 10);

        return $pdf->stream('PEDIDO-SISCON-'.$pedido->nombreyapellido.' ('.$fecha.').pdf');

    }
}
