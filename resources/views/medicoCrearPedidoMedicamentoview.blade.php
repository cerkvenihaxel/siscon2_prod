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
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>

<div class="tarjeta">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Crear receta</h3>
            </div>
            <div class="card-body">


                <form method="POST" action="{{ route('buscarAfiliadoPedido') }}">
                    @csrf

                    <div class="form-group">
                        <label for="select-obra-social">Obra social</label>
                        <select class="form-control select2" id="select-obra-social" disabled="true">
                            <option value="obra-social1">APOS</option>
                            <option value="obra-social2">Incluir Salud</option>
                            <option value="obra-social3">OSUNLaR</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="numeroAfiliado">Búsqueda por número de afiliado o DNI:</label>
                        <input type="text" class="form-control" id="numeroAfiliado" name="numeroAfiliado" placeholder="Ingrese el afiliado a buscar" required>
                    </div>

                    <div class="form-group">
                        <label for="select-patologia">Patologia</label>
                        <select class="form-control" name="patologia">
                            <option value="0">Ver todas</option>
                            @foreach($patologias as $pato)
                                <option value="{{ $pato->id }}">{{ $pato->nombre }}</option>
                            @endforeach

                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>


                @if($solicitud)
                    <hr>
                <div class="row">

                    <div class="col-sm-6">
                        <h3>Medicación del afiliado</h3>
                    </div>


                </div>


                <div class="row">
                    <div class="col-sm-3">
                            <h4>Nombre</h4>
                        <p>{{ $nombre }}</p>


                    </div>
                    <div class="col-sm-3">
                        <h4>Número de afiliado</h4>
                        <p>{{ $search }}</p>
                    </div>

                    <div class="col-sm-3">
                        <h4>Localidad</h4>
                        <p>{{$localidad}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h4>Teléfono</h4>
                        <p>{{ $telefono }}</p>
                    </div>
                </div>

                <h3>Medicación del afiliado</h3>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Medicación</th>
                        <th>Cantidad</th>
                        <th>Patologia</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($solicitud as $soli)
                        <td>{{ $soli->des_articulo }} | {{ $soli->presentacion }}</td>
                        <td>{{ $soli->cantidad }}</td>
                        <td>{{ DB::table('patologias')->where('id', $soli->patologias)->value('nombre') }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $soli->id }}" data-toggle="modal" data-target="#editModal">Editar</button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $soli->id }}" data-toggle="modal" data-target="#deleteModal">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>


                <div class="row">

                    <div class="col-md-6">
                        <button class="btn btn-info btn-sm" type="button" onclick="openCenteredWindow('{{ route('addNewPrecarga', ['patologias' => $soli->patologias, 'search' => $search]) }}', 750, 500)">Agregar medicación</button>
                        <button class="btn btn-warning btn-sm" type="button" onclick="location.reload()">Actualizar datos</button>
                        <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#crearPedidoModal">Crear receta</button>
                    </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

</div>


@if($pedidosMedicos)
    <div class="tarjeta2">
        <div class="container-fluid">
            <div class="card bg-white rounded shadow">
                <div class="card-header">
                    <h3 class="card-title">Solicitudes creadas</h3>
                    <h5>{{ $nombreMedico }}</h5>
                </div>
                <div class="card-body">
                    <!-- Resto del código -->

                    <h3>Solicitudes</h3>
                    <table id="tabla-solicitudes" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Fecha de pedido</th>
                            <th>Afiliado</th>
                            <th>Número de solicitud</th>
                            <th>Nombre afiliado</th>
                            <th>Estado Solicitud</th>
                            <th>Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pedidosMedicos as $pedido)
                            <tr>
                                <td>{{ $pedido->created_at }}</td>
                                <td>{{ $pedido->nroAfiliado }}</td>
                                <td>{{ $pedido->nrosolicitud }}</td>
                                <td>Metformina, Insulina, etc</td>
                                <td>Estado 1</td>
                                <td>
                                    <button class="btn btn-primary btn-xs" type="button" data-toggle="modal" data-index="{{$pedido->id}}" data-target="#modalVer">Ver</button>
                                    <button class="btn btn-warning btn-xs" type="button">Imprimir</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endif


<!-- Modal Ver -->
<div class="modal fade" id="modalVer" tabindex="-1" role="dialog" aria-labelledby="modalVerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerLabel">Datos de la solicitud</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Dato 1: Valor 1</p>
                <p>Dato 2: Valor 2</p>
                <p>Dato 3: Valor 3</p>
                <p>Dato 4: Valor 4</p>
                <p>Dato 5: Valor 5</p>
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
                <h5 class="modal-title" id="editModalLabel">Editar pedido</h5>
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


