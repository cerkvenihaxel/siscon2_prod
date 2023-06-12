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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>


<div class="tarjeta">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Pedidos por entregar</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="select-obra-social">Obra social</label>
                    <select class="form-control select2" id="select-obra-social">
                        <option value="">Seleccione</option>
                        <option value="obra-social1">Obra Social 1</option>
                        <option value="obra-social2">Obra Social 2</option>
                        <option value="obra-social3">Obra Social 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="input-buscar-afiliado">Buscar por: nombre, dni, número de afiliado, monodroga</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-buscar-afiliado">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">Buscar</button>
                        </span>
                    </div>
                </div>

                <table id="tabla-solicitudes" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Médico solicitante</th>
                        <th>Patología</th>
                        <th>Estado Solicitud</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Fecha 1</td>
                        <td>Afiliado 1</td>
                        <td>Número 1</td>
                        <td>Medico 1</td>
                        <td>Patologia 1</td>
                        <td>Estado 1</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-success btn-xs m-5"><i class="fas fa-check"></i> Confirmar entrega</button>
                                <button class="btn btn-danger btn-xs m-5"><i class="fas fa-times"></i> Rechazar</button>
                                <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Imprimir pedido</button>


                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>Fecha 2</td>
                        <td>Afiliado 2</td>
                        <td>Número 2</td>
                        <td>Medico 2</td>
                        <td>Patologia 2</td>
                        <td>Estado 2</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-success btn-xs m-5"><i class="fas fa-check"></i>Confirmar entrega</button>
                                <button class="btn btn-danger btn-xs m-5"><i class="fas fa-times"></i> Rechazar</button>
                                <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Imprimir pedido</button>


                            </div>

                        </td>
                    </tr>
                    <!-- Agrega más filas según sea necesario -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="tarjeta2">
    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title-autorizados">Pedidos entregados</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="select-obra-social">Obra social</label>
                    <select class="form-control select2" id="select-obra-social">
                        <option value="">Seleccione</option>
                        <option value="obra-social1">Obra Social 1</option>
                        <option value="obra-social2">Obra Social 2</option>
                        <option value="obra-social3">Obra Social 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="input-buscar-afiliado">Buscar por: nombre, dni, número de afiliado, monodroga</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-buscar-afiliado">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">Buscar</button>
                        </span>
                    </div>
                </div>

                <h3>Solicitudes</h3>
                <table id="tabla-solicitudes" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Fechas de creación</th>
                        <th>Afiliado</th>
                        <th>Número de solicitud</th>
                        <th>Medicación requerida</th>
                        <th>Proveedor</th>
                        <th>Estado Solicitud</th>
                        <th>Estado del pedido</th>
                        <th>Nro pedido</th>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Fecha 1</td>
                        <td>Afiliado 1</td>
                        <td>Número 1</td>
                        <td>Metformina, Insulina, etc</td>
                        <td>Global Médica</td>
                        <td>Estado 1</td>
                        <td>Enviado a depósito</td>
                        <td>PE0090-00000019</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Acuse recibo</button>
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td>Fecha 2</td>
                        <td>Afiliado 2</td>
                        <td>Número 2</td>
                        <td>Metformina, Insulina, etc</td>
                        <td>Global Médica</td>
                        <td>Estado 2</td>
                        <td>Enviado a depósito</td>
                        <td>PE0090-00000020</td>
                        <td>
                            <div class="button-container">
                                <button class="btn btn-info btn-xs m-5"><i class="fas fa-eye"></i> Ver pedido</button>
                                <button class="btn btn-warning btn-xs mr-2"><i class="fas fa-print"></i> Acuse recibo</button>
                            </div>
                        </td>

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

    function openCenteredWindow(url, width, height) {
        var left = (window.screen.width - width) / 2;
        var top = (window.screen.height - height) / 2;
        var windowFeatures = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',menubar=no,toolbar=no,location=no,status=no';
        window.open(url, '_blank', windowFeatures);
    }

    var tablaSolicitudes;

    $(document).ready(function() {
        // Inicializar DataTables y guardar la instancia en la variable tablaSolicitudes
        tablaSolicitudes = $('#tabla-solicitudes').DataTable({
            paging: true,
            pageLength: 10,
            searching: false,
            lengthChange: false,
            info: false
        });

    });



    function filtrarTabla() {
        var valorFiltro = $('#input-buscar-afiliado').val();

        tablaSolicitudes.search(valorFiltro).draw();
    }
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

    .card-title-autorizados{
        color: #00a65a;
    }

    .card-title-rechazados{
        color: #c41300;
    }

    .tarjeta2{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        box-shadow: #0a0a0a 15px;
        padding-bottom: 15px;
    }

    .tarjeta3{
        margin-top: 18px;
        background-color: white;
        border-radius: 12px;
        box-shadow: #0a0a0a 15px;
        padding-bottom: 15px;
    }

    .button-container {
        margin-top: 10px;
    }

    .button-container button {
        display: inline-block;
        margin-right: 3px;
        margin-top: 2px;

    }

</style>
</body>
</html>

@endsection
