<!DOCTYPE html>
<html>
<head>
    <title>Consumos UP - Sistema de Gestión</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Material UI CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-content { padding: 20px; }
        .stats-card { margin-bottom: 20px; }
        .filter-card { margin-bottom: 20px; }
        .table-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .status-chip { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .status-pendiente { background: #fff3cd; color: #856404; }
        .status-procesado { background: #ffeaa7; color: #d63031; }
        .status-remitado { background: #fdcb6e; color: #e17055; }
        .status-en_transito { background: #e17055; color: white; }
        .status-entregado { background: #00b894; color: white; }
        .status-anulado { background: #d63031; color: white; }
        .btn-action { margin: 2px; }
        .pagination-wrapper { display: inline-flex; align-items: center; }
        .pagination-wrapper .btn-flat { margin: 0 2px; min-width: 36px; height: 36px; line-height: 36px; padding: 0; text-align: center; }
        .pagination-wrapper .disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    @include('components.up_topbar')
    @include('components.up_sidebar')

    <div class="main-content">
        <!-- Header -->
        <div class="row">
            <div class="col s12">
                <div class="card-panel teal lighten-5">
                    <h4 class="teal-text text-darken-2">
                        <i class="material-icons left">list</i>
                        Consumos UP - Gestión de Entregas
                    </h4>
                    <p class="grey-text">Administración de consumos y validación de entregas</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row stats-card">
            <div class="col s12 m2">
                <a href="?estado_flujo=pendiente" class="card blue lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="blue-text text-darken-2">{{ $contadores['pendientes'] ?? 0 }}</h5>
                        <p class="blue-text text-darken-1">Pendientes</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m2">
                <a href="?estado_flujo=procesado" class="card orange lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="orange-text text-darken-2">{{ $contadores['procesados'] ?? 0 }}</h5>
                        <p class="orange-text text-darken-1">Procesados</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m2">
                <a href="?estado_flujo=remitado" class="card yellow lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="yellow-text text-darken-3">{{ $contadores['remitados'] ?? 0 }}</h5>
                        <p class="yellow-text text-darken-3">Remitados</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m1">
                <a href="?estado_flujo=en_transito" class="card purple lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="purple-text text-darken-2">{{ $contadores['en_transito'] ?? 0 }}</h5>
                        <p class="purple-text text-darken-1">Tránsito</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m2">
                <a href="?estado_flujo=entregado" class="card green lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="green-text text-darken-2">{{ $contadores['entregados'] ?? 0 }}</h5>
                        <p class="green-text text-darken-1">Entregados</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m1">
                <a href="?estado_flujo=anulado" class="card red lighten-4 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="red-text text-darken-2">{{ $contadores['anulados'] ?? 0 }}</h5>
                        <p class="red-text text-darken-1">Anulados</p>
                    </div>
                </a>
            </div>
            <div class="col s12 m2">
                <a href="?" class="card grey lighten-3 waves-effect waves-light" style="display: block; text-decoration: none;">
                    <div class="card-content center">
                        <h5 class="grey-text text-darken-2">{{ $consumos->total() }}</h5>
                        <p class="grey-text text-darken-1">Total</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card filter-card">
            <div class="card-content">
                <form method="GET" id="filtrosForm">
                    <div class="row">
                        <div class="input-field col s12 m2">
                            <input id="afiliado" name="afiliado" type="text" value="{{ request('afiliado') }}">
                            <label for="afiliado">Código Afiliado</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="nombre" name="nombre" type="text" value="{{ request('nombre') }}">
                            <label for="nombre">Nombre/Apellido</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <select name="estado_flujo">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" {{ request('estado_flujo') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobado" {{ request('estado_flujo') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                <option value="entregado" {{ request('estado_flujo') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            </select>
                            <label>Estado</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="fecha_desde" name="fecha_desde" type="date" value="{{ request('fecha_desde') }}">
                            <label for="fecha_desde">Desde</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ request('fecha_hasta') }}">
                            <label for="fecha_hasta">Hasta</label>
                        </div>
                        <div class="col s12 m2">
                            <button type="submit" class="btn waves-effect waves-light full-width" style="margin-top: 25px;">
                                <i class="material-icons left">search</i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Consumos -->
        <div class="card table-card">
            <div class="card-content">
                <div class="card-title">
                    <i class="material-icons left">table_chart</i>
                    Lista de Consumos
                </div>
                
                <div class="table-responsive">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Afiliado</th>
                                <th>Nombre</th>
                                <th>Medicamento</th>
                                <th>Cantidad</th>
                                <th>Nro Pedido</th>
                                <th>Remito</th>
                                <th>Transporte</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consumos as $consumo)
                            <tr>
                                <td>{{ $consumo->fecha_tran ? $consumo->fecha_tran->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $consumo->afiliado }}</td>
                                <td>{{ $consumo->apellidos }}, {{ $consumo->nombres }}</td>
                                <td>{{ $consumo->desc }}</td>
                                <td>{{ $consumo->cant }}</td>
                                <td>{{ $consumo->nro_pedido ?? '-' }}</td>
                                <td>{{ $consumo->remito ?? '-' }}</td>
                                <td>{{ $consumo->nro_transporte ?? '-' }}</td>
                                <td>
                                    <span class="status-chip status-{{ $consumo->estado_calculado }}">
                                        {{ strtoupper(str_replace('_', ' ', $consumo->estado_calculado)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-small blue waves-effect waves-light btn-action tooltipped"
                                            data-position="top"
                                            data-tooltip="Vista Rápida"
                                            onclick="verDetalle({{ $consumo->id }})">
                                        <i class="material-icons">visibility</i>
                                    </button>
                                    <a href="/admin/consumos-up/{{ $consumo->id }}"
                                       class="btn btn-small teal waves-effect waves-light btn-action tooltipped"
                                       data-position="top"
                                       data-tooltip="Vista Completa"
                                       target="_blank">
                                        <i class="material-icons">description</i>
                                    </a>
                                    @if($consumo->estado_calculado == 'en_transito')
                                    <a href="/admin/entregas-up/add/{{ $consumo->id }}"
                                       class="btn btn-small green waves-effect waves-light btn-action tooltipped"
                                       data-position="top"
                                       data-tooltip="Marcar como Entregado">
                                        <i class="material-icons">check</i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="center">No hay consumos para mostrar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="row" style="margin-top: 20px; align-items: center;">
                    <div class="col s6">
                        <span class="grey-text">
                            Mostrando {{ $consumos->firstItem() ?? 0 }} - {{ $consumos->lastItem() ?? 0 }} de {{ $consumos->total() }} registros
                            (Página {{ $consumos->currentPage() }} de {{ $consumos->lastPage() }})
                        </span>
                    </div>
                    <div class="col s6 right-align">
                        @if ($consumos->hasPages())
                            <div class="pagination-wrapper">
                                @if ($consumos->onFirstPage())
                                    <span class="btn-flat disabled grey-text">‹</span>
                                @else
                                    <a href="{{ $consumos->previousPageUrl() }}" class="btn-flat waves-effect">‹</a>
                                @endif
                                
                                <span class="btn-flat disabled">{{ $consumos->currentPage() }}</span>
                                
                                @if ($consumos->hasMorePages())
                                    <a href="{{ $consumos->nextPageUrl() }}" class="btn-flat waves-effect">›</a>
                                @else
                                    <span class="btn-flat disabled grey-text">›</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle -->
    <div id="modalDetalle" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>Detalle del Consumo</h4>
            <div id="detalleContent"></div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
        </div>
    </div>

    <!-- Modal Entrega -->
    <div id="modalEntrega" class="modal">
        <div class="modal-content">
            <h4>Marcar como Entregado</h4>
            <form id="formEntrega">
                <input type="hidden" id="consumo_id" name="consumo_id">
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="fecha_entrega" name="fecha_entrega" type="date" required>
                        <label for="fecha_entrega">Fecha de Entrega</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="cantidad_entregada" name="cantidad_entregada" type="number" min="1" required>
                        <label for="cantidad_entregada">Cantidad Entregada</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <select name="estado_entrega" required>
                            <option value="completa">Entrega Completa</option>
                            <option value="parcial">Entrega Parcial</option>
                        </select>
                        <label>Estado de Entrega</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="observaciones" name="observaciones" class="materialize-textarea"></textarea>
                        <label for="observaciones">Observaciones</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cancelar</a>
            <button type="button" class="waves-effect waves-green btn" onclick="confirmarEntrega()">Confirmar Entrega</button>
        </div>
    </div>

    <!-- Modal Anulación -->
    <div id="modalAnulacion" class="modal">
        <div class="modal-content">
            <h4 class="red-text">Anular Consumo</h4>
            <form id="formAnulacion">
                <input type="hidden" id="consumo_anular_id" name="consumo_id">
                <div class="row">
                    <div class="input-field col s12">
                        <select name="motivo_anulacion" required>
                            <option value="">Seleccionar motivo</option>
                            <option value="ERROR_CARGA">Error en la carga</option>
                            <option value="CANCELACION_MEDICA">Cancelación médica</option>
                            <option value="NO_DISPONIBLE">Medicamento no disponible</option>
                            <option value="OTRO">Otro motivo</option>
                        </select>
                        <label>Motivo de Anulación</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="observaciones_anulacion" name="observaciones" class="materialize-textarea" required></textarea>
                        <label for="observaciones_anulacion">Observaciones</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cancelar</a>
            <button type="button" class="waves-effect waves-red btn red" onclick="confirmarAnulacion()">Anular Consumo</button>
        </div>
    </div>

    <!-- Modal XML -->
    <div id="modalXML" class="modal modal-fixed-footer" style="width: 80%; height: 80%;">
        <div class="modal-content">
            <h4>XML Request y Response</h4>
            <div class="row">
                <div class="col s12">
                    <ul class="tabs">
                        <li class="tab col s6"><a href="#xml-request" class="active">Request XML</a></li>
                        <li class="tab col s6"><a href="#xml-response">Response XML</a></li>
                    </ul>
                </div>
                <div id="xml-request" class="col s12">
                    <pre id="xmlRequestContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 400px;"></pre>
                </div>
                <div id="xml-response" class="col s12">
                    <pre id="xmlResponseContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 400px;"></pre>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            M.AutoInit();
            
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        function verDetalle(id) {
            $.ajax({
                url: `/admin/consumos-up/${id}`,
                method: 'GET',
                success: function(response) {
                    const fechaTran = new Date(response.fecha_tran).toLocaleDateString('es-AR') + ' ' + new Date(response.fecha_tran).toLocaleTimeString('es-AR');
                    const precio = response.precio ? `$${parseFloat(response.precio).toFixed(2)}` : 'N/A';
                    const estadoCalculado = response.estado_calculado || response.estado_flujo || 'pendiente';
                    const idTran = response.num_tran ? response.num_tran : 'N/A';
                    
                    $('#detalleContent').html(`
                        <div class="row">
                            <div class="col s12">
                                <div class="card-panel grey lighten-5">
                                    <h6 class="blue-text"><i class="material-icons left">person</i>Información del Afiliado</h6>
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col s6"><strong>Código:</strong> ${response.afiliado}</div>
                                        <div class="col s6"><strong>Nombre:</strong> ${response.nombres} ${response.apellidos}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col s12">
                                <div class="card-panel grey lighten-5">
                                    <h6 class="green-text"><i class="material-icons left">local_pharmacy</i>Prestación Solicitada</h6>
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col s12"><strong>Descripción:</strong> ${response.desc}</div>
                                        <div class="col s4"><strong>Código:</strong> ${response.cod_prestacion}</div>
                                        <div class="col s4"><strong>Cantidad:</strong> ${response.cant}</div>
                                        <div class="col s4"><strong>Precio:</strong> ${precio}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col s12">
                                <div class="card-panel grey lighten-5">
                                    <h6 class="orange-text"><i class="material-icons left">assignment</i>Estado de la Transacción</h6>
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col s6"><strong>Estado:</strong> <span class="status-chip status-${estadoCalculado}">${estadoCalculado.toUpperCase().replace('_', ' ')}</span></div>
                                        <div class="col s6"><strong>ID Transacción:</strong> ${idTran}</div>
                                        <div class="col s6"><strong>Fecha:</strong> ${fechaTran}</div>
                                        <div class="col s6"><strong>Usuario:</strong> ${response.usuario_carga || 'Sistema'}</div>
                                        <div class="col s3"><strong>Nro Pedido:</strong> ${response.nro_pedido || 'N/A'}</div>
                                        <div class="col s3"><strong>Remito:</strong> ${response.remito || 'N/A'}</div>
                                        <div class="col s3"><strong>Transporte:</strong> ${response.nro_transporte || 'N/A'}</div>
                                        <div class="col s3"><strong>Estado:</strong> ${response.estado_flujo || 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            ${response.remito || response.nro_pedido || response.nro_transporte ? `
                            <div class="col s12">
                                <div class="card-panel grey lighten-5">
                                    <h6 class="purple-text"><i class="material-icons left">local_shipping</i>Información de Entrega</h6>
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col s4"><strong>Nro. Pedido:</strong> ${response.nro_pedido || 'N/A'}</div>
                                        <div class="col s4"><strong>Remito:</strong> ${response.remito || 'N/A'}</div>
                                        <div class="col s4"><strong>Transporte:</strong> ${response.nro_transporte || 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `);
                    M.Modal.getInstance($('#modalDetalle')).open();
                },
                error: function() {
                    M.toast({html: 'Error al cargar detalle', classes: 'red'});
                }
            });
        }

        function marcarEntregado(id) {
            $('#consumo_id').val(id);
            $('#fecha_entrega').val(new Date().toISOString().split('T')[0]);
            M.Modal.getInstance($('#modalEntrega')).open();
        }

        function confirmarEntrega() {
            const formData = new FormData($('#formEntrega')[0]);
            
            $.ajax({
                url: '/admin/consumos-up/marcar-entregado',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        M.toast({html: 'Entrega registrada exitosamente', classes: 'green'});
                        M.Modal.getInstance($('#modalEntrega')).close();
                        location.reload();
                    } else {
                        M.toast({html: response.message, classes: 'red'});
                    }
                },
                error: function() {
                    M.toast({html: 'Error al registrar entrega', classes: 'red'});
                }
            });
        }
        function anularConsumo(id) {
            // Ir directamente a la vista de anulación ATR
            $.ajax({
                url: '/admin/consumos-up/anular',
                method: 'POST',
                data: {
                    consumo_id: id,
                    motivo_anulacion: 'Anulación desde Consumos UP'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        M.toast({html: response.message, classes: 'red'});
                    }
                },
                error: function() {
                    M.toast({html: 'Error al procesar anulación', classes: 'red'});
                }
            });
        }

        function verXML(consumoId) {
            $.ajax({
                url: `/admin/consumos-up/${consumoId}/xml`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#xmlRequestContent').text(response.xml_request || 'No disponible');
                        $('#xmlResponseContent').text(response.xml_response || 'No disponible');
                        M.Modal.getInstance($('#modalXML')).open();
                        $('ul.tabs').tabs();
                    } else {
                        M.toast({html: 'Error al cargar XML: ' + response.message, classes: 'red'});
                    }
                },
                error: function() {
                    M.toast({html: 'Error en la solicitud', classes: 'red'});
                }
            });
        }
    </script>
</body>
</html>
