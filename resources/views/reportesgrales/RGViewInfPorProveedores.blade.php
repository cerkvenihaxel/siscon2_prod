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

{{-- paginado: --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

</head>

<body>
    <div class="container-fluid">
    <h1> Reporte por Proveedores</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered' id = 'tabla'>
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

    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                "order": [[0, 'desc'], [2, 'desc'], [1, 'desc']], // Puedes ajustar la columna de orden según tus necesidades
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
