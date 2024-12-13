<div class="container mt-4" style="background-color: white; padding: 2rem; border-radius: 1rem; margin-top: 2rem">
    <h2>Reportes Generales de Medicamentos</h2>
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
                <button type="button" id="btn-buscar-reportes" class="btn btn-primary w-100">Buscar Reportes</button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <button id="btn-exportar-reportes" class="btn btn-success">Descargar Excel</button>
    </div>

    <div id="resultados-reportes" class="mt-4">
        <!-- Resultados de la tabla -->
    </div>

    <nav id="paginacion-reportes" aria-label="Paginación" class="mt-4">
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
    let currentPageReportes = 1; // Página actual
    let perPageReportes = 15; // Resultados por página

    function fetchReportesGenerales(page = 1) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        currentPageReportes = page;

        fetch(`/reportes-medicamentos?startDate=${startDate}&endDate=${endDate}&page=${page}&perPage=${perPageReportes}`)
            .then(response => response.json())
            .then(data => {
                renderTableReportes(data.data);
                renderPaginationReportes(data);
            });
    }

    function renderTableReportes(data) {
        let html = '<table class="table table-striped"><thead><tr>';
        html += '<th>ID Pedido</th><th>Nro Factura</th><th>Punto de Retiro</th>';
        html += '<th>Fecha Factura</th><th>Fecha de Carga</th><th>Artículo</th><th>Cantidad</th><th>Total</th>';
        html += '</tr></thead><tbody>';

        data.forEach(row => {
            html += `<tr>
                <td>${row.id_pedido}</td>
                <td>${row.nro_factura}</td>
                <td>${row.Punto_de_Retiro}</td>
                <td>${row.Fecha_Comprobante}</td>
                <td>${row.Fecha_de_Carga}</td>
                <td>${row.Articulo}</td>
                <td>${row.Cantidad}</td>
                <td>${row.total}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        document.getElementById('resultados-reportes').innerHTML = html;
    }

    function renderPaginationReportes(data) {
        let html = '<ul class="pagination justify-content-center">';
        if (data.prev_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="fetchReportesGenerales(${data.current_page - 1})">Anterior</a></li>`;
        }

        for (let i = 1; i <= data.last_page; i++) {
            html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="fetchReportesGenerales(${i})">${i}</a>
                    </li>`;
        }

        if (data.next_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="fetchReportesGenerales(${data.current_page + 1})">Siguiente</a></li>`;
        }
        html += '</ul>';

        document.getElementById('paginacion-reportes').innerHTML = html;
    }

    document.getElementById('btn-buscar-reportes').addEventListener('click', () => fetchReportesGenerales());
    document.getElementById('btn-exportar-reportes').addEventListener('click', () => {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        window.location.href = `/exportar-excel?startDate=${startDate}&endDate=${endDate}`;
    });
</script>
