@extends('crudbooster::admin_template')


@section('content')

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
<div class="container-fluid">
    <h1>Validador de Farmacias</h1>
    <h2>Punto de retiro: FARMANOR VI </h2>
    <form method="POST" action="{{ route('validarAfiliado') }}">
        @csrf
        <div class="form-group">
            <label for="numeroAfiliado">Número de afiliado:</label>
            <input type="text" class="form-control" id="numeroAfiliado" name="numeroAfiliado" placeholder="Ingrese el número de afiliado" required>
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
            <table class="table">
                <thead>
                <tr>
                    <th>Nombre de Afiliado</th>
                    <th>Fecha de creación del Pedido Médico</th>
                    <th>Fecha de aprobación</th>
                    <th>Medicación Requerida</th>
                    <th>Cantidad autorizada</th>
                    <th>Cantidad entregada</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <!-- Aquí puedes generar las filas dinámicamente con tu backend -->
                @foreach($afiliado as $cotizacion)
                    @foreach($medicacion as $md)

                        <tr>
                    <td>{{ $cotizacion->nombreyapellido }}</td>
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
                        <input type="number" class="form-control" id="cantidadMedicacion" value="{{ $md->cantidad }}">
                    </td>
                    <td>
                        <select class="form-control" name="estadoPedido[]">
                            <option value="1">Entregado</option>
                            <option value="2">En revisión</option>
                            <option value="3">Rechazado</option>
                            <option value="4">En depósito</option>
                        </select>
                    </td>

                    </tr>
                    @endforeach

                @endforeach

                </tbody>

            </table>

        </div>
        <button type="submit" class="btn btn-success" onclick="mostrarAlerta()">Actualizar Datos</button>
        <button type="submit" class="btn btn-warning">Imprimir pedido entregado</button>


    </div>

</div>

<!-- Agrega los scripts de Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js">

</script>

<script>
    function mostrarAlerta() {
        alert("Actualizado correctamente");

    }
</script>

@endsection
