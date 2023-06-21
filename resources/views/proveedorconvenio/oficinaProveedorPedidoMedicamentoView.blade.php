@extends('crudbooster::admin_template')


@section('content')

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear solicitud</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>


<div class="tarjeta">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Pedidos por procesar</h3>
            </div>
            <div class="card-body">

                <table id="tabla-solicitudes" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Médico solicitante</th>
                        <th>Patología</th>
                        <th>Estado Solicitud</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($solicitudes->where('estado_solicitud_id', 3) as $solicitud)
                        <tr>
                            <td>{{$solicitud->created_at}}</td>
                            <td>{{ $solicitud->nroAfiliado }}</td>
                            <td>{{ $solicitud->nrosolicitud }}</td>
                            <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                            <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                            <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-success btn-xs m-5 btn-autorizar" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#modalAutorizar">
                                        <i class="fas fa-check"></i> Generar pedido
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs m-5 btn-rechazar" data-toggle="modal" data-pedido-id="{{ $solicitud->id }}" data-target="#confirmModal">
                                        <i class="fas fa-times"></i> Anular pedido
                                    </button>                                <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#pedidoModal">
                                        <i class="fas fa-eye"></i> Ver pedido
                                    </button>                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Imprimir pedido</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Agrega más filas según sea necesario -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="tarjeta2">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-autorizados">Pedidos enviados a depósito</h3>
            </div>
            <div class="card-body">
                <table id="tabla-solicitudes-cotizadas" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Medicación requerida</th>
                        <th>Estado Solicitud</th>
                        <th>Estado del pedido</th>
                        <th>Nro pedido</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($cotizadas->where('estado_solicitud_id', 6) as $cot)
                        <td>{{ $cot->created_at }}</td>
                        <td>{{ $cot->nombreyapellido}}</td>
                        <td>{{ $cot->nrosolicitud }}</td>
                        <td> Prueba </td>
                        <td>{{ DB::table('estado_solicitud')->where('id', $cot->estado_solicitud_id)->value('estado') }}</td>
                        <td>{{ DB::table('estado_pedido')->where('id', $cot->estado_pedido_id)->value('estado') }}</td>
                        <td>{{ $cot->id_pedido }} </td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Imprimir pedido</button>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                    <!-- Agrega más filas según sea necesario -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="tarjeta3">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-rechazados">Pedidos cancelados</h3>
            </div>
            <div class="card-body">
                <h3>Solicitudes</h3>
                <table id="tabla-solicitudes-anuladas" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Medicación requerida</th>
                        <th>Estado Solicitud</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($cotizadas->whereIn('estado_solicitud_id', [5, 9]) as $an)
                            <td>{{ $an->created_at }}</td>
                            <td>{{ $an->nombreyapellido }}</td>
                            <td>{{ $an->nrosolicitud }}</td>
                            <td>Medicación requerida</td>
                            <td>{{ DB::table('estado_solicitud')->where('id', $an->estado_solicitud_id)->value('estado') }}</td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                    <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Imprimir pedido</button>
                                </div>
                            </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    function openCenteredWindow(url, width, height) {
        var left = (window.screen.width - width) / 2;
        var top = (window.screen.height - height) / 2;
        var windowFeatures = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',menubar=no,toolbar=no,location=no,status=no';
        window.open(url, '_blank', windowFeatures);
    }

    var tablaSolicitudes;
    var tablaCotizaciones;
    var tablaCanceladas;

    $(document).ready(function() {
        // Inicializar DataTables y guardar la instancia en la variable tablaSolicitudes
        tablaSolicitudes = $('#tabla-solicitudes').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: false
        });

        tablaCotizaciones = $('#tabla-solicitudes-cotizadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: false
        });

        tablaCanceladas = $('#tabla-solicitudes-anuladas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: false
        });


    });



    function filtrarTabla() {
        var valorFiltro = $('#input-buscar-afiliado').val();

        tablaSolicitudes.search(valorFiltro).draw();
    }
</script>

<style>
    .tarjeta{
        background-color: white;
        border-radius: 12px;
        padding-bottom: 15px;
    }

    .card-title{
        color: #4285F4;
    }

    .card-title-autorizados{
        color: #00a65a;
    }

    .card-title-rechazados{
        color: #c41300;
    }

    .tarjeta2{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        padding-bottom: 15px;
    }

    .tarjeta3{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        padding-bottom: 15px;
    }

    .button-container {
        margin-top: 10px;
    }

    .button-container button {
        display: inline-block;
        margin-right: 3px;
        margin-top: 2px;

    }

</style>
</body>
</html>

@endsection
