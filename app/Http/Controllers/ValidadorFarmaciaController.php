<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;

class ValidadorFarmaciaController extends Controller
{
    //Controller para realizar la validación de las farmacias.

public function index()
    {
        $afiliado = [];
        return view('validadorFarmacia', compact('afiliado'));
    }

    public function validarAfiliado(Request $request)
    {
        $numeroAfiliado = $request->input('numeroAfiliado');
        // Verificar si el número de afiliado está presente
        if (!empty($numeroAfiliado)) {
            // Realizar la lógica para obtener los datos de la base de datos
            // y pasarlos a la vista

            $afiliado = CotizacionConvenio::with(['convenio'])->where('nroAfiliado', $numeroAfiliado)->get();

            if ($afiliado) {
                // Obtener los datos de medicación relacionados con el número de afiliado
                $afiliado = CotizacionConvenio::where('nroAfiliado', $numeroAfiliado)->get();
                foreach ($afiliado as $af) {
                    $medicacion = CotizacionConvenioDetail::with(['entranteConvenio'])->where('cotizacion_convenio_id', $af->id)->get();
                }
                return view('validadorFarmacia', compact('afiliado', 'medicacion'));
            }
        }

        // Si no se encuentra el número de afiliado o no hay datos de medicación,
        // puedes redirigir a una página de error o mostrar un mensaje de error

        return view('validadorFarmacia')->with('error', 'No se encontraron datos para el número de afiliado proporcionado.');
    }



}
