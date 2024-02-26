{{-- INFORME POR ESPECIALIDAD --}}

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
    <h1> Reporte Por Especialidad</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered' id = 'tabla'>
            {{-- <h3>Cantidad = {{ count($data)}}</h3> --}}
            <thead>
                <tr>
                    <th>Especialidad</th>
                    <th>INGRESO ENERO 2023</th>
                    <th>INGRESO FEBRERO 2023</th>
                    <th>INGRESO MARZO 2023</th>
                    <th>INGRESO ABRIL 2023</th>
                    <th>INGRESO MAYO 2023</th>
                    <th>INGRESO JUNIO 2023</th>
                    <th>INGRESO JULIO 2023</th>
                    <th>INGRESO AGOSTO 2023</th>
                    <th>INGRESO SEPTIEMBRE 2023</th>
                    <th>INGRESO OCTUBRE 2023</th>
                    <th>INGRESO NOVIEMBRE 2023</th>
                    <th>INGRESO DICIEMBRE 2023</th>
                    <th>INGRESO ENERO 2024</th>
                    <th>INGRESO FEBRERO 2024</th>
                    <th>INGRESO MARZO 2024</th>
                    <th>INGRESO ABRIL 2024</th>
                    <th>INGRESO MAYO 2024</th>
                    <th>INGRESO JUNIO 2024</th>
                    <th>INGRESO JULIO 2024</th>
                    <th>INGRESO AGOSTO 2024</th>
                    <th>INGRESO SEPTIEMBRE 2024</th>
                    <th>INGRESO OCTUBRE 2024</th>
                    <th>INGRESO NOVIEMBRE 2024</th>
                    <th>INGRESO DICIEMBRE 2024</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $item->Especialidad}}</td>
                    <td>{{ $item->INGRESO_ENERO_2023}}</td>
                    <td>{{ $item->INGRESO_FEBRERO_2023}}</td>
                    <td>{{ $item->INGRESO_MARZO_2023}}</td>
                    <td>{{ $item->INGRESO_ABRIL_2023}}</td>
                    <td>{{ $item->INGRESO_MAYO_2023}}</td>
                    <td>{{ $item->INGRESO_JUNIO_2023}}</td>
                    <td>{{ $item->INGRESO_JULIO_2023}}</td>
                    <td>{{ $item->INGRESO_AGOSTO_2023}}</td>
                    <td>{{ $item->INGRESO_SEPTIEMBRE_2023}}</td>
                    <td>{{ $item->INGRESO_OCTUBRE_2023}}</td>
                    <td>{{ $item->INGRESO_NOVIEMBRE_2023}}</td>
                    <td>{{ $item->INGRESO_DICIEMBRE_2023}}</td>
                    <td>{{ $item->INGRESO_ENERO_2024}}</td>
                    <td>{{ $item->INGRESO_FEBRERO_2024}}</td>
                    <td>{{ $item->INGRESO_MARZO_2024}}</td>
                    <td>{{ $item->INGRESO_ABRIL_2024}}</td>
                    <td>{{ $item->INGRESO_MAYO_2024}}</td>
                    <td>{{ $item->INGRESO_JUNIO_2024}}</td>
                    <td>{{ $item->INGRESO_JULIO_2024}}</td>
                    <td>{{ $item->INGRESO_AGOSTO_2024}}</td>
                    <td>{{ $item->INGRESO_SEPTIEMBRE_2024}}</td>
                    <td>{{ $item->INGRESO_OCTUBRE_2024}}</td>
                    <td>{{ $item->INGRESO_NOVIEMBRE_2024}}</td>
                    <td>{{ $item->INGRESO_DICIEMBRE_2024}}</td>
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
