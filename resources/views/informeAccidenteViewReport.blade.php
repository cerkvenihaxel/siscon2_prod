@extends('crudbooster::admin_template')

@section('content')



    @if(count($data)>0)

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <!-- Font Awesome CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

            <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
            <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
        </head>

        <body>
        <div class="card">
            <div class="card-header">
                <h4>Reporte solicitudes tipo : ACCIDENTE</h4>
                <h5 class="card-subtitle mb-2 text-muted">Periodo de consulta : {{$start_date}} | {{$end_date}}</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Tabla de datos</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fecha de carga</th>
                            <th>Nombre y apellido Afiliado</th>
                            <th>Nro Afiliado</th>
                            <th>Clínica</th>
                            <th>Edad</th>
                            <th>Teléfono</th>
                            <th>Médico</th>
                            <th>Estado Paciente</th>
                            <th>Estado Solicitud</th>
                            <th>Fecha Cirugía</th>
                            <th>Sufrió Accidente</th>
                            <th>Necesidad</th>
                            <th>Grupo Artículos</th>
                            <th>Fecha de expiración</th>
                            <th>Nro Solicitud</th>
                            <th>Monto total</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($data as $d)
                        <tr>
                            <td>{{ $d->created_at }}</td>
                            <td>{{ $nombre = DB::table('afiliados')->where('id', $d->afiliados_id)->value('apeynombres') }}</td>
                            <td>{{ $d->nroAfiliado }}</td>
                            <td>{{ $nomClinica = DB::table('clinicas')->where('id', $d->clinicas_id)->value('nombre') }}</td>
                            <td>{{ $d->edad }}</td>
                            <td>{{ $d->tel_afiliado }}</td>
                            <td>{{ $nomMedico = DB::table('medicos')->where('id', $d->medicos_id)->value('nombremedico') }}</td>
                            <td>{{ $estado = DB::table('estado_paciente')->where('id', $d->estado_paciente_id)->value('estado') }}</td>
                            <td>{{ $estadoSolicitud = DB::table('estado_solicitud')->where('id', $d->estado_solicitud_id)->value('estado') }}</td>
                            <td>{{ $d->fecha_cirugia }}</td>
                            <td>{{ $d->accidente }}</td>
                            <td>{{ $nec = DB::table('necesidad')->where('id', $d->necesidad)->value('necesidad') }}</td>
                            <td>{{ $grupo = DB::table('grupos')->where('id_grupo', $d->grupo_articulos)->value('des_grupo') }}</td>
                            <td>{{ $d->fecha_expiracion }}</td>
                            <td>{{ $d->nrosolicitud }}</td>
                            @php
                                $monto = DB::table('cotizaciones')->where('nrosolicitud', $d->nrosolicitud)->whereIn('estado_solicitud_id', [3, 4, 6])->value('total');
                                $precio_formateado = number_format($monto, 2, ',', '.');
                            @endphp
                            <td> $ {{ $precio_formateado }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        </body>
    @else
        <div class="alert alert-warning">No se pudieron encontrar datos</div>
    @endif

    <style>
        thead {
            background-color: #007bff;
            color: white;
        }
    </style>
@endsection

