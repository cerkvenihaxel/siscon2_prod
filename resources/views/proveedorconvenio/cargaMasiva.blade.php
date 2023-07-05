@extends('crudbooster::admin_template')
@section('content')

<!DOCTYPE html>
<html>
<head>
    <title>Generar pedido</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Generar pedido</h1>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Monodroga</th>
            <th>Presentación</th>
            <th>Laboratorio</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>SubTotal</th>
            <th>Descuento</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <!-- Aquí puedes agregar dinámicamente las filas de la tabla con los datos de los medicamentos -->
        </tbody>
    </table>

    <div class="form-group">
        <label for="punto_retiro">Punto de Retiro</label>
        <select class="form-control" name="punto_retiro" id="punto_retiro"></select>
    </div>
    <div class="form-group">
        <label for="observaciones">Observaciones</label>
        <input class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Ingrese las observaciones">
    </div>
    <div class="text-center">
        <button class="btn btn-primary btn-lg">Guardar</button>
    </div>
</div>
</body>
</html>

@endsection
