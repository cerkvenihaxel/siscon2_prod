@extends('crudbooster::admin_template')
@section('content')

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear solicitud</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3/css/bootstrap.min.css">
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
            <!--<div class="button-container">
                <a href="/generarpedido/cargamasiva">
            <button class="btn btn-info btn-xs m-5 btn-autorizar">
                    <i class="fas fa-send"></i> Enviar varios pedidos a depósito
                </button>
                </a>
            </div> -->
            <div class="card-body">
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
                        <th>Proveedor</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    <!-- CAMBIAR EL ID SOLICITUD POR EL 4 DESPUES -->
                    @foreach($solicitudes->whereIn('estado_solicitud_id', [3, 4]) as $solicitud)
                        <tr>
                            <td>{{$solicitud->created_at}}</td>
                            <td> {{ $solicitud->nombre }}</td>
                            <td>{{ $solicitud->nroAfiliado }}</td>
                            <td>{{ $solicitud->nrosolicitud }}</td>
                            <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                            <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                            <td>{{ DB::table('estado_solicitud')->where('id',$solicitud->estado_solicitud_id)->value('estado') }}</td>
                            <td>{{ DB::table('proveedores_convenio')->where('id', $solicitud->proveedor)->value('nombre') }}</td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-success btn-xs m-5 btn-autorizar" data-pedido-id="{{ $solicitud->id }}" data-toggle="modal" data-target="#modalGenerar">
                                        <i class="fas fa-check"></i> Enviar pedido único a depósito
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs m-5 btn-rechazar" data-toggle="modal" data-pedido-id="{{ $solicitud->id }}" data-target="#confirmModal">
                                        <i class="fas fa-times"></i> Rechazar pedido
                                    </button>

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
                        <th>Estado Solicitud</th>
                        <th>Estado del pedido</th>
                        <th>Nro pedido</th>
                        <th>Nro Remito</th>
                        <th>Nro Factura</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($cotizadas->whereIn('estado_solicitud_id', [6, 11, 12]) as $cot)
                        <td>{{ $cot->created_at }}</td>
                        <td>{{ $cot->nombreyapellido}}</td>
                        <td>{{ $cot->nrosolicitud }}</td>
                        <td>{{ DB::table('estado_solicitud')->where('id', $cot->estado_solicitud_id)->value('estado') }}</td>
                        <td>{{ DB::table('estado_pedido')->where('id', $cot->estado_pedido_id)->value('estado') }}</td>
                        <td>{{ $cot->id_pedido }} </td>
                        <td>{{$cot->nro_remito}}</td>
                        <td>{{$cot->nro_factura}}</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-info btn-xs m-5 btn-ver-pedido-prov" data-pedido-id="{{ $cot->id }}" data-toggle="modal" data-target="#pedidoModal">
                                    <i class="fas fa-eye"></i> Ver pedido
                                </button>
                                <button class="btn btn-warning btn-xs mr-2">
                                    <a class="link-pdf" href="/generarPDF_convenio/{{$cot->id}}" target="_blank">
                                    <i class="fas fa-print"> </i> Imprimir pedido
                                    </a>
                                </button>

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


<!-- tarjeta entregados -->

<div class="tarjeta3">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-entregados">Pedidos entregados</h3>
            </div>
            <div class="card-body">
                <table id="tabla-solicitudes-entregados" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Estado Solicitud</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($solicitudes->whereIn('estado_pedido_id', [1]) as $en)
                            <td>{{ $en->created_at }}</td>
                            <td>{{ DB::table('afiliados')->where('nroAfiliado', $en->nroAfiliado)->value('apeynombres') }}</td>
                            <td>{{ $en->nrosolicitud }}</td>
                            <td>{{ DB::table('estado_solicitud')->where('id', $en->estado_solicitud_id)->value('estado') }}</td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $en->id }}" data-toggle="modal" data-target="#pedidoModal">
                                        <i class="fas fa-eye"></i> Ver pedido
                                    </button>

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


<!-- fin tarjeta entregados -->

<div class="tarjeta3">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-rechazados">Pedidos cancelados</h3>
            </div>
            <div class="card-body">
                <table id="tabla-solicitudes-anuladas" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Estado Solicitud</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($solicitudes->whereIn('estado_solicitud_id', [10]) as $an)
                            <td>{{ $an->created_at }}</td>
                            <td>{{ DB::table('afiliados')->where('nroAfiliado', $an->nroAfiliado)->value('apeynombres') }}</td>
                            <td>{{ $an->nrosolicitud }}</td>
                            <td>{{ DB::table('estado_solicitud')->where('id', $an->estado_solicitud_id)->value('estado') }}</td>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-info btn-xs m-5 btn-ver-pedido" data-pedido-id="{{ $an->id }}" data-toggle="modal" data-target="#pedidoModal">
                                        <i class="fas fa-eye"></i> Ver pedido
                                    </button>

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
                        <th>ID PEDIDO</th>
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
                <form action="{{ route('generarpedido.rechazar') }}" method="POST">
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

