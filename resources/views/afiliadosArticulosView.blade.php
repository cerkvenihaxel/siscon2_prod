@extends('crudbooster::admin_template')

@section('content')
    <!DOCTYPE html>
<html>

<head>
    <!-- Agregar enlaces a Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Agregar enlace a jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="tabla-view">

<div class="container container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('afiliados_articulos.search') }}" method="POST" class="d-flex justify-content-between align-items-center">
                        @csrf
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, patologia o dni...">
                        </div>
                        <div class="ml-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus"></i> Añadir nuevo
                            </button>
                        </div>
                    </form>
                </div>
                <h4>Total de resultados: {{  }}</h4>
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
                                <td>{{ $afiliadoArticulo->nroAfiliado }}</td>
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
                    <h3>Chau amorcin nos vemos más tarde, te amo bai.</h3>
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
                <button type="button" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
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
