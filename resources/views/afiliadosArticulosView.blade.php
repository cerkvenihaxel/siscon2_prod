@extends('crudbooster::admin_template')
@section('content')
    <!DOCTYPE html>
<html>

<head>
    <title>Precarga Afiliados</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, patologia...">
                        </div>
                        <div class="ml-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>

                    </form>
                    <button type="button" class="btn btn-sm btn-warning" id="btn-actualizar-datos">Actualizar datos</button>
                    <button class="btn btn-success btn-sm botoncarga" type="button" onclick="openCenteredWindow('/addNewPrecarga', 750, 550)">Agregar datos</button>

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
                            <th>Proveedor asignado</th>
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
                                <td>{{DB::table('proveedores_convenio')->where('id', $afiliadoArticulo->proveedores_convenio_id)->value('nombre')}}</td>

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

        $('#btn-actualizar-datos').click(function() {
            location.reload();
        });

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



    function openCenteredWindow(url, width, height) {
        var left = (window.screen.width - width) / 2;
        var top = (window.screen.height - height) / 2;
        var windowFeatures = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',menubar=no,toolbar=no,location=no,status=no';
        window.open(url, '_blank', windowFeatures);
    }

</script>

<style>
    .tabla-view{
        background-color: white;
        border-radius: 15px;
        padding-bottom: 15px;
        padding-top:20px;
    }

    .ml-3{
        margin-left: 2.5rem !important;
        margin-bottom: 3.5rem;
    }

    .botoncarga a{
        color: white;
    }

</style>
</body>

</html>

@endsection
