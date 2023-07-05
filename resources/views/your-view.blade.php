@extends('crudbooster::admin_template')

@section('content')



@if(count($data)>0 && $id != null)

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


    <h2>Nombre del Articulo Solicitado = {{ $nombreArticulo }}</h2>
    <h2>Numero de Articulo Solicitado = {{ $id }}</h2>
    <h2>Fecha de consulta = {{ $fecha }}</h2>
    <h2> Cantidad de solicitudes = {{ count($data) }}</h2>
    <h2> Cantidad total de pedidos del artículo = {{ $cantidadTotal = DB::table('entrantes_detail')->where('articulos_id', $id)->sum('cantidad') }}</h2>
    <table class='table table-hover table-striped table-bordered'>
        <h3>Datos de la solicitud del médico</h3>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Numero de solicitud</th>
            <th>Nombre del afiliado</th>
            <th>Nombre del medico</th>
            <th>Clínica</th>
            <th>Especialidad</th>
            <th>Cantidad</th>
            <th>Estado solicitud</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)


        <tr>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->nrosolicitud }}</td>
            <td>{{ $nombreAfiliado = DB::table('afiliados')->where('id',
                $item->afiliados_id)->value('apeynombres')}}</td>
            <td>{{ $nombreMedico = DB::table('medicos')->where('id', $item->medicos_id)->value('nombremedico') }}</td>
            <td>{{ $clinicasNombre= DB::table('clinicas')->where('id', $item->clinicas_id)->value('nombre')}}</td>
            <td>{{ $especialidad = DB::table('grupos')->where('id', $item->grupo_articulos)->value('des_grupo') }}</td>
            <td>{{ $cantidad = DB::table('entrantes_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('cantidad') }}</td>
            <td>{{ $estado = DB::table('estado_solicitud')->where('id', $item->estado_solicitud_id)->value('estado') }}</td>


        @endforeach

        </tr>
    </tbody>

</table>

<table class='table table-hover table-striped table-bordered'>
    <h3>Datos de cotizaciones del artículo</h3>
<thead>
    <tr>
        <th>Fecha de cotización</th>
        <th>Numero de solicitud</th>
        <th>Nombre del afiliado</th>
        <th>Nombre del medico</th>
        <th>Proveedor</th>
        <th>Precio unitario</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Estado solicitud</th>
    </tr>
</thead>
<tbody>
    @foreach ($cot as $item)

    <tr>
        <td>{{ $item->created_at }}</td>
        <td>{{ $item->nrosolicitud }}</td>
        <td>{{ $nombreAfiliado = DB::table('afiliados')->where('id',
            $item->afiliados_id)->value('apeynombres')}}</td>
        <td>{{ $nombreMedico = DB::table('medicos')->where('id', $item->medicos_id)->value('nombremedico') }}</td>
        <td>{{ $item->proveedor }}</td>
        <td>{{ $precioUnitario = DB::table('cotizaciones_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('precio_unitario') }}</td>
        <td>{{ $cantidad = DB::table('cotizaciones_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('cantidad') }}</td>
        <td>{{ $subtotal = DB::table('cotizaciones_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('precio') }}</td>
        <td>{{ $estado = DB::table('estado_solicitud')->where('id', $item->estado_solicitud_id)->value('estado') }}</td>



        @endforeach

    </tr>
</tbody>

</table>

{{-- {{ $data->links() }} --}}

</body>
@else
<div class="alert alert-warning">No se pudieron encontrar datos</div>
@endif
@endsection