<!-- Modal generar pedido -->

<div class="modal fade full-screen-modal" id="modalGenerar" tabindex="-1" role="dialog" aria-labelledby="modalGenerarLabel">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalGenerarLabel">Generar pedido</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('generarpedido.guardar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <table id="tablaAutorizar" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Monodroga</th>
                            <th>Presentación</th>
                            <th>Laboratorio</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>SubTotal</th>
                            <th>Descuento</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody id="tablaAutorizarBody">
                        </tbody>
                    </table>

                    <div class="form-group">
                        <label for="zona_retiro">Zona retiro</label>
                        <input class="form-control-sm" name="zona_retiro" id="zona_retiro" readonly>
                    </div>

                    <div class="form-group">
                        <label for="punto_retiro">Punto de Retiro</label>
                        <select class="form-control" name="punto_retiro" id="punto_retiro"></select>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <input class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Ingrese las observaciones">
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

<script>
    $(document).ready(function() {
        $('.select2').select2();// Inicializar el select con Select2
        $('#punto_retiro').select2();

    });

    function openCenteredWindow(url, width, height) {
        var left = (window.screen.width - width) / 2;
        var top = (window.screen.height - height) / 2;
        var windowFeatures = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',menubar=no,toolbar=no,location=no,status=no';
        window.open(url, '_blank', windowFeatures);
    }

    var tablaSolicitudes;
    var tablaCotizaciones;
    var tablaEntregados;
    var tablaCanceladas;

    $(document).ready(function() {
        // Inicializar DataTables y guardar la instancia en la variable tablaSolicitudes
        tablaSolicitudes = $('#tabla-solicitudes').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: false,
            order: [[ 0, "desc" ]]

        });

        tablaCotizaciones = $('#tabla-solicitudes-cotizadas').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            lengthChange: false,
            info: false,
            order: [[ 0, "desc" ]]

        });

        tablaEntregados = $('#tabla-solicitudes-entregados').DataTable({
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
            info: false,
            order: [[ 0, "desc" ]]


        });


    });

    $(document).ready(function() {
        $(document).on('click', '.btn-ver-pedido', function() {
            var pedidoId = $(this).data('pedido-id');
            var url = '/generarpedido/' + pedidoId + '/detalle'; // Reemplaza la URL con la ruta correcta de tu aplicación

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


        $(document).on('click', '.btn-ver-pedido-prov', function() {
            var pedidoId = $(this).data('pedido-id');
            var url = '/generarpedido/' + pedidoId + '/detalleprov'; // Reemplaza la URL con la ruta correcta de tu aplicación

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
                    var nroPedido = pedido.id_pedido;

                    var fila = '<tr>' +
                        '<td>' + nombre + '</td>' +
                        '<td>' + nroAfiliado + '</td>' +
                        '<td>' + nrosolicitud + '</td>' +
                        '<td>' + nroPedido + '</td>' +
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
            var url = '/generarpedido/' + pedidoId + '/autorizar'; // Reemplaza la URL con la ruta correcta de tu aplicación


            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('response', response);
                    $('#tablaAutorizarBody').empty();

                    var medicamentos = response.medicamentos;
                    var pedido = response.pedido;
                    var zona_retiro = response.pedido.zona_residencia;
                    var nroSolicitudInput = '<input type="hidden" name="nroSolicitud" value="' + pedido.nrosolicitud + '">';
                    var nroAfiliadoInput = '<input type="hidden" name="nroAfiliado" value="' + pedido.nroAfiliado + '">';
                    var idCotizacion = '<input type="hidden" name="idCotizacion" value="' + pedido.id + '">';

                    $('#tablaAutorizarBody').append(nroSolicitudInput);
                    $('#tablaAutorizarBody').append(idCotizacion);
                    $('#tablaAutorizarBody').append(nroAfiliadoInput);
                    $('#zona_retiro').val(zona_retiro);



                    for (var i = 0; i < medicamentos.length; i++) {
                        var medicamento = medicamentos[i];
                        var filaMedicamento = '<tr>' +
                            '<input type="hidden" name="medicamentos[' + i + '][articuloZafiro_id]" value="' + medicamento.articuloszafiro_id + '">' +
                            '<input type="hidden" name="medicamentos[' + i + '][presentacion]" value="' + medicamento.presentacion + '">' +
                            '<input type="hidden" name="medicamentos[' + i + '][cantidad]" value="' + medicamento.cantidad + '">' +

                            '<td>' + (medicamento.des_monodroga ?? '') + '</td>' +
                            '<td>' + medicamento.presentacion + '</td>' +
                            '<td><input type="text"  name="medicamentos[' + i + '][laboratorio]" placeholder="Laboratorio" value="'+ medicamento.laboratorio + '"></td>' +
                            '<td>' +
                            '<input type="text" class="form-control" name="medicamentos[' + i + '][precio]" placeholder="Ingrese el precio" onchange="calculateTotalWithDiscount(' + i + ')" required value="'+(medicamento.precio ?? '') +'">' +
                            '</td>' + // PRECIO
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][cantidad]" value="' + medicamento.cantidad + '" onchange="calculateTotalWithDiscount(' + i + ')" readonly>' +
                            '</td>' + // CANTIDAD
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][subtotal]" placeholder="Subtotal" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input type="text" class="form-control" name="medicamentos[' + i + '][banda_descuento]" placeholder="Ingrese el descuento" onchange="calculateTotalWithDiscount(' + i + ')" value="' + (medicamento.banda_descuento ?? '')+'" required>' +
                            '</td>' +
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][total]" placeholder="Ingrese el total final" readonly>' +
                            '</td>' +
                            // TOTAL
                            '</tr>';

                        $('#tablaAutorizarBody').append(filaMedicamento);
                    }


                    var zonaResidencia = pedido.zonaRetiro.id


                    var puntosRetiro = response.puntosRetiro; // Suponiendo que obtienes los puntos de retiro en la respuesta AJAX
                    for (var j = 0; j < puntosRetiro.length; j++) {
                        var puntoRetiro = puntosRetiro[j];
                        var opcion = new Option(puntoRetiro.nombre, puntoRetiro.id);
                        $('#punto_retiro').append(opcion);
                    }



                    //$('#punto_retiro').val(zonaResidencia).trigger('change');



                    $('#punto_retiro').on('select2:select', function(e) {

                        var puntoRetiroSeleccionadoId = e.params.data.id;
                        var puntoRetiroSeleccionado = puntosRetiro.find(function(punto) {
                            return punto.id === puntoRetiroSeleccionadoId;
                        });

                        if (puntoRetiroSeleccionado) {
                            var direccion = puntoRetiroSeleccionado.direccion;
                            var localidad = puntoRetiroSeleccionado.localidad;
                            var telefono = puntoRetiroSeleccionado.telefono;

                            var puntoRetiroDes = direccion + " | " + localidad + " | " + telefono;
                            $('#punto_retiro_des').val(puntoRetiroDes);
                            $('#punto_retiro_des').trigger('change');
                        }
                    });



                    // Mostrar el modal
                    $('#modalGenerar').modal('show');
                },
                error: function(error) {
                    console.log('error', error);
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

    function calculateTotalWithDiscount(index) {
        var precioInput = document.getElementsByName('medicamentos[' + index + '][precio]')[0];
        var cantidadInput = document.getElementsByName('medicamentos[' + index + '][cantidad]')[0];
        var subtotalInput = document.getElementsByName('medicamentos[' + index + '][subtotal]')[0];
        var descuentoInput = document.getElementsByName('medicamentos[' + index + '][banda_descuento]')[0];
        var totalInput = document.getElementsByName('medicamentos[' + index + '][total]')[0];

        var precio = parseFloat(precioInput.value);
        var cantidad = parseFloat(cantidadInput.value);
        var subtotal = precio * cantidad;

        var descuento = parseFloat(descuentoInput.value);
        var total = subtotal - (subtotal * (descuento / 100));

        subtotalInput.value = subtotal.toFixed(2);
        totalInput.value = total.toFixed(2);
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
        color: #ec971f;
    }

    .card-title-entregados{
        color: #5cb85c;
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


    .full-screen-modal .modal-dialog {
        width: 100vw; /* Ocupa el 100% del ancho de la pantalla */
        margin: 0;
    }

    .full-screen-modal .modal-content {
        border-radius: 0;
    }

    .full-screen-modal {
        align-items: center;
        justify-content: center;
    }
    .link-pdf{
        text-decoration: none ;
        color: white;
    }

    .link-pdf:hover{
        text-decoration: none ;
        color: white;
    }

</style>
</body>
</html>

@endsection
