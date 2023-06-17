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
                <h3 class="card-title">Pedidos por autorizar</h3>
            </div>
            <div class="card-body">

                <div class="form-group">
                    <label for="input-buscar-afiliado">Buscar por: nombre, dni, número de afiliado, monodroga</label>
                </div>

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
                    @foreach($solicitudes->where('estado_solicitud_id', 1) as $solicitud)
                    <tr>
                        <td>{{$solicitud->created_at}}</td>
                        <td>{{ $solicitud->nroAfiliado }}</td>
                        <td>{{ $solicitud->nrosolicitud }}</td>
                        <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                        <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                        <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-success btn-xs m-5"><i class="fas fa-check"></i> Autorizar</button>
                                <button class="btn btn-danger btn-xs m-5"><i class="fas fa-times"></i> Rechazar</button>
                                <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#pedidoModal">
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

<!-- Solicitudes procesadas -->

<div class="tarjeta2">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-autorizados">Pedidos autorizados</h3>
            </div>
            <div class="card-body">

                <table id="tabla-solicitudes-autorizadas" class="table table-bordered">
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
                                    <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#pedidoModal">
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


<div class="tarjeta3">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-rechazados">Pedidos rechazados</h3>
            </div>
             <table id="tabla-solicitudes-rechazadas" class="table table-bordered">
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
                 @foreach($solicitudes->whereIn('estado_solicitud_id', [5,9]) as $solicitud)
                     <tr>
                         <td>{{$solicitud->created_at}}</td>
                         <td>{{ $solicitud->nroAfiliado }}</td>
                         <td>{{ $solicitud->nrosolicitud }}</td>
                         <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                         <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                         <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                         <td>
                             <div class="button-container">
                                 <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#pedidoModal">
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



<!-- Modal Ver pedido-->
<div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="pedidoModalLabel">Detalles del pedido</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Nombre afiliado</th>
                        <th>Número Afiliado</th>
                        <th>Número Solicitud</th>
                    </tr>
                    </thead>
                    <tbody id="pedidoDetalleBody">
                    <!-- Aquí se agregarán las filas con los detalles del pedido -->
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Médico prescriptor</th>
                        <th>Teléfono Afiliado</th>
                    </tr>
                    </thead>
                    <tbody id="pedidoDetalleBody2">
                    <!-- Aquí se agregarán las filas con los detalles del pedido -->
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Medicación requerida</th>
                        <th>Cantidad</th>
                    </tr>
                    </thead>
                    <tbody id="pedidoDetalleBodyMedicamento">
                    <!-- Aquí se agregarán las filas con los detalles del pedido -->
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
    var tablaSolicitudesRechazadas;
    var tablaSolicitudesAutorizadas;

    $(document).ready(function() {
        // Inicializar DataTables y guardar la instancia en la variable tablaSolicitudes
        tablaSolicitudes = $('#tabla-solicitudes').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: true
        });

        tablaSolicitudesRechazadas = $('#tabla-solicitudes-rechazadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: true
        });

        tablaSolicitudesAutorizadas = $('#tabla-solicitudes-autorizadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: true
        });

    });

    $(document).ready(function() {
        $(document).on('click', '.btn-ver-pedido', function() {
            var pedidoId = $(this).data('pedido-id');
            console.log(pedidoId);
            var url = '/pedido/' + pedidoId + '/detalle'; // Reemplaza la URL con la ruta correcta de tu aplicación

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#pedidoDetalleBody').empty();
                    $('#pedidoDetalleBody2').empty();
                    $('#pedidoDetalleBodyMedicamento').empty();



                        var pedido = response.pedido;
                    // Vaciar el contenido anterior del cuerpo del modal

                    // Agregar las filas con los detalles del pedido al cuerpo del modal
                        var nombre = response.nombre;
                        var nroAfiliado = pedido.nroAfiliado;
                        var nrosolicitud = pedido.nrosolicitud;
                        var nombremedico = response.nombremedico;

                        var fila = '<tr>' +
                            '<td>' + nombre + '</td>' +
                            '<td>' + nroAfiliado + '</td>' +
                            '<td>' + nrosolicitud + '</td>' +
                            '</tr>';

                        $('#pedidoDetalleBody').append(fila);

                    var fila2 = '<tr>' +
                        '<td>' + nombremedico + '</td>' +
                        '<td>' + pedido.tel_afiliado + '</td>' +
                        '</tr>';

                    $('#pedidoDetalleBody2').append(fila2);

                    var medicamentos = response.detalles;

                    for (var i = 0; i < medicamentos.length; i++) {
                        var medicamento = medicamentos[i];
                        var filaMedicamento = '<tr>' +
                            '<td>' + medicamento.presentacion + '</td>' +
                            '<td>' + medicamento.cantidad + '</td>' +
                            '</tr>';

                        $('#pedidoDetalleBodyMedicamento').append(filaMedicamento);
                    }


                    // Mostrar el modal
                    $('#pedidoModal').modal('show');
                },
                error: function(error) {
                    console.error(error);
                }
            });
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