<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrante;
use Illuminate\Support\Facades\DB;
use App\Models\Necesidad;

		class ApiDepositoController extends Controller
        {

            public function solicitudesProtesis(Request $request){


                    // Obtener las solicitudes con las relaciones cargadas
                    $solicitudes = Entrante::with(['clinicas', 'medicos', 'estado_paciente', 'necesidad'])
                        ->get();

                    // Mapear los datos y devolverlos como respuesta
                    $data = $solicitudes->sortByDesc('created_at')->map(function ($solicitud) {
                        return [
                            'NroSolicitud' => $solicitud->nrosolicitud,
                            'FechaSolicitud' => $solicitud->created_at,
                            'NroAfiliado' => $solicitud->nroAfiliado,
                            'NroClinica' => $solicitud->clinicas->id,
                            'NombreClinica' => $solicitud->clinicas->nombre,
                            'Matricula' => $solicitud->medicos->matricula,
                            'Medico' => $solicitud->medicos->nombremedico,
                            'IdEstadoPaciente' => $solicitud->estado_paciente_id,
                            'EstadoPaciente' => $solicitud->estado_paciente->estado,
                            'idNecesidad' => $solicitud->necesidad,
                            'Necesidad' => Necesidad::find($solicitud->necesidad)->necesidad, // Revisar esto, para que haga la relación en menor tiempo
                            'FechaCirugia' => $solicitud->fecha_cirugia,
                            'idCentro' => $solicitud->clinicas->id,
                            'Centro' => $solicitud->clinicas->nombre,
                            'idAccidente' => $solicitud->accidente,
                            'Accidente' => $solicitud->accidente,
                            'Observacion' => $solicitud->observaciones,
                            'idUsuario' => '1',
                            'Usuario' => 'S/U',
                        ];
                    });

                    // Devolver los datos como respuesta en formato JSON
                    return response()->json($data);
            }
        }
