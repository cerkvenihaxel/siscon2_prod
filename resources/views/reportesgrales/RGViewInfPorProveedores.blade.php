{{-- INFORME POR PROVEEDORES --}}
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
    <h1> Reporte por Proveedores</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered'>
            <h3>Cantidad = {{ count($data)}}</h3>
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Año</th>
                    <th>Mes</th>
                    <th>Cotizadas</th>
                    <th>Adjudicadas</th>
                    <th>Finalizadas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $item->Proveedor }}</td>
                    <td>{{ $item->Año }}</td>
                    <td>{{ $item->Mes}}</td>
                    <td>{{ $item->Cotizadas}}</td>
                    <td>{{ $item->Adjudicadas}}</td>
                    <td>{{ $item->Finalizadas}}</td> 
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
