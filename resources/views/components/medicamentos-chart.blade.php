<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
</head>
<body>
<h2>
    Cantidad de medicación requerida por la obra social
</h2>
<table id="table1" class="display">
    <thead>
    <tr>
        <th>Nombre Medicación</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>

    <!-- TODO Falta revisar bien los nombres que no aparecen -->

    @foreach ($result as $row)
        <tr>
            <td>{{ $row->nombreMedicacion }}</td>
            <td>{{ $row->cantidadMedicacion }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table1').DataTable({
            "order": [[1, 'desc']]
        });
    });
</script>

<style>
    table{
        padding: 2rem;
    }

    {
    margin-top: 1rem;
    }
</style>
</body>
</html>

