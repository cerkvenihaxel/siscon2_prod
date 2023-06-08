@extends('crudbooster::admin_template')


@section('content')

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear solicitud</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>

<div class="tarjeta">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Crear solicitud</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="select-obra-social">Seleccione una obra social</label>
                    <select class="form-control select2" id="select-obra-social">
                        <option value="">Seleccione</option>
                        <option value="obra-social1">Obra Social 1</option>
                        <option value="obra-social2">Obra Social 2</option>
                        <option value="obra-social3">Obra Social 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="input-buscar-afiliado">Buscar Afiliado, coloque DNI o Número de afiliado</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-buscar-afiliado">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">Buscar</button>
                        </span>
                    </div>
                </div>
                <h3>Datos del afiliado</h3>
                <div class="row">
                    <div class="col-sm-3">
                        <h4>Nombre</h4>
                        <p>Nombre del afiliado</p>
                    </div>
                    <div class="col-sm-3">
                        <h4>DNI</h4>
                        <p>DNI del afiliado</p>
                    </div>
                    <div class="col-sm-3">
                        <h4>Localidad</h4>
                        <p>Localidad del afiliado</p>
                    </div>
                    <div class="col-sm-3">
                        <h4>Teléfono</h4>
                        <p>Teléfono del afiliado</p>
                    </div>
                </div>

                <h3>Medicación del afiliado</h3>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Medicación</th>
                        <th>Cantidad</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Medicación 1</td>
                        <td>2</td>
                        <td>
                            <button class="btn btn-warning btn-sm" type="button">Editar</button>
                            <button class="btn btn-danger btn-sm" type="button">Eliminar</button>

                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-info" type="button">Agregar medicación</button>
                        <button class="btn btn-success" type="button">Crear solicitud</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>

<div class="tarjeta2">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Buscar solicitud</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="select-obra-social">Seleccione una obra social</label>
                    <select class="form-control select2" id="select-obra-social">
                        <option value="">Seleccione</option>
                        <option value="obra-social1">Obra Social 1</option>
                        <option value="obra-social2">Obra Social 2</option>
                        <option value="obra-social3">Obra Social 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="input-buscar-afiliado">Buscar Afiliado, coloque DNI o Número de afiliado</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-buscar-afiliado">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">Buscar</button>
                        </span>
                    </div>
                </div>

                <h3>Solicitudes</h3>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Medicación requerida</th>
                        <th>Estado Solicitud</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Fecha 1</td>
                        <td>Afiliado 1</td>
                        <td>Número 1</td>
                        <td>Metformina, Insulina, etc</td>
                        <td>Estado 1</td>
                    </tr>
                    <tr>
                        <td>Fecha 2</td>
                        <td>Afiliado 2</td>
                        <td>Número 2</td>
                        <td>Metformina, Insulina, etc</td>

                        <td>Estado 2</td>
                    </tr>
                    <!-- Agrega más filas según sea necesario -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

<style>
    .tarjeta{
        background-color: white;
        border-radius: 12px;
        box-shadow: #0a0a0a 15px;
        padding-bottom: 15px;
    }

    .card-title{
        color: #4285F4;
    }

    .tarjeta2{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        box-shadow: #0a0a0a 15px;
        padding-bottom: 15px;
    }

</style>
</body>
</html>

@endsection
