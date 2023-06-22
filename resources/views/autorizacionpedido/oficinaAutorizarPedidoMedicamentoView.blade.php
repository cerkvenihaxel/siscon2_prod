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
                        <th>Nombre afiliado</th>
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
                        <td>{{$solicitud->nombre}}</td>
                        <td>{{ $solicitud->nroAfiliado }}</td>
                        <td>{{ $solicitud->nrosolicitud }}</td>
                        <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                        <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                        <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-success btn-xs m-5 btn-autorizar" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#modalAutorizar">
                                    <i class="fas fa-check"></i> Autorizar
                                </button>
                                <button type="button" class="btn btn-danger btn-xs m-5 btn-rechazar" data-toggle="modal" data-pedido-id="{{ $solicitud->id }}" data-target="#confirmModal">
                                    <i class="fas fa-times"></i> Rechazar
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
                        <th>Fecha de actualización</th>
                        <th>Nombre afiliado</th>
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
                            <td>{{$solicitud->updated_at}}</td>
                            <td>{{ $solicitud->nombre }}</td>
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
                     <th>Fecha de actualizacion</th>
                     <th>Nombre afiliado</th>
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
                         <td>{{$solicitud->updated_at}}</td>
                            <td>{{ $solicitud->nombre }}</td>
                         <td>{{ $solicitud->nroAfiliado }}</td>
                         <td>{{ $solicitud->nrosolicitud }}</td>
                         <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                         <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                         <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                         <td>
                             <div class="button-container">
                                 <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#pedidoModal">
                                     <i class="fas fa-eye"></i> Ver pedido
                                 </button>

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



<!-- Modal Ver pedido -->
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

<!-- Modal autorizar pedido -->

<div class="modal fade" id="modalAutorizar" tabindex="-1" role="dialog" aria-labelledby="modalAutorizarLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAutorizarLabel">Autorizar solicitud</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pedido.guardar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <table id="tablaAutorizar" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID Articulo</th>
                            <th>Presentación</th>
                            <th>Cantidad</th>
                            <th>Banda de descuento</th>
                            <th>Seleccionar proveedor</th>
                        </tr>
                        </thead>
                        <tbody id="tablaAutorizarBody">
                        </tbody>
                    </table>
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <input class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Ingrese las observaciones"></input>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal rechazar pedido -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Cabecera del modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Rechazo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Contenido del modal -->
            <div class="modal-body">
                <p>¿Estás seguro de que deseas anular esta solicitud?</p>
            </div>

            <!-- Pie del modal -->
            <div class="modal-footer">
                <!-- Botón "Confirmar" -->
                <form action="{{ route('pedido.rechazar') }}" method="POST">
                    @csrf
                    <input type="hidden" id="pedidoIdInput" name="pedidoId" value="">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger">Confirmar</button>
                    </div>
                </form>
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
            info: true,
            order: [[0, 'desc']] // Ordenar por la primera columna (created_at) en orden descendente

        });

        tablaSolicitudesRechazadas = $('#tabla-solicitudes-rechazadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: true,
            order: [[0, 'desc']] // Ordenar por la primera columna (created_at) en orden descendente

        });

        tablaSolicitudesAutorizadas = $('#tabla-solicitudes-autorizadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: true,
            order: [[0, 'desc']] // Ordenar por la primera columna (created_at) en orden descendente

        });

    });

    $(document).ready(function() {
        $(document).on('click', '.btn-ver-pedido', function() {
            var pedidoId = $(this).data('pedido-id');
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

        $(document).on('click', '.btn-autorizar', function() {
            var pedidoId = $(this).data('pedido-id');
            var url = '/pedido/' + pedidoId + '/autorizar'; // Reemplaza la URL con la ruta correcta de tu aplicación

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#tablaAutorizarBody').empty();

                    var medicamentos = response.medicamentos;
                    var pedido = response.pedido;

                    var nroSolicitudInput = '<input type="hidden" name="nroSolicitud" value="' + pedido.nrosolicitud + '">';
                    var nroAfiliadoInput = '<input type="hidden" name="nroAfiliado" value="' + pedido.nroAfiliado + '">';

                    $('#tablaAutorizarBody').append(nroSolicitudInput);
                    $('#tablaAutorizarBody').append(nroAfiliadoInput);

                    for (var i = 0; i < medicamentos.length; i++) {
                        var medicamento = medicamentos[i];
                        var filaMedicamento = '<tr>' +
                            '<input type="hidden" name="medicamentos[' + i + '][articuloZafiro_id]" value="' + medicamento.articuloZafiro_id + '">' +
                            '<input type="hidden" name="medicamentos[' + i + '][presentacion]" value="' + medicamento.presentacion + '">' +
                            '<input type="hidden" name="medicamentos[' + i + '][cantidad]" value="' + medicamento.cantidad + '">' +

                            '<td>' + medicamento.articuloZafiro_id + '</td>' +
                            '<td>' + medicamento.presentacion + '</td>' +
                            '<td>' + medicamento.cantidad + '</td>' +
                            '<td>' +
                            '<input type="number" step="0.01" class="form-control" name="medicamentos[' + i + '][banda_descuento]" placeholder="Ingrese un número decimal" value="52">' +
                            '</td>' +
                            '<td>' +
                            '<select class="form-control select2 form-control-lg" name="medicamentos[' + i + '][proveedor_convenio_id]" placeholder="Proveedor">' +
                            '<option value="1">Global Médica</option>' +
                            '<option value="2">Zcienza</option>' +
                            '<option value="3">Red Farma</option>' +
                            '</select>' +
                            '</td>' +
                            '</tr>';

                        $('#tablaAutorizarBody').append(filaMedicamento);
                    }


                    // Mostrar el modal
                    $('#modalAutorizar').modal('show');
                },
                error: function(error) {
                    console.error(error);
                }
                });
            });

    });

    $(document).ready(function() {
        $('#confirmModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var pedidoId = button.data('pedido-id'); // Obtener el valor del atributo data-pedido-id
            var modal = $(this);

            // Asignar el valor al campo oculto del formulario
            modal.find('#pedidoIdInput').val(pedidoId);
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
