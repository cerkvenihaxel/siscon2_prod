<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class MedicacionBusquedaController extends Controller
{

        public function showMeds($id){

            $dataid = $id;
            $data = DB::table('convenio_oficina_os')->where('id', $dataid)->get();
            $nroSolicitud = DB::table('convenio_oficina_os')->where('id', $dataid)->value('nrosolicitud');
            $nroAfiliado = DB::table('convenio_oficina_os')->where('id', $dataid)->value('nroAfiliado');

            //Datos del Paciente


            $nombrePaciente = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('apeynombres');
            $dniPaciente = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('documento');
            $localidadPaciente = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('localidad');
            $telefonoPaciente = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('telefonos');
            $edadPaciente = DB::table('pedido_medicamento')->where('nrosolicitud', $nroSolicitud)->value('edad');


            //Medicacion requerida
            $pedidoId = DB::table('pedido_medicamento')->where('nrosolicitud', $nroSolicitud)->value('id');
            $pedidoDetail = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $pedidoId)->get();

            return view('medicacionBusqueda', compact( 'data', 'nroSolicitud', 'dataid', 'nombrePaciente', 'dniPaciente', 'localidadPaciente', 'telefonoPaciente', 'edadPaciente', 'pedidoDetail'));

       // return view('medicacionBusqueda', compact( 'data','nombrePaciente', 'dniPaciente', 'obraSocialPaciente', 'nroAfiliadoPaciente', 'sexo', 'edad', 'medicacion', 'cantMedicacion'));


    }

}

