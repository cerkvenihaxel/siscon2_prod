<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarjetas Centradas</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            padding: 16px;
            max-width: 1200px;
            width: 100%;
        }
        .card {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            width: 300px;
        }
        .card i {
            font-size: 48px;
            color: #3f51b5;
        }
        .card h2 {
            font-size: 24px;
            margin: 16px 0 8px;
        }
        .card p {
            font-size: 16px;
            color: #757575;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <i class="material-icons">inbox</i>
        <h1>{{$inbox}}</h1>
        <h2>Solicitudes ingresadas</h2>
        <p>Solicitudes ingresadas del período</p>
    </div>
    <div class="card">
        <i class="material-icons">info</i>
        <h1>{{$processed}}</h1>
        <h2>Solicitudes procesadas</h2>
        <p>Solicitudes procesadas por oficina convenio.</p>
    </div>
    <div class="card">
        <i class="material-icons">check</i>
        <h1>{{$delivered}}</h1>
        <h2>Solicitudes entregadas</h2>
        <p>Pedidos entregados en punto de dispensa.</p>
    </div>
</div>
</body>
</html>