@if($search)
<!-- Modal para crear pedido -->
<div class="modal fade" id="crearPedidoModal" tabindex="-1" role="dialog" aria-labelledby="crearPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearPedidoModalLabel">Crear receta</h5>
                <p>Por favor, revise los datos antes de guardar</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enviarPedido" method="POST" action="{{ route("pm.store")}}">
                    @csrf
                    <!-- Aquí puedes agregar los campos necesarios para crear el pedido -->
                    <div class="form-group">
                        <label for="search">Número de afiliado</label>
                        <input type="text" class="form-control" id="nroAfiliado" name="nroAfiliado" value="{{ $search }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $nombre }}" readonly>
                    </div>


                    <!-- Este puede ser un hidden -->

                    <div class="form-group">
                        <label for="pedidosMedicos">Número de teléfono</label>
                        <input class="form-control" id="tel_afiliado" name="tel_afiliado" rows="3" value="{{ $telefono }}">
                    </div>

                    <div class="form-group">
                        <label for="clinicas_id">Establecimiento</label>
                        <select  class="form-control select2-search--dropdown" id="clinicas_id" name="clinicas_id">
                            @foreach($clinicas as $cli)
                            <option value="{{$cli->id}}">{{ $cli->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edad">Edad del paciente</label>
                        <input type="number" class="form-control" id="edad" name="edad" value="{{$edad}}">
                    </div>

                    <div class="form-group">
                        <label for="medicos_id">Médico que prescribe</label>
                        <select  class="form-control select2" id="medicos_id" name="medicos_id">
                            @foreach($medicos as $med)
                                <option value="{{$med->id}}">{{ $med->nombremedico }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="zona_residencia">Elegir zona de retiro</label>
                        <select  class="form-control select2" id="zona_residencia" name="zona_residencia">
                                <option value="Norte">Norte</option>
                                <option value="Sur">Sur</option>
                                <option value="Este">Este</option>
                                <option value="Oeste">Oeste</option>
                                <option value="Centro">Centro</option>
                                <option value="Interior">Interior de la capital</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="postdatada">Receta prolongada</label>
                        <select  class="form-control select2" id="postdatada" name="postdatada">
                            @foreach($postdatada as $pd)
                                <option value="{{$pd->id}}">{{ $pd->cantidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="discapacidad">Discapacidad</label>
                        <select  class="form-control select2" id="discapacidad" name="discapacidad">
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico</label>
                        <textarea class="form-control" id="diagnostico" name="diagnostico"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                    </div>


                    <!-- Resto de los campos del formulario en hidden -->
                    <input type="hidden" name="afiliado_id" value="{{ $afiliado_id }}">
                    <input type="hidden" id="nrosolicitud" name="nrosolicitud" value="{{ $nroSolicitud }}" readonly>
                    <input type="hidden" id="fecha_receta" name="fecha_receta" value="{{ date('Y-m-d') }}" readonly>
                    <input type="hidden" id="fecha_venicimiento" name="fecha_venicimiento" value="{{ date('Y-m-d', strtotime('+30 days')) }}" readonly>
                    <input type="hidden" id="patologias" name="patologias" value="{{ $searchPatologia}}" >
                    <input type="hidden" id="estado_solicitud_id" name="estado_solicitud_id" value="'1'" readonly>
                    <input type="hidden" id="stamp_user" name="stamp_user" value="{{ $stampuser }}" readonly>

                    <button type="submit" class="btn btn-success btn-pedido">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
<script>

    var afiliadoNumber = null;

    $('.btn-edit').on('click', function() {
        var afiliadoId = $(this).data('id');
        var url = '{{ route("pm.edit", ":id") }}';
        url = url.replace(':id', afiliadoId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('FUNCIONA');
                $('#editModal .modal-body').html(response);
                $('#editModal').modal('show');

            },
            error: function(xhr) {
                // Manejo de errores si es necesario
            }
        });
    });

    $('.btn-delete').on('click', function() {
        afiliadoNumber = $(this).data('id');
    });

    $('.btn-eliminar').on('click', function() {
        if (afiliadoNumber !== null) {
            var afiliadoId = afiliadoNumber;
            var url = '{{ route("pm.destroy", ":id") }}';
            url = url.replace(':id', afiliadoId);

            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(response) {
                    // Procesar la respuesta si es necesario
                    $('#deleteModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    // Manejo de errores si es necesario
                }
            });

            afiliadoNumber = null; // Restablecer el valor de afiliadoNumber después de utilizarlo
        }
    });

    $(document).ready(function() {

        $('#tabla-solicitudes').DataTable();


    });

    function openCenteredWindow(url, width, height) {
        var left = (window.screen.width - width) / 2;
        var top = (window.screen.height - height) / 2;
        var windowFeatures = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left;
        var newWindow = window.open(url, '_blank', windowFeatures);

        newWindow.onbeforeunload = function() {
            location.reload();
        };
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

    .tarjeta2{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        padding-bottom: 15px;
    }

</style>



</body>
</html>

@endsection
