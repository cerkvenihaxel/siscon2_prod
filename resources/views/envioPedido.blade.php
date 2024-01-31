@extends('crudbooster::admin_template')

@section('content')

    @if(count($data)>0)

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <!-- Font Awesome CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
            <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
        </head>

        <body>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Envío de pedidos</h3>
                    <h5 class="card-subtitle">Periodo de tiempo = {{ $start_date }} - {{ $end_date }}</h5>
                    <h5 class="card-subtitle">Fecha de consulta = {{ $fecha }}</h5>
                    <h4 class="card-subtitle">Punto de retiro = {{ $id = DB::table('punto_retiro')->where('id', $id)->value('nombre') }}</h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class='table table-hover table-striped table-bordered'>
                    <h3>Pedidos = {{ count($data)}}</h3>
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>ID PEDIDO</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th> Total </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($entDetail as $e)
                            <td>  {{ $loop->iteration }} </td>
                            <td>  PC-0000033 </td>
                            <td>  {{ DB::table('articulosZafiro')->where('id', $e->articuloZafiro_id)->value('presentacion_completa') }} </td>
                            <td>  {{ $e->total_cantidad }} </td>
                            <td> $ {{ $e->total_total }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="btn-group">
                    <button id="progressButton" type="button" class="btn btn-primary">Enviar pedido a depósito</button>
                    <button type="button" class="btn btn-warning">Imprimir descripción</button>
                </div>

                <div id="progressCircle"></div>

            </div>
        </div>

        <style>
            #progressCircle {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                width: 120px;
                height: 120px;
                border-radius: 50%;
                border: 3px solid #ccc;
                border-top-color: #3498db;
                animation: spin 3s linear infinite;
            }
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
        <script>
            document.getElementById('progressButton').addEventListener('click', function() {
                var progressCircle = document.getElementById('progressCircle');
                progressCircle.style.display = 'block';

                setTimeout(function() {
                    progressCircle.style.display = 'none';
                    alert('Acción realizada con éxito');
                }, 1000);
            });

            var button = document.querySelector(".btn.btn-warning");
            button.addEventListener("click", function() {
                window.print();
            });
        </script>

        </body>
    @else
        <div class="alert alert-warning">No se pudieron encontrar datos</div>
    @endif

@endsection
