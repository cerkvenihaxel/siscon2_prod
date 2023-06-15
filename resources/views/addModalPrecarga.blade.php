@php
    $patologias = DB::table('patologias')->get();
 @endphp

<!DOCTYPE html>
<html>
<head>
    <title>Precarga SISCON</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        $(document).ready(function() {
            $('.search-select').select2({
                placeholder: 'Search...',
                ajax: {
                    url: '{{ route("afiliados.search") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            $('.search-select2').select2({
                placeholder: 'Search...',
                ajax: {
                    url: '{{ route("articulos.search") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
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
                    nro_afiliado: afiliado,
                    id_articulo: articulo,
                    patologias: patologia,
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
                filasAgregadas.forEach(function(fila, index) {
                    var idex = index+1;
                    var tr = '<tr>' +
                        '<td>' + idex +'</td>' +
                        '<td>' + fila.nro_afiliado + '</td>' +
                        '<td>' + fila.id_articulo + '</td>' +
                        '<td>' + fila.patologias + '</td>' +
                        '<td>' + fila.cantidad + '</td>' +
                        '<td><button class="btn btn-danger btn-eliminar-fila" data-index="' + index + '">Eliminar</button></td>' +
                        '</tr>';
                    tabla.append(tr);
                });

                // Asignar evento de eliminación a los botones de eliminar fila
                $('.btn-eliminar-fila').click(function() {
                    var afiliado = $(this).data('index');
                    filasAgregadas.splice(afiliado, 1);
                    actualizarTabla();
                });
            }


            // Evento del botón "Agregar Fila"
            $('#btn-agregar-fila').click(function() {
                agregarFila();
                console.log(filasAgregadas);

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
                            alert('Filas agregadas correctamente.');
                            window.close();
                            // Cerrar el modal
                        } else {
                            alert('Error al agregar las filas. Inténtelo nuevamente.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText); // Imprimir el error en la consola
                        // Mostrar el error en tu página web
                        $('#error-message').text(jqXHR.responseText);
                    }
                });
            });
        });

    </script>
</head>
<body>

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addModalLabel">Añadir nuevo pedido</h5>
        </div>
        <div class="modal-body">
            <!-- Select para afiliado -->
            <div class="form-group">
                <label for="afiliado">Afiliado:</label>
                <select id="afiliado" class="form-control select2 search-select" style="width: 300px;"></select>
            </div>
            <!-- Select2 para artículos -->
            <div class="form-group">
                <label for="articulo">Artículo:</label>
                <select id="articulo" class="form-control search-select2" style="width: 300px;"></select>
            </div>

            <!-- Select2 para patologías -->
            <div class="form-group">
                <label for="patologia">Patología:</label>
                <select id="patologia" class="form-control">
                    @foreach($patologias as $patologia)
                        <option value="{{$patologia->id}}">{{$patologia->nombre}}</option>
                    @endforeach
                </select>
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
                    <th>ID</th>
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
            <button type="submit" class="btn btn-success" id="btnAgregar">Agregar</button>
        </div>

    </div>
</div>
</div>
</body>
</html>
