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
                <h1 class="card-title">Envío de pedidos</h1>
                <h1 class="card-subtitle">Periodo de tiempo = {{ $start_date }} - {{ $end_date }}</h1>
                <h1 class="card-subtitle">Fecha de consulta = {{ $fecha }}</h1>
                <h2 class="card-subtitle">Punto de retiro = {{ $id = DB::table('punto_retiro')->where('id', $id)->value('nombre') }}</h2>
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
            <button type="button" class="btn btn-primary mr-9">Enviar pedido a depósito</button>
            <button type="button" class="btn btn-warning">Imprimir descripción</button>
        </div>

    </div>
    </div>
</body>
@else
<div class="alert alert-warning">No se pudieron encontrar datos</div>
@endif
@endsection

