@extends('crudbooster::admin_template')
@section('content')

    <!DOCTYPE html>
<html>
<head>
    <title>Generar pedido</title>
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

<div class="container">
    <h1>Pedidos</h1>

    <table id="pedidos-table" class="table table-striped">
        <thead>
        <tr>
            <th><input type="checkbox" id="check-all"></th>
            <th>Fecha de creacion</th>
            <th>Nombre de afiliado</th>
            <th>Numero de solicitud</th>
            <th>Medico Solicitante</th>
            <th>Patologia</th>
            <th>Estado Solicitud</th>
            <th>Proveedor</th>
            <th>Zona retiro</th>
        </tr>
        </thead>
        <tbody>
        <!-- CAMBIAR EL ID SOLICITUD POR EL 4 DESPUES -->
        @foreach($solicitudes as $solicitud)
            <tr>
                <td><input type="checkbox" name="pedido[]" value="{{ $solicitud->id }}"></td>
                <td>{{ $solicitud->created_at }}</td>
                <td>{{ $solicitud->nombre }}</td>
                <td>{{ $solicitud->nroAfiliado }}</td>
                <td>{{ DB::table('medicos')->where('id', $solicitud->medicos_id)->value('nombremedico') }}</td>
                <td>{{ DB::table('patologias')->where('id', $solicitud->patologia)->value('nombre') }}</td>
                <td>{{ DB::table('estado_solicitud')->where('id', $solicitud->estado_solicitud_id)->value('estado') }}</td>
                <td>{{ DB::table('proveedores_convenio')->where('id', $solicitud->proveedor)->value('nombre') }}</td>
                <td>{{ $solicitud->zona_residencia }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>

    <button id="enviar-pedido-masivo" class="btn btn-primary" data-toggle="modal" data-target="#pedidoModal">Enviar pedido masivo</button>

</div>

<!-- Modal para mostrar los IDs seleccionados -->
<div class="modal fade full-screen-modal" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-xl"  role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('generarpedido.guardarmasivo') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <table id="tablaAutorizar" class="table table-bordered">
                            <thead>
                            <tr>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar el datatable
        $('#pedidos-table').DataTable({
            "paging": true,
            "pageLength": 10,
            "filter": true,
            "order": [[ 1, "desc" ]],
        });

        // Agregar evento de clic al botón "Check-All"
        $('#check-all').click(function() {
            $('input[name="pedido[]"]').prop('checked', this.checked);
        });

        $('#enviar-pedido-masivo').click(function() {
            var checkboxes = document.getElementsByName('pedido[]');
            var selectedPedidos = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selectedPedidos.push(checkboxes[i].value);
                }
            }
            // Enviar la solicitud AJAX
            $.ajax({
                url: '{{ route("enviar.pedido.masivo") }}',
                method: 'POST',
                data: { selected_ids: selectedPedidos },

                success: function(response) {


                    console.log(response);
                    $('#tablaAutorizarBody').empty();

                    // Obtener un arreglo de los valores de 'medicamentos'
                    var medicamentosArray = Object.values(response.medicamentos);
                    var pedido = response.pedido;

                    var nroSolicitudArray = response.nroSolicitud;
                    var nroAfiliadoArray = response.nroAfiliado;
                    var idsArray = response.ids;

                    for (var h = 0; h < idsArray.length; h++){
                        var id = idsArray[h];
                        var idInput = '<input type="hidden" name="id[]" value="' + id + '">';
                        $('#tablaAutorizarBody').append(idInput);
                    }


                    for (var k = 0; k < nroAfiliadoArray.length; k++){
                        var nroAfiliado = nroAfiliadoArray[k].afiliados_id;
                        var nroAfiliadoInput = '<input type="hidden" name="nroAfiliado[]" value="' + nroAfiliado + '">';
                        $('#tablaAutorizarBody').append(nroAfiliadoInput);
                    }

                    // Iterar sobre el arreglo 'nroSolicitudArray'

                    for (var j = 0; j < nroSolicitudArray.length; j++) {
                        var nroSolicitud = nroSolicitudArray[j].nrosolicitud;
                        var nroSolicitudInput = '<input type="hidden" name="nroSolicitud[]" value="' + nroSolicitud + '">';
                        $('#tablaAutorizarBody').append(nroSolicitudInput);
                    }


                    // Iterar sobre el arreglo 'medicamentosArray'
                    for (var i = 0; i < medicamentosArray.length; i++) {
                        var medicamento = medicamentosArray[i][0];
                        console.log(medicamento);
                        var filaMedicamento =

                            '<tr>' +
                            '<input type="hidden" name="medicamentos[' + i + '][articuloZafiro_id]" value="' + medicamento.articuloszafiro_id + '">' +
                            '<td>' + medicamento.presentacion + '</td>' +
                            '<td><input type="text"  name="medicamentos[' + i + '][laboratorio]" placeholder="Laboratorio"></td>' +
                            '<td>' +
                            '<input type="text" class="form-control" name="medicamentos[' + i + '][precio]" placeholder="Ingrese el precio" onchange="calculateTotalWithDiscount(' + i + ')">' +
                            '</td>' + // PRECIO
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][cantidad]" value="' + medicamento.cantidad_total + '" onchange="calculateTotalWithDiscount(' + i + ')" readonly>' +
                            '</td>' + // CANTIDAD
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][subtotal]" placeholder="Subtotal" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input type="text" class="form-control" name="medicamentos[' + i + '][banda_descuento]" placeholder="Ingrese el descuento" onchange="calculateTotalWithDiscount(' + i + ')" value="' + medicamento.banda_descuento + '">' +
                            '</td>' +
                            '<td>' +
                            '<input type="number" class="form-control" name="medicamentos[' + i + '][total]" placeholder="Ingrese el total final" readonly>' +
                            '</td>' +
                            // TOTAL
                            '</tr>';

                        $('#tablaAutorizarBody').append(filaMedicamento);
                    }

                    var puntosRetiro = response.puntosRetiro; // Suponiendo que obtienes los puntos de retiro en la respuesta AJAX
                    for (var j = 0; j < puntosRetiro.length; j++) {
                        var puntoRetiro = puntosRetiro[j];
                        var opcion = new Option(puntoRetiro.nombre, puntoRetiro.id);
                        $('#punto_retiro').append(opcion);
                    }


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
                },
                error: function(xhr, status, error) {
                    console.error(error, status, xhr);
                }
            });
        });
    });

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

    .container{
        background-color: white;
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

</style>
</body>
</html>

@endsection
