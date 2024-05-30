{{-- INFORME POR MES --}}

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

{{-- paginado: --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

<body>
    <div class="container-fluid">
    <h1> Reporte Por Mes</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered' id = 'tabla'>
            <h3>Cantidad = {{ count($data)}}</h3>
            <thead>
                <tr>
                    <th>Fecha de carga</th>
                    <th>Año</th>
                    <th>Mes de carga</th>
                    <th>Nombre</th>
                    <th>N° Afiliado</th>
                    <th>Clínica</th>
                    <th>Edad</th>
                    <th>Número de solicitud</th>
                    <th>Médico</th>
                    <th>Estado paciente</th>
                    <th>Estado solicitud</th>
                    <th>Fecha de cirugía</th>
                    <th>Sufrió accidente</th>
                    <th>Necesidad</th>
                    <th>Grupo artículos</th>
                    <th>Fecha de expiración</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $item->fecha_de_carga}}</td>
                    <td>{{ $item->Año}}</td>
                    <td>{{ $item->mes_de_carga}}</td>
                    <td>{{ $item->Nombre}}</td>
                    <td>{{ $item->NroAfiliado}}</td>
                    <td>{{ $item->Clínica}}</td>
                    <td>{{ $item->Edad}}</td>
                    <td>{{ $item->NroSolicitud}}</td>
                    <td>{{ $item->Médico}}</td>
                    <td>{{ $item->estado_paciente}}</td>
                    <td>{{ $item->estado_solicitud}}</td>
                    <td>{{ $item->fecha_de_cirugía}}</td>
                    <td>{{ $item->sufrió_accidente}}</td>
                    <td>{{ $item->Necesidad}}</td>
                    <td>{{ $item->grupo_articulos}}</td>
                    <td>{{ $item->fecha_de_expiración}}</td>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                "order": [[0, 'desc']], // Puedes ajustar la columna de orden según tus necesidades
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
            });
        }); 
    </script>
    
</body>
@else
<div class="alert alert-warning">No se pudieron encontrar datos</div>
@endif
@endsection
