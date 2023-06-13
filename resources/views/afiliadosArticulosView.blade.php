@extends('crudbooster::admin_template')

@section('content')
    <!DOCTYPE html>
<html>
<head>
    <title>Afiliados Articulos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <style>
        .container {
            background-color: #fff;
            border-radius: 46px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Afiliados Articulos</h2>
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="{{ route('afiliados_articulos.store') }}">
                @csrf
                <table id="afiliados-table" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Nro Afiliado</th>
                        <th>ID Articulo</th>
                        <th>Cantidad</th>
                        <th>Patologias</th>
                        <th>Descripción Articulo</th>
                        <th>Presentación</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Aquí se mostrarán los datos de la tabla afiliados_articulos -->
                    @foreach($afiliadosArticulos as $afiliadoArticulo)
                        <tr>
                            <td>{{ $afiliadoArticulo->nro_afiliado }}</td>
                            <td>{{ $afiliadoArticulo->id_articulo }}</td>
                            <td>{{ $afiliadoArticulo->cantidad }}</td>
                            <td>{{ $afiliadoArticulo->patologias }}</td>
                            <td>{{ $afiliadoArticulo->des_articulo }}</td>
                            <td>{{ $afiliadoArticulo->presentacion }}</td>
                            <td>
                                <a>Editar</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#afiliados-table').DataTable({
        });
    });
</script>
</body>
</html>

@endsection
