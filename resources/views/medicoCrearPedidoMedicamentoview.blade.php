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
                <h3 class="card-title">Crear solicitud</h3>
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
                <form method="POST" action="{{ route('buscarAfiliadoPedido') }}">
                    @csrf
                    <div class="form-group">
                        <label for="numeroAfiliado">Número de afiliado o DNI:</label>
                        <input type="text" class="form-control" id="numeroAfiliado" name="numeroAfiliado" placeholder="Ingrese el número de afiliado" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Buscar</button>

                </form>


                @if ($solicitud)

                <div class="row">

                    <div class="col-sm-6">
                        <h3>Medicación del afiliado</h3>
                    </div>
                    <div class="col-sm-6">
                        <label for="select-patologia">Seleccione una patología</label>
                        <select class="form-control select2" id="select-patologia">
                            <option value="">Seleccione</option>
                            <option value="diabetes">Diabetes</option>
                            <option value="hemodialisis">Hemodialisis</option>
                            <option value="transplantados">Transplantados</option>
                        </select>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-3">
                        @foreach ($solicitud as $item)
                            <h4>Nombre</h4>
                        <p> </p>
                        @endforeach

                    </div>
                    <div class="col-sm-3">
                        <h4>DNI</h4>
                        <p>{{ $search }}</p>
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
                        <button class="btn btn-warning btn-sm" type="button" onclick="openCenteredWindow('/admin/entrantes', 750, 500)">Actualizar Datos</button>
                        <button class="btn btn-info btn-sm" type="button" onclick="openCenteredWindow('/admin/entrantes', 750, 500)">Agregar medicación</button>
                        <button class="btn btn-success btn-sm" type="button" onclick="openCenteredWindow('/admin/entrantes', 750, 500)">Crear pedido</button>
                    </div>

                </div>
                @endif
            </div>

        </div>

    </div>

</div>


<div class="tarjeta2">

    <div class="container-fluid">
        <div class="card bg-white rounded shadow">
            <div class="card-header">
                <h3 class="card-title">Solicitudes creadas</h3>
                <h5>Dr. Oliva Cerkvenih, Cristian</h5>
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

        generarDatosDeEjemplo();
    });

    function generarDatosDeEjemplo() {
        var datos = [
            ['Fecha 1', 'Afiliado 1', 'Número 1', 'Metformina, Insulina, etc', 'Estado 1'],
            ['Fecha 2', 'Afiliado 2', 'Número 2', 'Metformina, Insulina, etc', 'Estado 2'],
            ['Fecha 3', 'Afiliado 3', 'Número 3', 'Metformina, Insulina, etc', 'Estado 3'],
            ['Fecha 4', 'Afiliado 4', 'Número 4', 'Metformina, Insulina, etc', 'Estado 4'],
            ['Fecha 5', 'Afiliado 5', 'Número 5', 'Metformina, Insulina, etc', 'Estado 5'],
            ['Fecha 6', 'Afiliado 6', 'Número 6', 'Metformina, Insulina, etc', 'Estado 6'],
            ['Fecha 7', 'Afiliado 7', 'Número 7', 'Metformina, Insulina, etc', 'Estado 7'],
            ['Fecha 8', 'Afiliado 8', 'Número 8', 'Metformina, Insulina, etc', 'Estado 8'],
            ['Fecha 9', 'Afiliado 9', 'Número 9', 'Metformina, Insulina, etc', 'Estado 9'],
            ['Fecha 10', 'Afiliado 10', 'Número 10', 'Metformina, Insulina, etc', 'Estado 10'],
            ['Fecha 11', 'Afiliado 11', 'Número 11', 'Metformina, Insulina, etc', 'Estado 11'],
            ['Fecha 12', 'Afiliado 12', 'Número 12', 'Metformina, Insulina, etc', 'Estado 12'],
            ['Fecha 13', 'Afiliado 13', 'Número 13', 'Metformina, Insulina, etc', 'Estado 13'],
            ['Fecha 14', 'Afiliado 14', 'Número 14', 'Metformina, Insulina, etc', 'Estado 14'],
            ['Fecha 15', 'Afiliado 15', 'Número 15', 'Metformina, Insulina, etc', 'Estado 15'],
            ['Fecha 16', 'Afiliado 16', 'Número 16', 'Metformina, Insulina, etc', 'Estado 16'],
            ['Fecha 17', 'Afiliado 17', 'Número 17', 'Metformina, Insulina, etc', 'Estado 17'],
            ['Fecha 18', 'Afiliado 18', 'Número 18', 'Metformina, Insulina, etc', 'Estado 18'],
            ['Fecha 19', 'Afiliado 19', 'Número 19', 'Metformina, Insulina, etc', 'Estado 19'],
            ['Fecha 20', 'Afiliado 20', 'Número 20', 'Metformina, Insulina, etc', 'Estado 20']
        ];

        var tabla = $('#tabla-solicitudes').DataTable();
        tabla.clear(); // Eliminar datos existentes de la tabla

    // Agregar los nuevos datos a la tabla
        for (var i = 0; i < datos.length; i++) {
            tabla.row.add(datos[i]);
        }
        // Dibujar la tabla nuevamente con los nuevos datos
        tabla.draw();
    }

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
