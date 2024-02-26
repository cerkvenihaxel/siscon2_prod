{{-- INFORME DE SOLICITUDES CARGADAS --}}

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
    <h1> Reporte de Solicitudes Cargadas </h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered'>
            <h3>Cantidad = {{ count($data)}}</h3>
            <thead>
                <tr>
                    <th>Patología</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Nro Afiliado</th>
                    <th>Medicación</th>
                    <th>Cantidad</th>
                    <th>Zona de Residencia</th>
                    <th>Fecha de Carga</th>
                    <th>Nro Solicitud</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $item->nombre }}</td>
                    <td>{{ $item->apeynombres }}</td>
                    <td>{{ $item->documento}}</td>
                    <td>{{ $item->nroAfiliado}}</td>
                    <td>{{ $item->presentacion_completa}}</td>
                    <td>{{ $item->cantidad }}</td> 
                    <td>{{ $item->zona_residencia }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->nrosolicitud }}</td>                    
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>


</body>
@else
<div class="alert alert-warning">No se pudieron encontrar datos</div>
@endif
@endsection
