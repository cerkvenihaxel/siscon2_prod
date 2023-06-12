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
                                    <td><a href="#">Ver más</a></td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
</script>

</body>
</html>
@endsection
