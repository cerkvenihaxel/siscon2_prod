<!DOCTYPE html>
<html>
<head>
    <title>Detalle del Consumo #{{ $consumo->id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('SISCON.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f5f5;
            margin-left: 250px;
            margin-top: 60px;
        }
        .main-container {
            margin-top: 20px;
            padding: 20px;
        }
        .info-card {
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
        }
        .card-header i {
            margin-right: 15px;
            font-size: 32px;
        }
        .card-header h5 {
            margin: 0;
            font-weight: 500;
        }
        .info-row {
            padding: 12px 24px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 500;
            color: #666;
            min-width: 150px;
        }
        .info-value {
            color: #333;
            font-weight: 400;
            text-align: right;
            flex: 1;
        }
        .badge-estado {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
        }
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-aprobado { background: #d4edda; color: #155724; }
        .estado-entregado { background: #d1ecf1; color: #0c5460; }
        .estado-anulado { background: #f8d7da; color: #721c24; }
        .estado-rechazado { background: #f8d7da; color: #721c24; }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
@include('components.up_topbar')
@include('components.up_sidebar')

<div class="container main-container">
    <div class="row">
        <div class="col s12">
            <a href="/admin/consumos-up" class="btn btn-back waves-effect waves-light">
                <i class="material-icons left">arrow_back</i>
                Volver al Listado
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card-panel teal lighten-5">
                <h4 class="teal-text text-darken-2">
                    <i class="material-icons left">description</i>
                    Detalle del Consumo #{{ $consumo->id }}
                </h4>
                <p class="grey-text">Información completa del consumo y transacción</p>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">person</i>
            <h5>Información del Afiliado</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">Código:</span>
                <span class="info-value">{{ $consumo->afiliado ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ trim($consumo->nombres . ' ' . $consumo->apellidos) ?: 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">
                    {{ $consumo->nombre_modelo_plan ?? 'N/A' }}
                    @if($consumo->modelo_plan)
                        ({{ $consumo->modelo_plan }})
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Localidad:</span>
                <span class="info-value">
                    {{ $consumo->localidad ?? 'N/A' }}
                    @if($consumo->provincia)
                        , {{ $consumo->provincia }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Edad:</span>
                <span class="info-value">
                    {{ $consumo->edad ?? 'N/A' }}
                    @if($consumo->edad)
                        años
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo Afiliado:</span>
                <span class="info-value">{{ $consumo->tipo_afiliado ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">local_pharmacy</i>
            <h5>Prestación Solicitada</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">Descripción:</span>
                <span class="info-value">{{ $consumo->desc ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Código:</span>
                <span class="info-value">{{ $consumo->cod_prestacion ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value">
                    @if($consumo->tipo_pres == 'P')
                        Prestación
                    @elseif($consumo->tipo_pres == 'M')
                        Medicamento
                    @else
                        {{ $consumo->tipo_pres }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Cantidad:</span>
                <span class="info-value">{{ $consumo->cant ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cargo:</span>
                <span class="info-value">${{ number_format($consumo->cargo ?? 0, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Importe OS:</span>
                <span class="info-value">${{ number_format($consumo->impos ?? 0, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Importe Total:</span>
                <span class="info-value"><strong>${{ number_format($consumo->imptot ?? 0, 2) }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total a Pagar:</span>
                <span class="info-value"><strong style="font-size: 18px; color: #d32f2f;">${{ number_format(($consumo->imptot ?? 0) + ($consumo->cargo ?? 0), 2) }}</strong></span>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">assignment</i>
            <h5>Estado de la Transacción</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    @php
                        $estadoClasses = [
                            'pendiente' => 'estado-pendiente',
                            'aprobado' => 'estado-aprobado',
                            'entregado' => 'estado-entregado',
                            'anulado' => 'estado-anulado',
                            'rechazado' => 'estado-rechazado',
                        ];
                        $estadoClass = $estadoClasses[$consumo->estado_flujo] ?? 'estado-pendiente';
                    @endphp
                    <span class="badge-estado {{ $estadoClass }}">{{ strtoupper($consumo->estado_flujo ?? 'PENDIENTE') }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">ID Transacción:</span>
                <span class="info-value">{{ $consumo->num_tran ?? $consumo->idtran_aprobacion ?? 'N/A' }}</span>
            </div>
            @if($consumo->idaut)
                <div class="info-row">
                    <span class="info-label">ID Autorización:</span>
                    <span class="info-value">{{ $consumo->idaut }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Fecha Transacción:</span>
                <span class="info-value">{{ $consumo->fecha_tran ? $consumo->fecha_tran->format('d/m/Y H:i:s') : 'N/A' }}</span>
            </div>
            @if($consumo->fecha_aprobacion)
                <div class="info-row">
                    <span class="info-label">Fecha Aprobación:</span>
                    <span class="info-value">{{ $consumo->fecha_aprobacion->format('d/m/Y H:i:s') }}</span>
                </div>
            @endif
            @if($consumo->usuario_aprobacion)
                <div class="info-row">
                    <span class="info-label">Usuario Aprobación:</span>
                    <span class="info-value">{{ $consumo->usuario_aprobacion }}</span>
                </div>
            @endif
            @if($consumo->fecha_entrega)
                <div class="info-row">
                    <span class="info-label">Fecha Entrega:</span>
                    <span class="info-value">{{ $consumo->fecha_entrega->format('d/m/Y H:i:s') }}</span>
                </div>
            @endif
            @if($consumo->usuario_entrega)
                <div class="info-row">
                    <span class="info-label">Usuario Entrega:</span>
                    <span class="info-value">{{ $consumo->usuario_entrega }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Nro Pedido:</span>
                <span class="info-value">{{ $consumo->nro_pedido ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Remito:</span>
                <span class="info-value">{{ $consumo->remito ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Transporte:</span>
                <span class="info-value">{{ $consumo->nro_transporte ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado Calculado:</span>
                <span class="info-value">{{ strtoupper(str_replace('_', ' ', $consumo->estado_calculado ?? 'pendiente')) }}</span>
            </div>
        </div>
    </div>

    @if($consumo->observaciones_flujo)
        <div class="card info-card">
            <div class="card-header">
                <i class="material-icons">note</i>
                <h5>Observaciones</h5>
            </div>
            <div class="card-content">
                <p>{{ $consumo->observaciones_flujo }}</p>
                @if($consumo->usuario_observaciones)
                    <p class="grey-text"><small>Por: {{ $consumo->usuario_observaciones }} - {{ $consumo->fecha_observaciones ? $consumo->fecha_observaciones->format('d/m/Y H:i') : '' }}</small></p>
                @endif
            </div>
        </div>
    @endif

    <!-- Acciones Principales -->
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        <i class="material-icons left">settings</i>
                        Acciones Disponibles
                    </span>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col s12 m3">
                            <button onclick="imprimirSolicitud()" class="btn blue waves-effect waves-light full-width">
                                <i class="material-icons left">print</i>
                                Imprimir Solicitud
                            </button>
                        </div>
                        @if($consumo->num_tran || $consumo->idtran_aprobacion)
                        <div class="col s12 m3">
                            <button onclick="verXMLConsumo()" class="btn purple waves-effect waves-light full-width">
                                <i class="material-icons left">code</i>
                                Ver XML
                            </button>
                        </div>
                        @endif
                        @if(in_array($consumo->estado_flujo, ['aprobado', 'entregado']) && !in_array($consumo->estado_calculado, ['anulado']))
                        <div class="col s12 m3">
                            <button onclick="mostrarModalAnulacion()" class="btn red waves-effect waves-light full-width">
                                <i class="material-icons left">cancel</i>
                                Anular Consumo
                            </button>
                        </div>
                        @endif
                        <div class="col s12 m3">
                            <a href="/admin/consumos-up" class="btn grey waves-effect waves-light full-width">
                                <i class="material-icons left">arrow_back</i>
                                Volver al Listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Anulación -->
<div id="modalAnulacion" class="modal">
    <div class="modal-content">
        <h4 class="red-text">
            <i class="material-icons left">warning</i>
            Anular Consumo
        </h4>
        <p>¿Está seguro que desea anular este consumo? Esta acción no se puede deshacer.</p>
        <form id="formAnulacion">
            <input type="hidden" name="consumo_id" value="{{ $consumo->id }}">
            <div class="row">
                <div class="input-field col s12">
                    <select name="motivo_anulacion" required>
                        <option value="">Seleccionar motivo</option>
                        <option value="ERROR_CARGA">Error en la carga</option>
                        <option value="CANCELACION_MEDICA">Cancelación médica</option>
                        <option value="NO_DISPONIBLE">Medicamento no disponible</option>
                        <option value="DUPLICADO">Registro duplicado</option>
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
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancelar</a>
        <button type="button" class="waves-effect waves-red btn red" onclick="confirmarAnulacion()">
            Confirmar Anulación
        </button>
    </div>
</div>

<!-- Modal XML -->
<div id="modalXML" class="modal modal-fixed-footer" style="width: 90%; max-width: 1200px;">
    <div class="modal-content">
        <h4><i class="material-icons left">code</i>XML de Transacción</h4>
        <div class="row">
            <div class="col s6">
                <h6 class="blue-text">XML Solicitud</h6>
                <pre id="xmlRequest" style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="col s6">
                <h6 class="green-text">XML Respuesta</h6>
                <pre id="xmlResponse" style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
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

function imprimirSolicitud() {
    window.open('/admin/consumos-up/{{ $consumo->id }}/imprimir', '_blank', 'width=800,height=600');
}

function verXMLConsumo() {
    $.ajax({
        url: '/admin/consumos-up/{{ $consumo->id }}/xml',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#xmlRequest').text(formatXml(response.xml_request || 'No disponible'));
                $('#xmlResponse').text(formatXml(response.xml_response || 'No disponible'));
                M.Modal.getInstance($('#modalXML')).open();
            } else {
                M.toast({html: 'Error al cargar XML: ' + response.message, classes: 'red'});
            }
        },
        error: function() {
            M.toast({html: 'Error al cargar XML', classes: 'red'});
        }
    });
}

function formatXml(xml) {
    if (!xml || xml === 'No disponible') return xml;
    return xml.replace(/></g, '>\n<').replace(/^\s*\n/gm, '').trim();
}

function mostrarModalAnulacion() {
    // Ir directamente a la vista de anulación ATR
    const consumoId = {{ $consumo->id }};
    
    $.ajax({
        url: '/admin/consumos-up/anular',
        method: 'POST',
        data: {
            consumo_id: consumoId,
            motivo_anulacion: 'Anulación desde vista detalle de consumo'
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

function confirmarAnulacion() {
    const formData = new FormData($('#formAnulacion')[0]);

    $.ajax({
        url: '/admin/consumos-up/anular',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                M.toast({html: 'Consumo anulado exitosamente', classes: 'green'});
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                M.toast({html: response.message || 'Error al anular consumo', classes: 'red'});
            }
        },
        error: function() {
            M.toast({html: 'Error al anular consumo', classes: 'red'});
        }
    });
}
</script>

</body>
</html>
