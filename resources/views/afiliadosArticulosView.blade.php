@extends('crudbooster::admin_template')

@section('content')
    <!DOCTYPE html>
<html>

<head>
    <!-- Agregar enlaces a Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Agregar enlaces a Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Select2 -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Agregar enlace a jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="tabla-view">

<div class="container container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="box-title pb-8">Precarga de medicamentos</h3>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('afiliados_articulos.search') }}" method="POST" class="d-flex justify-content-between align-items-center">
                        @csrf
                        <div class="form-group mb-4">
                            <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, patologia o dni...">
                        </div>
                        <div class="ml-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>

                    </form>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fas fa-plus"></i> Añadir nuevo
                    </button>
                </div>

                <h4>Total de resultados: {{ $count }}</h4>


                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Nro Afiliado</th>
                            <th>Nombre Afiliado</th>
                            <th>Articulo</th>
                            <th>Des Articulo</th>
                            <th>Presentacion</th>
                            <th>Cantidad</th>
                            <th>Patologias</th>
                            <th>Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($afiliadosArticulos as $afiliadoArticulo)
                            <tr>
                                <td>{{ $afiliadoArticulo->nro_afiliado }}</td>
                                <td>{{ $afiliadoArticulo->nombre }}</td>
                                <td>{{ $afiliadoArticulo->id_articulo }}</td>
                                <td>{{ $afiliadoArticulo->des_articulo }}</td>
                                <td>{{ $afiliadoArticulo->presentacion }}</td>
                                <td>{{ $afiliadoArticulo->cantidad }}</td>
                                <td>{{ DB::table('patologias')->where('id', $afiliadoArticulo->patologias)->value('nombre') }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm btn-view" data-id="{{ $afiliadoArticulo->id }}">Ver</button>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $afiliadoArticulo->id }}" data-toggle="modal" data-target="#editModal">Editar</button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $afiliadoArticulo->id }}" data-toggle="modal" data-target="#deleteModal">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination justify-content-center">
                        {{ $afiliadosArticulos->links() }}
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir nuevos datos -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Añadir nuevo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Select para afiliado -->
                <div class="form-group">
                    <label for="afiliado">Afiliado:</label>
                    <input type="text" id="afiliado" class="form-control" >
                </div>

                <!-- Select2 para artículos -->
                <div class="form-group">
                    <label for="articulo">Artículo:</label>
                    <input type="text" id="articulo" class="form-control" >
                </div>

                <!-- Select2 para patologías -->
                <div class="form-group">
                    <label for="patologia">Patología:</label>
                    <input type="text" id="patologia" class="form-control" >
                </div>

                <!-- Cantidad -->
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" class="form-control">
                </div>

                <!-- Botón para agregar fila -->
                <button type="button" class="btn btn-primary" id="btn-agregar-fila">Agregar Fila</button>

                <!-- Tabla para mostrar las filas agregadas -->
                <table class="table table-bordered" id="tabla-filas">
                    <thead>
                    <tr>
                        <th>Afiliado</th>
                        <th>Artículo</th>
                        <th>Patología</th>
                        <th>Cantidad</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Filas agregadas dinámicamente -->
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnAgregar">Agregar</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal para ver los detalles del afiliado -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Detalles del Afiliado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewModalContent">
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar los datos del afiliado -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Afiliado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="editModalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar los datos del afiliado -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Eliminar Afiliado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este afiliado?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-eliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>

    //Crear modal



    $('.btn-view').on('click', function() {
        var afiliadoId = $(this).data('id');
        var url = '{{ route("afiliadosarticulos.show", ":id") }}';
        url = url.replace(':id', afiliadoId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#viewModal .modal-body').html(response);
                $('#viewModal').modal('show');
            },
            error: function(xhr) {
                // Manejo de errores si es necesario
            }
        });
    });

    $('.btn-edit').on('click', function() {
        var afiliadoId = $(this).data('id');
        var url = '{{ route("afiliadosarticulos.edit", ":id") }}';
        url = url.replace(':id', afiliadoId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#editModal .modal-body').html(response);
                $('#editModal').modal('show');
            },
            error: function(xhr) {
                // Manejo de errores si es necesario
            }
        });
    });

    $(document).ready(function() {
        var afiliadoNumber = null; // Variable para almacenar el número de afiliado

        $('.btn-delete').on('click', function() {
            afiliadoNumber = $(this).data('id');
        });

        $('.btn-eliminar').on('click', function() {
            if (afiliadoNumber !== null) {
                var afiliadoId = afiliadoNumber;
                console.log(afiliadoId);
                var url = '{{ route("afiliados_articulos.destroy", ":id") }}';
                url = url.replace(':id', afiliadoId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        // Procesar la respuesta si es necesario
                        location.reload();
                    },
                    error: function(xhr) {
                        // Manejo de errores si es necesario
                    }
                });

                afiliadoNumber = null; // Restablecer el valor de afiliadoNumber después de utilizarlo
            }
        });
    });


    $(document).ready(function() {
        // Capturar el evento input del campo de búsqueda
        $('#search').on('input', function() {
            var searchText = $(this).val().toLowerCase();

            // Filtrar las filas de la tabla según el texto de búsqueda
            $('table tbody tr').each(function() {
                var rowText = $(this).text().toLowerCase();

                if (rowText.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });


    $(document).ready(function() {
        // Arreglo para almacenar las filas agregadas
        var filasAgregadas = [];

        // Función para agregar una nueva fila
        function agregarFila() {
            // Obtener los valores seleccionados
            var afiliado = $('#afiliado').val();
            var articulo = $('#articulo').val();
            var patologia = $('#patologia').val();
            var cantidad = $('#cantidad').val();

            // Validar que se hayan seleccionado todos los campos
            if (afiliado === '' || articulo === '' || patologia === '' || cantidad === '') {
                alert('Por favor, complete todos los campos.');
                return;
            }

            // Crear objeto de fila
            var fila = {
                afiliado: afiliado,
                articulo: articulo,
                patologia: patologia,
                cantidad: cantidad
            };

            // Agregar la fila al arreglo de filas
            filasAgregadas.push(fila);

            // Limpiar los campos de entrada
            limpiarCampos();

            // Actualizar la tabla
            actualizarTabla();
        }

        // Función para limpiar los campos de entrada
        function limpiarCampos() {
            $('#afiliado').val('');
            $('#articulo').val('');
            $('#patologia').val('');
            $('#cantidad').val('');
        }

        // Función para actualizar la tabla con las filas agregadas
        function actualizarTabla() {
            var tabla = $('#tabla-filas tbody');

            // Limpiar la tabla
            tabla.empty();

            // Agregar las filas al cuerpo de la tabla
            filasAgregadas.forEach(function(fila) {
                var tr = '<tr>' +
                    '<td>' + fila.afiliado + '</td>' +
                    '<td>' + fila.articulo + '</td>' +
                    '<td>' + fila.patologia + '</td>' +
                    '<td>' + fila.cantidad + '</td>' +
                    '<td><button class="btn btn-eliminar-fila" data-afiliado="' + fila.afiliado + '">Eliminar</button></td>' +
                    '</tr>';
                tabla.append(tr);
            });

            // Asignar evento de eliminación a los botones de eliminar fila
            $('.btn-eliminar-fila').click(function() {
                var afiliado = $(this).data('afiliado');
                eliminarFila(afiliado);
            });
        }

        // Función para eliminar una fila del arreglo de filas
        function eliminarFila(afiliado) {
            filasAgregadas = filasAgregadas.filter(function(fila) {
                return fila.afiliado !== afiliado;
            });

            // Actualizar la tabla
            actualizarTabla();
        }

        // Evento del botón "Agregar Fila"
        $('#btn-agregar-fila').click(function() {
            agregarFila();
        });

        // Evento del botón "Agregar"
        $('#btnAgregar').click(function() {
            // Enviar los datos al controlador (puedes cambiar la URL según tu configuración)
            $.ajax({
                url: '/guardar-filas',
                type: 'POST',
                dataType: 'json',
                data: { filas: filasAgregadas },
                success: function(response) {
                    // Manejar la respuesta del controlador
                    if (response.success) {
                       console.log(filasAgregadas);
                        // Limpiar el arreglo de filas agregadas
                        filasAgregadas = [];
                        // Limpiar la tabla
                        actualizarTabla();
                        // Cerrar el modal
                        $('#addModal').modal('hide');
                    } else {
                        alert('Error al agregar las filas. Inténtelo nuevamente.');
                    }
                },
                error: function() {
                    alert('Error de conexión. Inténtelo nuevamente.');
                }
            });
        });
    });


    //Get Afiliado

    $(document).ready(function() {
        $('#afiliado_id').select2({
            ajax: {
                url: '/afiliados/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    var options = [];

                    // Generar las opciones del select
                    data.forEach(function(item) {
                        options.push({
                            id: item.id,
                            text: item.name
                        });
                    });

                    // Retornar las opciones generadas
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Mostrar resultados después de escribir al menos 2 caracteres
        });
    });




</script>

<style>
    .tabla-view{
        background-color: white;
        border-radius: 15px;
        box-shadow: #0a0a0a 15px;
        padding-bottom: 15px;
        padding-top:20px;
    }

    .ml-3{
        margin-left: 2 .5rem !important;
        margin-bottom: 3.5rem;
    }

</style>
</body>

</html>
@endsection
