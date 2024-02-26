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

{{-- paginado: --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>

<body>
    <div class="container-fluid">
    <h1> Reporte de Solicitudes Cargadas </h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered' id = 'tabla'>
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

    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                "order": [[0, 'desc'],[4, 'desc'],[1, 'asc']], // Puedes ajustar la columna de orden según tus necesidades
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
