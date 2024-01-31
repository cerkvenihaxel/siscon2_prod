<!DOCTYPE html>
<html>
<head>
    <title>Carga masiva SISCON</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        $(document).ready(function() {

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
    </script>
</head>

<body>

    <div class="container-fluid bg-white relative">
        <div class="card shadow-1 mt-3">
            <div class="card-body detail-list">
                <h4 class="font-bold">Crear pedido masivo</h4>
                <p class="text-muted">Seleccione varios artículos para enviar a depósito</p>
                <hr>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('generar-pedido.store')}}" method="POST">
                    @csrf
                    <div class="select2-art form-group col-md-12">
                        <label for="articulo">Artículo:</label>
                        <select type="text" name="articulo" class="select2-dropdown search-select2 col-md-4" style="width: 500px;"></select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" required>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>

                <hr>

                @if(count($elementos) > 0)
                    <table class="table-bordered mt-4 table-responsive">
                        <thead>
                        <tr>
                            <th>Nro artículo</th>
                            <th>Presentacion</th>
                            <th>Monodroga</th>
                            <th>Laboratorio</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Total</th>

                            <th>Acciones</th>


                        </tr>
                        </thead>
                        <tbody>
                        @foreach($elementos as $elemento)
                            <tr>
                                <td>{{ $elemento['nro_articulo'] }}</td>
                                <td>{{ $elemento['presentacion'] }}</td>
                                <td>{{ $elemento['monodroga'] }}</td>
                                <td>{{ $elemento['laboratorio'] }}</td>
                                <td>{{ $elemento['precio'] }}</td>
                                <td>{{ $elemento['cantidad'] }}</td>
                                <td>{{ $elemento['subtotal'] }}</td>
                                <td>{{ $elemento['descuento'] }}</td>
                                <td>{{ $elemento['total'] }}</td>

                                <td class="text-center">
                                    <a href="#" data-toggle="modal" class="btn btn-xs btn-warning" data-target="#modal-editar-{{ $loop->index }}">Editar</a>
                                    <a class="btn btn-xs btn-danger" href="{{ route('generar-pedido.eliminar-articulo', ['index' => $loop->index]) }}">Eliminar</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


            <form action="{{route('generar-pedido.enviar', ['elementos' => $elementos])}}" method="POST">


                <div class="form-group obs-form">
                    <label for="observaciones">Observaciones</label>
                    <input class="form-control" type="text" name="observaciones">
                </div>

                @csrf
                <select class="form-control-sm col-md-4 farmacia" name="farmacia" id="farmacia" required>
                    <option value="">Seleccione una farmacia</option>
                    @foreach($farmacias as $convenio)
                        <option value="{{ $convenio->id }}">{{ $convenio->nombre }}</option>
                    @endforeach
                </select>


                <div class="text-right mt-4">
                    <hr class="hert">
                    <a href="{{route('generar-pedido.vaciar')}}" class="btn btn-danger">Vaciar pedido</a>
                    <button type="submit" class="btn btn-success">Enviar pedido</button>
                    <hr class="hert">
                </div>
            </form>

            @else
                <h4 class="text-center">No hay elementos para mostrar.</h4>
            @endif





        </div>

        <a href="/generarpedido_oficina">
            <button type="button" class="btn btn-primary btn-lg"> Volver a convenio </button>
        </a>


            @include('backoffice.global.pedidos-convenio.partials.edit')

            </div>





    <style>
    .card{
        background-color: white;
        height: 100vh;
        padding: 4rem;
        border-radius: 1rem;
    }

    .card h4{
        font-size: 2rem;
        font-weight: bolder;
    }

    .table-bordered{
        width: 100%;
        border-top: none;
        border-left: none;
        border-right: none;
    }

    .table-bordered thead tr th{
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
        border: none;
    }

    .table-bordered tbody tr td{
        font-size: 1.6rem;
        font-weight: lighter;
        text-align: center;
        margin: 1rem 0;
    }

    .botones{
        position: absolute;
        right: 6rem;
        top: 80vh;
    }

    .hert {
        width: 100vw;
    }

    .rellenable{
        padding-top: 2rem;
    }

    .select2-art{
        margin-top: 3rem;
    }

    .farmacia{
        margin-top: 3rem;
        margin-bottom: 2rem;
    }

    .obs-form{
        margin-top: 3rem;
    }

</style>
</body>
