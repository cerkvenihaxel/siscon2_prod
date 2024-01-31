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
    <p class="text-right">Fecha {{ now() }}</p>
    <hr class="border">
    <a>Datos del Pedido</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Número de solicitud</th>
            <th>Afiliado</th>
            <th>Número de Afiliado</th>
            <th>Patología</th>
            <th>ID PEDIDO</th>
            <th>Sucursal</th>
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
            <td>{{ DB::table('punto_retiro')->where('id', $pedido->punto_retiro_id)->value('nombre') }}</td>


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

    <a>Detalles del Pedido</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Item</th>
            <th>Descripción</th>
            <th>Cantidad entregada</th>
            <th>Cantidad pendiente</th>
            <!-- Agrega aquí más columnas según los datos que desees mostrar de $detalles -->
        </tr>
        </thead>
        <tbody>
        @foreach($detalles as $key => $detalle)
            <tr>
                <td>{{ $key +1 }}</td>
                <td> {{ $detalle->presentacion }}</td>
                <td>{{ $detalle->cantidad_entregada }}</td>
                <td>{{ $detalle->cantidad_pendiente }}</td>

                <!-- Agrega aquí más celdas según los datos que desees mostrar de $detalles -->
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr class="border mt-5">
    <h3 class="text-bold">Acuse de recibo</h3>
    <div class="inline-block mt-12">
        <hr class="border">
        <div>
            <p class="text-muted mt-12">Firma: ______________________________________</p>
            <p class="text-muted mt-12">Aclaración: ________________________________</p>
            <p class="text-muted mt-12">Domicilio : _____________________________________</p>
            <p class="text-muted mt-12">Documento: ________________________________</p>
            <p class="text-muted mt-12">Parentesco: ________________________________</p>
            <p class="text-muted mt-12">Teléfono: ________________________________</p>

        </div>


    </div>
</div>


</body>
<style>
    body {
        font-size: 11px; /* Ajusta el tamaño de la letra según tus necesidades */
    }
</style>
</html>

