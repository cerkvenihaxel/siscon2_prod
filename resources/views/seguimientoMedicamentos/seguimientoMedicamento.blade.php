<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de envíos medicamentos SISCON</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        .hero {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 8px;
        }

        .timeline-container {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-item .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }

        .timeline-item .line {
            position: absolute;
            top: 50%;
            left: 20px;
            width: 2px;
            background-color: #007bff;
            height: 100%;
            z-index: 0;
        }

        .timeline-item:last-child .line {
            display: none;
        }

        .timeline-item .description {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-left: 20px;
            flex-grow: 1;
            font-size: 14px;
        }

        .timeline-item .description strong {
            color: #0056b3;
        }

        /* Estilos responsivos */
        @media (max-width: 458px) {
            .timeline-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .timeline-item .circle {
                margin-bottom: 10px;
            }

            .timeline-item .description {
                margin-left: 0;
            }

            .timeline-item .line {
                left: 50%;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-control {
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Hero Section -->
    <div class="hero bg-primary text-white text-center py-5 mb-4">
        <h1 class="display-4">Seguimiento de envíos medicamentos SISCON</h1>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="card p-4 mb-5 shadow-sm">
        <form id="seguimientoForm" method="POST" action="#">
            <div class="d-flex flex-column flex-md-row align-items-md-center">
                <div class="form-group flex-grow-1 me-md-3">
                    <label for="nro_solicitud" class="form-label">Nro solicitud</label>
                    <input type="text" name="nro_solicitud" id="nro_solicitud" class="form-control" placeholder="Ingrese el número de solicitud" required>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <button type="submit" class="btn btn-primary mt-3 mt-md-0">Buscar</button>
            </div>
        </form>
    </div>

    <!-- Timeline -->
    <div id="timeline" class="timeline-container">
        <!-- Aquí se generará dinámicamente el timeline -->
    </div>
</div>

<!-- Bootstrap JS (Incluye Popper y Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script personalizado -->
<script>
    document.getElementById('seguimientoForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const timelineContainer = document.getElementById('timeline');
        timelineContainer.innerHTML = ''; // Limpiar contenido previo

        // Obtener el número de solicitud desde el formulario
        const nroSolicitud = document.getElementById('nro_solicitud').value;

        try {
            // Realizar el fetch
            const response = await fetch('{{ route('seguimiento.obtener') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Agrega el token CSRF
                },
                body: JSON.stringify({ nro_solicitud: nroSolicitud })
            });

            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }

            const data = await response.json();

            const userData = data.userData;
            if (!userData || !userData.name || !userData.affiliate_number) {
                timelineContainer.innerHTML = '<p class="text-danger">No se encontraron registros</p>';
                return; // Detener la ejecución si no se encuentra información
            }
            const greetingMessage = `Buenos días ${userData.name}, Nro Afiliado: ${userData.affiliate_number}`;

            const greetingElement = document.createElement('h3');
            greetingElement.textContent = greetingMessage;

            // Cambié el ID de 'timeline-container' a 'timeline' para hacer coincidir con el contenedor correcto
            const searchContainer = document.getElementById('timeline');
            searchContainer.insertBefore(greetingElement, searchContainer.firstChild);

            // Crear el timeline dinámicamente
            const states = [
                { key: 'incoming', label: 'Pedido Ingresado' },
                { key: 'processed', label: 'En Procesamiento' },
                { key: 'readyToPick', label: 'Listo para Retiro' },
                { key: 'delivered', label: 'Entregado' },
            ];

            states.forEach((state, index) => {
                if (data[state.key]?.status) {
                    const createdAt = data[state.key].created_at ? formatDate(data[state.key].created_at) : 'N/A';
                    const updatedAt = data[state.key].updated_at ? formatDate(data[state.key].updated_at) : 'N/A';

                    // Verificar si ambas fechas son "N/A" o no existen
                    if (createdAt === 'N/A' && updatedAt === 'N/A') {
                        return; // Saltar esta iteración y no mostrar el estado
                    }

                    const timelineItem = document.createElement('div');
                    timelineItem.className = 'timeline-item';

                    // Crear línea
                    if (index < states.length - 1) {
                        const line = document.createElement('div');
                        line.className = 'line';
                        timelineItem.appendChild(line);
                    }

                    // Crear círculo
                    const circle = document.createElement('div');
                    circle.className = 'circle';
                    circle.textContent = index + 1;

                    // Crear descripción
                    const description = document.createElement('div');
                    description.className = 'description';
                    description.innerHTML = `
                        <strong>${state.label}</strong><br>
                        ${data[state.key].message || ''}<br>
                        Fecha creada: ${createdAt}<br>
                        Fecha actualizada: ${updatedAt}
                    `;

                    timelineItem.appendChild(circle);
                    timelineItem.appendChild(description);
                    timelineContainer.appendChild(timelineItem);
                }
            });

        } catch (error) {
            console.error(error);
            timelineContainer.innerHTML = '<p class="text-danger">Error al obtener los datos del seguimiento.</p>';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            };
            return new Intl.DateTimeFormat('es-AR', options).format(date);
        }
    });
</script>
</body>
</html>
