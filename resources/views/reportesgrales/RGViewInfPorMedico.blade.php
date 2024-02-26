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
    <h1> Reporte Por Médico</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <div class="table-responsive">
        <table class='table table-hover table-striped table-bordered' id = 'tabla'>
            <h3>Médicos listados: {{ count($data)}}</h3>
            <thead>
                <tr>
                    <th>Médico</th>
                    <th>ENERO 2023</th>
                    <th>FEBRERO 2023</th>
                    <th>MARZO 2023</th>
                    <th>ABRIL 2023</th>
                    <th>MAYO 2023</th>
                    <th>JUNIO 2023</th>
                    <th>JULIO 2023</th>
                    <th>AGOSTO 2023</th>
                    <th>SEPTIEMBRE 2023</th>
                    <th>OCTUBRE 2023</th>
                    <th>NOVIEMBRE 2023</th>
                    <th>DICIEMBRE 2023</th>
                    <th>ENERO 2024</th>
                    <th>FEBRERO 2024</th>
                    <th>MARZO 2024</th>
                    <th>ABRIL 2024</th>
                    <th>MAYO 2024</th>
                    <th>JUNIO 2024</th>
                    <th>JULIO 2024</th>
                    <th>AGOSTO 2024</th>
                    <th>SEPTIEMBRE 2024</th>
                    <th>OCTUBRE 2024</th>
                    <th>NOVIEMBRE 2024</th>
                    <th>DICIEMBRE 2024</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{$item->Medico}}</td>
                    <td>{{$item->ENERO_2023}}</td>
                    <td>{{$item->FEBRERO_2023}}</td>
                    <td>{{$item->MARZO_2023}}</td>
                    <td>{{$item->ABRIL_2023}}</td>
                    <td>{{$item->MAYO_2023}}</td>
                    <td>{{$item->JUNIO_2023}}</td>
                    <td>{{$item->JULIO_2023}}</td>
                    <td>{{$item->AGOSTO_2023}}</td>
                    <td>{{$item->SEPTIEMBRE_2023}}</td>
                    <td>{{$item->OCTUBRE_2023}}</td>
                    <td>{{$item->NOVIEMBRE_2023}}</td>
                    <td>{{$item->DICIEMBRE_2023}}</td>
                    <td>{{$item->ENERO_2024}}</td>
                    <td>{{$item->FEBRERO_2024}}</td>
                    <td>{{$item->MARZO_2024}}</td>
                    <td>{{$item->ABRIL_2024}}</td>
                    <td>{{$item->MAYO_2024}}</td>
                    <td>{{$item->JUNIO_2024}}</td>
                    <td>{{$item->JULIO_2024}}</td>
                    <td>{{$item->AGOSTO_2024}}</td>
                    <td>{{$item->SEPTIEMBRE_2024}}</td>
                    <td>{{$item->OCTUBRE_2024}}</td>
                    <td>{{$item->NOVIEMBRE_2024}}</td>
                    <td>{{$item->DICIEMBRE_2024}}</td>
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
