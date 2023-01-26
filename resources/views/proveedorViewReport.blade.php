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
    <h1> Reporte de solicitudes cotizadas</h1>
    <h1> Periodo de tiempo = {{ $start_date }} - {{ $end_date}}</h1>
    <h1> Fecha de consulta = {{ $fecha }}</h1>
    <h2> Nombre del proveedor = {{$id}}</h2>
    <div class="table-responsive">
    <table class='table table-hover table-striped table-bordered'>
        <h3>Solicitudes cotizadas = {{ count($data)}}</h3>
    <thead>
        <tr>
            <th>Fecha de cotización</th>
            <th>Numero de solicitud</th>
            <th>Nombre del afiliado</th>
            <th>Nombre del medico</th>
            <th>Clínica</th>
            <th>Fecha de cirugía</th>
            <th> Artículos cotizados </th>
            <th> Total de la cotización </th>
            <th> Ver cotización </th>
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
            <td> {{ $item->fecha_cirugia }}</td>
            <td>
                <table  class='table table-hover table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cotDetail as $item2)
                        @if ($item->id == $item2->entrantes_id)
                        <tr>
                            <td>{{ $nombreArticulo = DB::table('articulos')->where('id', $item2->articulos_id)->value('des_articulo') }}</td>
                            <td>{{ $item2->precio_unitario }}</td>
                            <td>{{ $item2->cantidad }}</td>
                            <td>$ {{ $item2->precio }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </td>    
            <td> {{ $item->total }}</td>
            <td>
                <a href='/admin/cotizaciones19/detail/{{$item->id}}' class="btn btn-primary">Ver cotización</a>
            </td>
            

        @endforeach

        </tr>
    </tbody>

</table>
    </div>

    <div class="table-responsive">
    <table class='table table-hover table-striped table-bordered'>
        <h3>Solicitudes adjudicadas = {{count($adj)}}</h3>
    <thead>
        <tr>
            <th>Fecha de adjudicación</th>
            <th>Nombre y Apellido Afiliado</th>
            <th>Número de solicitud</th>
            <th>Médico Solicitante</th>
            <th>Clínica</th>
            <th>Fecha de cirugía</th>
            <th> Ver adjudicación </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($adj as $a)

        <tr>
            <td>{{ $a->created_at }}</td>
            <td>{{ $afiliado = DB::table('afiliados')->where('id', $a->afiliados_id )->value('apeynombres')}}</td>
            <td>{{ $a->nrosolicitud }}</td>
            <td>{{ $medico = DB::table('medicos')->where('id', $a->medicos_id)->value('nombremedico') }}</td>
            <td>{{ $clinica = DB::table('clinicas')->where('id', $a->clinicas_id )->value('nombre')}}</td>
            <td>{{ $a->fecha_cirugia }}</td>
            <td> <a href='/admin/adjudicaciones/detail/{{$a->id}}' class="btn btn-primary">Ver adjudicación</a></td>


        </tr>
        @endforeach

        </tr>
    </tbody>
    </table>

    </div>  
{{-- <table class='table table-hover table-striped table-bordered'>
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

    
    @endforeach

    </tr>
</tbody>

</table> --}}

{{-- {{ $data->links() }} --}}

</body>
@else
<div class="alert alert-warning">No se pudieron encontrar datos</div>
@endif
@endsection
