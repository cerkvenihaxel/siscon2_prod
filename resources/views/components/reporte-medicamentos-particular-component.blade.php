<div class="container mt-4" style="background-color: white; padding: 2rem; border-radius: 1rem; margin-top: 2rem">
    <h2>Reportes Particulares de Medicamentos (por clientes)</h2>
    <form id="reporte-form">
        <div class="row">
            <div class="col-md-4">
                <label for="startDate">Fecha de Inicio:</label>
                <input type="date" id="startDate" name="startDate" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="endDate">Fecha de Fin:</label>
                <input type="date" id="endDate" name="endDate" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="puntoRetiroId">Punto de Retiro:</label>
                <select id="puntoRetiroId" name="puntoRetiroId" class="form-control" required>
                    <option value="" disabled selected>Seleccione un punto de retiro</option>
                    @foreach($puntoRetiro as $pr)
                        <option value="{{$pr->id}}">{{$pr->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="button-container mt-4">
            <button type="button" id="btn-buscar" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <div class="mt-4">
        <button id="btn-exportar" class="btn btn-success">Descargar Excel</button>
    </div>

    <div id="resultados" class="mt-4">
        <!-- Resultados de la tabla -->
    </div>

    <nav id="paginacion" aria-label="Paginación" class="mt-4">
        <!-- Controles de paginación -->
    </nav>
</div>

<style>
    .button-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1rem 0; /* Padding de 1 rem */
    }
</style>

<script>
    let currentPage = 1; // Página actual
    let perPage = 15; // Resultados por página

    function fetchReportes(page = 1) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const puntoRetiroId = document.getElementById('puntoRetiroId').value;

        if (!puntoRetiroId) {
            alert('Seleccione un punto de retiro.');
            return;
        }

        currentPage = page;

        fetch(`/reportes-medicamentos-particular?startDate=${startDate}&endDate=${endDate}&puntoRetiroId=${puntoRetiroId}&page=${page}&perPage=${perPage}`)
            .then(response => response.json())
            .then(data => {
                renderTable(data.data);
                renderPagination(data);
            });
    }

    function renderTable(data) {
        let html = '<table class="table table-striped"><thead><tr>';
        html += '<th>ID Pedido</th><th>Nro Remito</th><th>Nro Factura</th><th>Sucursal</th>';
        html += '<th>Afiliado</th><th>Nro Afiliado</th><th>Id Articulo</th>'
        html += '<th>Fecha de Carga</th><th>Artículo</th><th>Cantidad</th><th>Patología</th>';
        html += '</tr></thead><tbody>';

        data.forEach(row => {
            html += `<tr>
                <td>${row.id_pedido}</td>
                <td>${row.Remito}</td>
                <td>${row.Factura}</td>
                <td>${row.Sucursal}</td>
                <td>${row.Afiliado}</td>
                <td>${row.Numero_de_Afiliado}</td>
                <td>${row.Id_Articulo}</td>
                <td>${row.Fecha_de_Carga}</td>
                <td>${row.Artículo}</td>
                <td>${row.Cantidad}</td>
                <td>${row.Patología}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        document.getElementById('resultados').innerHTML = html;
    }

    function renderPagination(data) {
        let html = '<ul class="pagination justify-content-center">';
        if (data.prev_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="fetchReportes(${data.current_page - 1})">Anterior</a></li>`;
        }

        for (let i = 1; i <= data.last_page; i++) {
            html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="fetchReportes(${i})">${i}</a>
                    </li>`;
        }

        if (data.next_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="fetchReportes(${data.current_page + 1})">Siguiente</a></li>`;
        }
        html += '</ul>';

        document.getElementById('paginacion').innerHTML = html;
    }

    document.getElementById('btn-buscar').addEventListener('click', () => fetchReportes());

    document.getElementById('btn-exportar').addEventListener('click', () => {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const puntoRetiroId = document.getElementById('puntoRetiroId').value;

        if (!puntoRetiroId) {
            alert('Seleccione un punto de retiro.');
            return;
        }

        window.location.href = `/exportar-excel-particular?startDate=${startDate}&endDate=${endDate}&puntoRetiroId=${puntoRetiroId}`;
    });
</script>
