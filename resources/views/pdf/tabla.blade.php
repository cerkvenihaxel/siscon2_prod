<!DOCTYPE html>
<html lang="es">
<head>
    <title>Información de pedido</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>

        table {
            width: 100%;
        }

    </style>
</head>
<body>


<div class="container mt-5">
    <img src="https://i.ibb.co/G91yZ8Z/SISCON.png" alt="SISCON" border="0" class="img-fluid left" width="65px" height="40px">
    <h6 class="text-right">Fecha {{ now() }}</h6>
    <hr class="border">
    <h5>Datos del Pedido</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Número de solicitud</th><
            <th>Afiliado</th>
            <th>Número de Afiliado</th>
            <th>Patología</th>
            <th>ID PEDIDO</th>
            <!-- Agrega aquí más columnas según los datos que desees mostrar de $pedido -->
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $pedido->nrosolicitud }}</td>
            <td>{{ $pedido->nombreyapellido }}</td>
            <td>{{ $pedido->nroAfiliado }}</td>
            @php
            $patologiaNro = DB::table('pedido_medicamento')->where('nrosolicitud', $pedido->nrosolicitud)->value('patologia');
            @endphp
            <td>{{ DB::table('patologias')->where('id', $patologiaNro)->value('nombre') }}</td>
            <td>{{ $pedido->id_pedido }}</td>

        </tr>
        </tbody>
    </table>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Observaciones</th>
            <th> Localidad </th>
            <th> Zona </th>
            <th> Número de teléfono </th>

        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $pedido->observaciones }}</td>
            <td>{{ DB::table('afiliados')->where('nroAfiliado', $pedido->nroAfiliado)->value('localidad') }}</td>
            <td>{{ $pedido->zona_residencia }}</td>
            <td>{{ $pedido->tel_afiliado }}</td>
        </tbody>
    </table>

    <h6>Detalles del Pedido</h6>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Item</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio final</th>
            <!-- Agrega aquí más columnas según los datos que desees mostrar de $detalles -->
        </tr>
        </thead>
        <tbody>
        @foreach($detalles as $key => $detalle)
            <tr>
                <td>{{ $key +1 }}</td>
                @php
                    $articuloID = $detalle->articuloZafiro_id;
                    $numeroArticulo = str_pad($articuloID, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda
                @endphp
                <td>{{ DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('presentacion_completa') }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{$detalle->total}}</td>
                <!-- Agrega aquí más celdas según los datos que desees mostrar de $detalles -->
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr class="border mt-5">

    <a class="btn btn-success" href="{{ route('printPDF', ['id' => $pedido->id]) }}">Imprimir PDF</a>

</div>

</body>
<style>
    body {
        font-size: 11px; /* Ajusta el tamaño de la letra según tus necesidades */
    }
</style>
</html>

