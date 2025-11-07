@extends('crudbooster::admin_template')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert@1.1.3/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert@1.1.3/dist/sweetalert.css">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-pills"></i> Consumos UP - Vista Personalizada
                </h3>
            </div>
            
            <!-- Filtros -->
            <div class="box-body">
                <!-- Botones de filtro rápido -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <a href="/admin/consumos_up_v2" class="btn btn-default {{ !request('estado_flujo') ? 'active' : '' }}">
                                <i class="fa fa-list"></i> Todos
                            </a>
                            <a href="/admin/consumos_up_v2?estado_flujo=pendiente" class="btn btn-warning {{ request('estado_flujo') == 'pendiente' ? 'active' : '' }}">
                                <i class="fa fa-clock-o"></i> Pendientes 
                                <span class="badge">{{ $contadores['pendientes'] }}</span>
                            </a>
                            <a href="/admin/consumos_up_v2?estado_flujo=aprobado" class="btn btn-success {{ request('estado_flujo') == 'aprobado' ? 'active' : '' }}">
                                <i class="fa fa-check"></i> Aprobados 
                                <span class="badge">{{ $contadores['aprobados'] }}</span>
                            </a>
                            <a href="/admin/consumos_up_v2?estado_flujo=entregado" class="btn btn-primary {{ request('estado_flujo') == 'entregado' ? 'active' : '' }}">
                                <i class="fa fa-truck"></i> Entregados 
                                <span class="badge">{{ $contadores['entregados'] }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <form method="GET" class="form-inline">
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Afiliado:</label>
                        <input type="text" name="afiliado" class="form-control" value="{{ request('afiliado') }}" placeholder="Código afiliado">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" class="form-control" value="{{ request('nombre') }}" placeholder="Nombre o apellido">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Estado:</label>
                        <select name="estado_flujo" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado_flujo') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="elegibilidad_ok" {{ request('estado_flujo') == 'elegibilidad_ok' ? 'selected' : '' }}>Elegibilidad OK</option>
                            <option value="aprobado" {{ request('estado_flujo') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="entregado" {{ request('estado_flujo') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Desde:</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Hasta:</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </form>
            </div>

            <!-- Tabla -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'fecha_tran', 'dir' => request('sort') == 'fecha_tran' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Fecha 
                                    @if(request('sort') == 'fecha_tran')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'afiliado', 'dir' => request('sort') == 'afiliado' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Afiliado
                                    @if(request('sort') == 'afiliado')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'apellidos', 'dir' => request('sort') == 'apellidos' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Nombre
                                    @if(request('sort') == 'apellidos')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Medicamento</th>
                            <th>Cant</th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'estado_flujo', 'dir' => request('sort') == 'estado_flujo' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Estado
                                    @if(request('sort') == 'estado_flujo')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'imptot', 'dir' => request('sort') == 'imptot' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Importe
                                    @if(request('sort') == 'imptot')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'updated_at', 'dir' => request('sort') == 'updated_at' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Última Actualización
                                    @if(request('sort') == 'updated_at')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consumos as $consumo)
                        <tr class="consumo-row 
                            @if($consumo->estado_flujo == 'entregado') success
                            @elseif($consumo->estado_flujo == 'aprobado') info
                            @elseif($consumo->estado_flujo == 'elegibilidad_no' || $consumo->estado_flujo == 'rechazado') danger
                            @elseif($consumo->estado_flujo == 'anulado') warning
                            @endif" 
                            data-id="{{ $consumo->id }}" style="cursor: pointer;">
                            <td>{{ $consumo->fecha_tran->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $consumo->afiliado }}</strong></td>
                            <td>{{ $consumo->apellidos }}, {{ $consumo->nombres }}</td>
                            <td>
                                <small>{{ Str::limit($consumo->desc, 40) }}</small><br>
                                <code>{{ $consumo->cod_prestacion }}</code>
                            </td>
                            <td><span class="badge badge-info">{{ $consumo->cant }}</span></td>
                            <td>
                                @php
                                    $badges = [
                                        'pendiente' => 'default',
                                        'elegibilidad_ok' => 'info',
                                        'elegibilidad_no' => 'danger',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        'entregado' => 'primary',
                                        'anulado' => 'warning',
                                    ];
                                    $color = $badges[$consumo->estado_flujo] ?? 'default';
                                    $texto = strtoupper(str_replace('_', ' ', $consumo->estado_flujo));
                                @endphp
                                <span class="label label-{{ $color }}">{{ $texto }}</span>
                            </td>
                            <td>${{ number_format($consumo->imptot, 2, ',', '.') }}</td>
                            <td>
                                <small>{{ $consumo->updated_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-info" onclick="verDetalle({{ $consumo->id }})">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="box-footer">
                {{ $consumos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-pills"></i> Detalle del Consumo
                </h4>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalle(id) {
    $('#modalDetalle').modal('show');
    
    $.get('/admin/consumos_up_v2/' + id, function(data) {
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Datos del Afiliado</strong></div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <tr><td><strong>Código:</strong></td><td>${data.afiliado}</td></tr>
                                <tr><td><strong>Nombre:</strong></td><td>${data.apellidos}, ${data.nombres}</td></tr>
                                <tr><td><strong>Plan:</strong></td><td>${data.nombre_modelo_plan || 'N/A'}</td></tr>
                                <tr><td><strong>Localidad:</strong></td><td>${data.localidad || 'N/A'}, ${data.provincia || 'N/A'}</td></tr>
                                <tr><td><strong>Edad:</strong></td><td>${data.edad || 'N/A'} años</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-success">
                        <div class="panel-heading"><strong>Datos del Medicamento</strong></div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <tr><td><strong>Código:</strong></td><td>${data.cod_prestacion}</td></tr>
                                <tr><td><strong>Descripción:</strong></td><td>${data.desc}</td></tr>
                                <tr><td><strong>Tipo:</strong></td><td>${data.tipo_pres}</td></tr>
                                <tr><td><strong>Cantidad:</strong></td><td>${data.cant}</td></tr>
                                <tr><td><strong>Importe:</strong></td><td>$${parseFloat(data.imptot).toLocaleString('es-AR', {minimumFractionDigits: 2})}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <strong>Consulta de Elegibilidad</strong>
                    <button class="btn btn-xs btn-default pull-right" onclick="toggleElegibilidad()">
                        <i class="fa fa-chevron-down" id="iconElegibilidad"></i>
                    </button>
                </div>
                <div class="panel-body" id="panelElegibilidad" style="display: none;">
                    ${getFormularioElegibilidad(data)}
                </div>
            </div>
            
            <div class="panel panel-primary">
                <div class="panel-heading"><strong>Estado del Flujo</strong></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Estado Actual:</strong> 
                                <span class="label label-${getBadgeColor(data.estado_flujo)}">${data.estado_flujo.toUpperCase().replace('_', ' ')}</span>
                            </p>
                            ${data.idaut ? '<p><strong>ID Autorización:</strong> <code>' + data.idaut + '</code></p>' : ''}
                            ${data.fecha_elegibilidad ? '<p><strong>Fecha Elegibilidad:</strong> ' + formatDate(data.fecha_elegibilidad) + '</p>' : ''}
                            ${data.fecha_aprobacion ? '<p><strong>Fecha Aprobación:</strong> ' + formatDate(data.fecha_aprobacion) + '</p>' : ''}
                            ${data.fecha_validacion_entrega ? '<p><strong>Fecha Entrega:</strong> ' + formatDate(data.fecha_validacion_entrega) + '</p>' : ''}
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group-vertical" style="width: 100%;">
                                ${getBotonesAccion(data)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#modalContent').html(html);
    }).fail(function() {
        $('#modalContent').html('<div class="alert alert-danger">Error al cargar los datos</div>');
    });
}

function getBadgeColor(estado) {
    const colors = {
        'pendiente': 'default',
        'elegibilidad_ok': 'info',
        'elegibilidad_no': 'danger',
        'aprobado': 'success',
        'rechazado': 'danger',
        'entregado': 'primary',
        'anulado': 'warning'
    };
    return colors[estado] || 'default';
}

function getBotonesAccion(data) {
    let botones = '';
    
    if (data.estado_flujo === 'pendiente' || data.estado_flujo === 'elegibilidad_no') {
        botones += `<button class="btn btn-info btn-block" onclick="toggleElegibilidad()">
            <i class="fa fa-user-check"></i> Consultar Elegibilidad
        </button>`;
        botones += `<button class="btn btn-warning btn-block" onclick="ejecutarAccion('aprobar_directo', ${data.id})">
            <i class="fa fa-fast-forward"></i> Aprobar Directo
        </button>`;
    }
    
    if (data.estado_flujo === 'elegibilidad_ok') {
        botones += `<button class="btn btn-success btn-block" onclick="ejecutarAccion('aprobar', ${data.id})">
            <i class="fa fa-check-circle"></i> Aprobar Prestación
        </button>`;
    }
    
    if (data.estado_flujo === 'aprobado') {
        botones += `<button class="btn btn-primary btn-block" onclick="ejecutarAccion('validar', ${data.id})">
            <i class="fa fa-clipboard-check"></i> Generar Validación
        </button>`;
    }
    
    return botones;
}

function ejecutarAccion(accion, id) {
    let url = '';
    let mensaje = '';
    
    switch(accion) {
        case 'aprobar':
            url = '/admin/consumos_up_v2/' + id + '/aprobar';
            mensaje = '¿Aprobar esta prestación?';
            break;
        case 'aprobar_elegibilidad':
            url = '/admin/consumos_up_v2/' + id + '/aprobar-elegibilidad';
            mensaje = '¿Aprobar esta prestación con elegibilidad verificada?';
            break;
        case 'aprobar_directo':
            url = '/admin/consumos_up_v2/' + id + '/aprobar-directo';
            mensaje = '¿Aprobar directamente sin verificar elegibilidad?';
            break;
        case 'validar':
            url = '/admin/consumos_up_v2/' + id + '/validar';
            mensaje = '¿Generar validación de entrega?';
            break;
    }
    
    swal({
        title: 'Confirmación',
        text: mensaje,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }, function(isConfirm) {
        if (isConfirm) {
            $.post(url, {_token: '{{ csrf_token() }}'}, function(response) {
                if (response.success) {
                    if (response.redirect) {
                        // Si hay redirección, ir a la URL
                        window.location.href = response.redirect;
                    } else {
                        swal('Éxito', response.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    swal('Error', response.message, 'error');
                }
            }).fail(function() {
                swal('Error', 'Error de conexión', 'error');
            });
        }
    });
}

function getFormularioElegibilidad(data) {
    return `
        <form id="formElegibilidad" class="form-horizontal">
            <input type="hidden" name="consumo_id" value="${data.id}">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Código Afiliado *</label>
                        <div class="col-sm-8">
                            <input type="text" name="codigo_afiliado" class="form-control" value="${data.afiliado}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">TOKEN</label>
                        <div class="col-sm-8">
                            <input type="text" name="token" class="form-control" placeholder="9999" value="9999">
                            <small class="text-muted">TOKEN de credencial digital</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Plan</label>
                        <div class="col-sm-8">
                            <input type="text" name="plan" class="form-control" placeholder="150" value="150">
                            <small class="text-muted">Solo si no usa TOKEN</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Versión Credencial</label>
                        <div class="col-sm-8">
                            <input type="text" name="vercred" class="form-control" placeholder="45" value="45">
                            <small class="text-muted">Solo si no usa TOKEN</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Consultar Elegibilidad
                    </button>
                    <button type="button" class="btn btn-default" onclick="limpiarFormElegibilidad()">
                        <i class="fa fa-eraser"></i> Limpiar
                    </button>
                </div>
            </div>
        </form>

        <div id="resultadoElegibilidad" style="margin-top: 20px; display: none;"></div>
    `;
}

function toggleElegibilidad() {
    const panel = $('#panelElegibilidad');
    const icon = $('#iconElegibilidad');
    
    if (panel.is(':visible')) {
        panel.slideUp();
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        panel.slideDown();
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        
        // Configurar el formulario si no está configurado
        setTimeout(() => {
            configurarFormularioElegibilidad();
        }, 300);
    }
}

function configurarFormularioElegibilidad() {
    $('#formElegibilidad').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const btnSubmit = $(this).find('button[type=submit]');
        const originalText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        
        $.ajax({
            url: '/admin/up_elegibilidad/consultar-elegibilidad',
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            success: function(response) {
                $('#resultadoElegibilidad').html(response.html).slideDown();
                btnSubmit.prop('disabled', false).html(originalText);
                
                // Si la elegibilidad es exitosa, agregar botón para aprobar
                if (response.success && response.data && response.data.status === 'OK') {
                    const consumoId = $('#formElegibilidad input[name=consumo_id]').val();
                    const btnAprobar = `
                        <div class="text-center" style="margin-top: 15px;">
                            <button class="btn btn-success btn-lg" onclick="aprobarConElegibilidad(${consumoId})">
                                <i class="fa fa-check-circle"></i> Aprobar Consumo
                            </button>
                        </div>
                    `;
                    $('#resultadoElegibilidad').append(btnAprobar);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al consultar elegibilidad';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                const errorHtml = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + errorMsg + '</div>';
                $('#resultadoElegibilidad').html(errorHtml).slideDown();
                btnSubmit.prop('disabled', false).html(originalText);
            }
        });
    });
}

function limpiarFormElegibilidad() {
    $('#formElegibilidad')[0].reset();
    $('#resultadoElegibilidad').slideUp();
}

function aprobarConElegibilidad(id) {
    ejecutarAccion('aprobar_elegibilidad', id);
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-AR') + ' ' + date.toLocaleTimeString('es-AR');
}

// Click en fila para ver detalle
$(document).ready(function() {
    $('.consumo-row').click(function() {
        const id = $(this).data('id');
        verDetalle(id);
    });
});
</script>

<style>
.consumo-row:hover {
    background-color: #f5f5f5;
}

.table-condensed td {
    padding: 4px 8px;
}

.btn-group-vertical .btn {
    margin-bottom: 5px;
}

.btn-group .btn.active {
    background-color: #337ab7;
    color: white;
}

th a {
    color: inherit;
    text-decoration: none;
}

th a:hover {
    color: #337ab7;
}

.fa-sort, .fa-sort-asc, .fa-sort-desc {
    margin-left: 5px;
}

.form-group {
    margin-bottom: 10px;
}

.form-group label {
    margin-right: 5px;
    font-weight: normal;
}
</style>
@endsection
