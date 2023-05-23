@extends('crudbooster::admin_template')


@section('content')

@php
$puntoRetiro = DB::table('punto_retiro')->where('proveedor_convenio_id', 2)->get();
@endphp

    <!DOCTYPE html>
<html lang="es">
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

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="container-fluid">
    <h1>Validador de Farmacias</h1>

    <form method="POST" action="{{ route('validarAfiliado') }}">
        @csrf
        <div class="form-group">
            <label for="numeroAfiliado">Número de afiliado:</label>
            <input type="text" class="form-control" id="numeroAfiliado" name="numeroAfiliado" placeholder="Ingrese el número de afiliado" required>
        </div>

        <div class="form-group">
            <label for="puntoRetiro">Punto de Retiro:</label>
            <select class="form-control" id="puntoRetiro" name="puntoRetiro">
                @foreach($puntoRetiro as $opcion)
                        <option value="{{ $opcion->id }}">{{ $opcion->nombre }}</option>
                @endforeach
                <!-- Agrega más opciones de punto de retiro si es necesario -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>

    </form>

    <div class="card mt-4" @if (!empty($afiliado)) style="display: block;" @else style="display: none;" @endif>
        <div class="card-header">
            Resultados de la búsqueda
        </div>
        <div class="card-body">
            <h1 class="box-title">
                Medicación entregada
            </h1>
            <form method="POST" action="{{ route('actualizarDatos') }}">
                @csrf

                <table class="table table-responsive table-bordered">
                <thead>
                <tr>
                    <th>Nombre de Afiliado</th>
                    <th>Fecha de creación del Pedido Médico</th>
                    <th>Fecha de aprobación</th>
                    <th>Medicación Requerida</th>
                    <th>Cantidad autorizada</th>
                    <th>Cantidad entregada</th>
                    <th>Ultima fecha de actualizacion</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <!-- Aquí puedes generar las filas dinámicamente con tu backend -->
                @foreach($afiliado as $cotizacion)
                    @foreach($medicacion as $md)
                        @php
                            $estadoSeleccionado = DB::table('estado_pedido')->where('id', $cotizacion->estado_pedido_id)->value('estado');
                            $opcionesEstado = DB::table('estado_pedido')->get();
                        @endphp
                        <tr>
                    <td>{{ $cotizacion->nombreyapellido }}</td>
                            <input type="hidden" id="nombreAfiliado" value="{{ $cotizacion->nombreyapellido }}">

                            <td>{{ DB::table('pedido_medicamento')->where('nrosolicitud', $cotizacion->nrosolicitud)->value('created_at') }}</td>
                    <td>{{ $cotizacion->created_at }}</td>
                    <td>
                        <ul>
                            <li> {{ DB::table('articulosZafiro')->where('id', $md->articuloZafiro_id)->value('presentacion_completa') }}</li>
                        </ul>
                    </td>
                            <td>
                                {{$md->cantidad}}
                            </td>
                    <td>
                        <input type="number" class="form-control" name="cantidadMedicacion[]" value="{{ $md->cantidad_entregada }}">
                    </td>
                    <td>{{ $cotizacion->updated_at}} </td>
                    <td>
                        <select class="form-control-static" name="estadoPedido[]">
                            <option value="{{ $cotizacion->estado_pedido_id }}" selected style="color: #0A246A">
                                {{ DB::table('estado_pedido')->where('id', $cotizacion->estado_pedido_id)->value('estado') }}
                            </option>
                            @foreach($opcionesEstado as $opcion)
                                @if($opcion->id !== $cotizacion->estado_pedido_id)
                                    <option value="{{ $opcion->id }}">{{ $opcion->estado }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                            <input type="hidden" name="cotizacionConvenioDetailId[]" value="{{ $md->id }}">
                        </tr>
                    @endforeach

                @endforeach

                </tbody>

                </table>


                <button type="submit" class="btn btn-success" id="actualizarDatosBtn">Actualizar Datos</button>

            </form>

        </div>


    </div>


</div>

<!-- Agrega los scripts de Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js">

</script>



@endsection
