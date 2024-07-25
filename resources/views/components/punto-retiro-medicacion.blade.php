    <h2>Cantidad de medicación enviada al punto de dispensa</h2>

    <label for="puntoRetiroSelect">Filtrar por Punto de Retiro:</label>
    <select id="puntoRetiroSelect">
        <option value="">Todos</option>
        @foreach ($puntosRetiro as $punto)
            <option value="{{ $punto->nombre }}">{{ $punto->nombre }}</option>
        @endforeach
    </select>

    <table id="table2" class="display">
        <thead>
        <tr>
            <th>Nombre Medicación</th>
            <th>Cantidad</th>
            <th>Punto de Retiro</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($result as $row)
            <tr>
                <td>{{ $row->nombreMedicacion }}</td>
                <td>{{ $row->cantidadMedicacion }}</td>
                <td>{{ $row->nombrePuntoRetiro }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table2').DataTable({
                "order": [[1, 'desc']]
            });

            $('#puntoRetiroSelect').on('change', function () {
                var selectedPuntoRetiro = $(this).val();
                if (selectedPuntoRetiro) {
                    table.column(2).search('^' + selectedPuntoRetiro + '$', true, false).draw();
                } else {
                    table.column(2).search('').draw();
                }
            });
        });
    </script>
