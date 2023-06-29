@extends('crudbooster::admin_template')

@section('content')

    <!DOCTYPE html>
<html>
<head>
    <title>Escritorio convenio medicamentos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .card {
            background-color: #ffffff;
            border-radius: 46px;
            width: 100%;
            min-height: 420px; /* Actualizamos la altura mínima */
            margin: 26px auto;
            padding: 30px;
        }

        .info-box {
            width: 250px;
            height: 150px;
            display: inline-block;
            margin-right: 20px;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
        }

        .info-box a {
            color: white;
        }

        .info-box a:hover{
            color:orange;
        }

        .info-box.blue {
            background-color: #007bff;
        }

        .info-box.green {
            background-color: #28a745;
        }

        .info-box.red {
            background-color: #dc3545;
        }

        .info-box.yellow{
            background-color: #f39c12;
        }

        .info-box .card-title {
            color: #fff;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-box h3 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .info-box p {
            margin-bottom: 0;
        }

    </style>
</head>
<body>
<div class="container">
    <h3 class="card-title">Obra social</h3>
    <div class="text-center">
        <div class="info-box blue">
            <h4 class="card-title">Pedidos entrantes</h4>
            <div class="card-text">
                <h3>{{ $nroEntrantes }}</h3>
                <p><a href="#">Ver más</a></p>
            </div>
        </div>
        <div class="info-box green">
            <h4 class="card-title">Pedidos autorizados</h4>
            <div class="card-text">
                <h3>{{$nroAutorizados}}</h3>
                <p><a href="#">Ver más</a></p>
            </div>
        </div>
        <div class="info-box red">
            <h4 class="card-title">Pedidos rechazados</h4>
            <div class="card-text">
                <h3>{{$nroRechazados}}</h3>
                <p><a href="#">Ver más</a></p>
            </div>
        </div>
    </div>

    <h3 class="card-title">Global Medica</h3>
    <div class="text-center">
        <div class="info-box blue">
            <h4 class="card-title">Pedidos asignados</h4>
            <div class="card-text">
                <h3>{{$nroAsignados}}</h3>
                <p><a href="#">Ver más</a></p>
            </div>
        </div>
        <div class="info-box yellow">
            <h4 class="card-title">Pedidos procesados</h4>
            <div class="card-text">
                <h3>{{$nroProcesados}}</h3>
                <p><a href="#">Ver más</a></p>
            </div>
        </div>
        <div class="info-box green">
            <h4 class="card-title">Pedidos entregados</h4>
                    <div class="card-text">
                        <h3>{{$nroEntregados}}</h3>
                        <p><a href="#">Ver más</a></p>
                    </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tabla de Patologías</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="patologiasTable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Patologías</th>
                                <th>Cantidad de pacientes</th>
                                <th>Opción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($patologiasName as $pato)
                                <tr>
                                    <td>{{ $pato->nombre  }}</td>
                                    <td> {{ $consulta = DB::table('afiliados_articulos')
                                            ->select('patologias')
                                             ->where('patologias', $pato->id)
                                            ->distinct('nro_afiliado')
                                            ->count() }}</td>
                                    <td><button class="btn btn-info btn-xs m-5">
                                            <a href="/admin/afiliados_articulos47?q={{$pato->nombre}}" style="color: white">
                                             Ver más
                                            </a>
                                        </button>  </td>
                                </tr>
                                @php
                                $suma += $consulta
                                 @endphp

                            @endforeach

                            </tbody>
                        </table>

                        <h4>Total afiliados por patologías : {{ $suma }}</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</div>

<div class="modal fade" id="verMasModal" tabindex="-1" role="dialog" aria-labelledby="verMasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verMasModalLabel">Detalles del afiliado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="afiliadosTable" class="table">
                    <thead>
                    <tr>
                        <th>Afiliado</th>
                        <th>Nombre</th>
                        <!-- Agrega aquí las columnas adicionales que necesites -->
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Aquí se generarán dinámicamente las filas de la tabla -->
                    </tbody>
                </table>
                <div id="paginationContainer" class="text-center"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {
        $('#patologiasTable').DataTable({
            "order": [[1, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });
    });

    $(document).ready(function() {
        var currentPage = 1;
        var pageSize = 10;

        $('.btn-ver-mas').on('click', function(e) {
            e.preventDefault();
            var afiliadoId = $(this).data('pato');

            $.ajax({
                url: '/escritorioConvenioMedicamentos/vermas/' + afiliadoId,
                type: 'GET',
                success: successHandler,
                error: function(xhr) {
                    // Maneja los errores en caso de que ocurran
                }
            });
        });

        function successHandler(response) {
            $('#verMasModal').modal('show');

            // Limpia el cuerpo de la tabla
            $('#afiliadosTable tbody').empty();
            $('#paginationContainer').empty();

            // Obtiene la cantidad total de registros
            var totalRecords = response.afiliados.length;

            // Calcula la cantidad total de páginas
            var totalPages = Math.ceil(totalRecords / pageSize);

            // Muestra los registros de la página actual
            var startIndex = (currentPage - 1) * pageSize;
            var endIndex = startIndex + pageSize;
            var currentPageRecords = response.afiliados.slice(startIndex, endIndex);

            // Recorre los registros de la página actual y agrega las filas a la tabla
            $.each(currentPageRecords, function(index, afiliado) {
                var row = '<tr>';
                row += '<td>' + afiliado.nro_afiliado + '</td>';
                row += '<td>' + afiliado.nombre + '</td>';
                // Agrega aquí las columnas adicionales que necesites
                row += '</tr>';
                $('#afiliadosTable tbody').append(row);
            });

            // Check if the total number of pages exceeds 15
            var displayPaginator = totalPages > 15;

            // Create the pagination buttons
            if (displayPaginator) {
                // Display only a limited number of pages
                var maxPages = 15;
                var startPage = 1;
                var endPage = startPage + maxPages - 1;

                if (currentPage > Math.floor(maxPages / 2)) {
                    // Adjust the start and end pages based on the current page
                    startPage = currentPage - Math.floor(maxPages / 2);
                    endPage = startPage + maxPages - 1;

                    if (endPage > totalPages) {
                        // If the end page exceeds the total pages, adjust the start and end pages
                        endPage = totalPages;
                        startPage = endPage - maxPages + 1;
                    }
                }

                for (var i = startPage; i <= endPage; i++) {
                    var button = '<button class="btn btn-sm btn-pagination';
                    if (i === currentPage) {
                        button += ' active';
                    }
                    button += '">' + i + '</button>';
                    $('#paginationContainer').append(button);
                }

                // Add ellipsis if there are more pages
                if (endPage < totalPages) {
                    $('#paginationContainer').append('<span class="ellipsis">...</span>');
                }
            }


            // Asigna el controlador de eventos para los botones de paginación
            $('.btn-pagination').on('click', function() {
                currentPage = parseInt($(this).text());
                // Vuelve a llamar a la función de éxito del Ajax para mostrar la página seleccionada
                successHandler(response);
            });
        }
    });

</script>

</body>
</html>
@endsection
