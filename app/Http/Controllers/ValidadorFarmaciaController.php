<?php

namespace App\Http\Controllers;

use http\Url;
use Illuminate\Http\Request;
use App\Models\CotizacionConvenio;
use App\Models\CotizacionConvenioDetail;
use Illuminate\Support\Facades\DB;

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
        $idPuntoRetiro = $request->input('puntoRetiro');
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

    public function actualizarDatos(Request $request)
    {
        // Obtener los datos enviados desde el formulario
        $cantidadMedicacion = $request->input('cantidadMedicacion');
        $estadoPedido = $request->input('estadoPedido');
        // Obtener el ID del registro de cotizacion_convenio_detail correspondiente
        $cotizacionConvenioDetailId = $request->input('cotizacionConvenioDetailId');
        $nombreAfiliado = $request->input('nombreAfiliado');


        // Realizar la actualización de los datos en la tabla cotizacion_convenio_detail
        foreach ($cotizacionConvenioDetailId as $index => $id) {
            $cantidad = $cantidadMedicacion[$index];

            // Actualizar la cantidad en la tabla cotizacion_convenio_detail
            DB::table('cotizacion_convenio_detail')
                ->where('id', $id)
                ->update([
                    'cantidad_entregada' => $cantidad
                ]);

            // Obtener el ID de la cotización
            $cotizacionId = DB::table('cotizacion_convenio_detail')
                ->where('id', $id)
                ->value('cotizacion_convenio_id');

            // Actualizar el estado en la tabla cotizacion_convenio
            DB::table('cotizacion_convenio')
                ->where('id', $cotizacionId)
                ->update([
                    'estado_pedido_id' => $estadoPedido[$index]
                ]);
        }
        // Retornar una respuesta exitosa
        return redirect()->to('admin/validador_farmacia')->with('success', 'Datos del afiliado actualizados correctamente.');
    }


}

