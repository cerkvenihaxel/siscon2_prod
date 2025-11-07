<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Anulación #{{ $anulacion->id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
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
        .estado-ok { background: #d4edda; color: #155724; }
        .estado-no { background: #f8d7da; color: #721c24; }
        .estado-error { background: #f8d7da; color: #721c24; }
        .estado-pend { background: #fff3cd; color: #856404; }
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
            <a href="/admin/anulaciones-up" class="btn btn-back waves-effect waves-light">
                <i class="material-icons left">arrow_back</i>
                Volver al Listado
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card-panel red lighten-5">
                <h4 class="red-text text-darken-2">
                    <i class="material-icons left">cancel</i>
                    Detalle de Anulación #{{ $anulacion->id }}
                </h4>
                <p class="grey-text">Información completa de la anulación ATR</p>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">info</i>
            <h5>Información General</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">Fecha de Anulación:</span>
                <span class="info-value">{{ $anulacion->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    <span class="badge-estado estado-{{ strtolower($anulacion->status) }}">
                        {{ $anulacion->status }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo de Anulación:</span>
                <span class="info-value">{{ $anulacion->tipoidanul }} - {{ $anulacion->tipo_anulacion_texto }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Usuario:</span>
                <span class="info-value">{{ $anulacion->usuario_creador ?: 'Sistema' }}</span>
            </div>
            @if($anulacion->execution_time_ms)
            <div class="info-row">
                <span class="info-label">Tiempo de Ejecución:</span>
                <span class="info-value">{{ number_format($anulacion->execution_time_ms, 2) }} ms</span>
            </div>
            @endif
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
                <span class="info-value">{{ $anulacion->codigo_afiliado }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ $anulacion->nombre_completo ?: 'N/A' }}</span>
            </div>
            @if($anulacion->plan)
            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">{{ $anulacion->plan }}</span>
            </div>
            @endif
            @if($anulacion->token)
            <div class="info-row">
                <span class="info-label">Token:</span>
                <span class="info-value">{{ $anulacion->token }}</span>
            </div>
            @endif
            @if($anulacion->vercred)
            <div class="info-row">
                <span class="info-label">Versión Credencial:</span>
                <span class="info-value">{{ $anulacion->vercred }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">swap_horiz</i>
            <h5>Información de Transacciones</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">ID Anulado:</span>
                <span class="info-value">{{ $anulacion->idanul }}</span>
            </div>
            @if($anulacion->idtran)
            <div class="info-row">
                <span class="info-label">ID Transacción ATR:</span>
                <span class="info-value">{{ $anulacion->idtran }}</span>
            </div>
            @endif
            @if($anulacion->msgid)
            <div class="info-row">
                <span class="info-label">Message ID:</span>
                <span class="info-value">{{ $anulacion->msgid }}</span>
            </div>
            @endif
            @if($anulacion->idaut_anulado)
            <div class="info-row">
                <span class="info-label">ID Autorización Anulado:</span>
                <span class="info-value">{{ $anulacion->idaut_anulado }}</span>
            </div>
            @endif
            @if($anulacion->response_code)
            <div class="info-row">
                <span class="info-label">Código de Respuesta:</span>
                <span class="info-value">{{ $anulacion->response_code }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">message</i>
            <h5>Motivo y Respuesta</h5>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="info-row">
                <span class="info-label">Motivo:</span>
                <span class="info-value">{{ $anulacion->motivo }}</span>
            </div>
            @if($anulacion->response_message)
            <div class="info-row">
                <span class="info-label">Mensaje de Respuesta:</span>
                <span class="info-value">{{ $anulacion->response_message }}</span>
            </div>
            @endif
            @if($anulacion->observaciones)
            <div class="info-row">
                <span class="info-label">Observaciones:</span>
                <span class="info-value">{{ $anulacion->observaciones }}</span>
            </div>
            @endif
            @if($anulacion->fecha_anulacion)
            <div class="info-row">
                <span class="info-label">Fecha de Anulación:</span>
                <span class="info-value">{{ $anulacion->fecha_anulacion->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons">settings</i>
            <h5>Acciones Disponibles</h5>
        </div>
        <div class="card-content">
            <div class="row" style="margin-top: 20px;">
                <div class="col s12 m3">
                    <a href="{{ url('/admin/anulaciones-up/imprimir/' . $anulacion->id) }}" target="_blank" class="btn blue waves-effect waves-light full-width">
                        <i class="material-icons left">print</i>
                        Imprimir Solicitud
                    </a>
                </div>
                @if($anulacion->idtran)
                <div class="col s12 m3">
                    <a href="{{ url('/admin/anulaciones-up/xml/' . $anulacion->id) }}" class="btn purple waves-effect waves-light full-width">
                        <i class="material-icons left">code</i>
                        Ver XML
                    </a>
                </div>
                @endif
                <div class="col s12 m3">
                    <a href="{{ url('/admin/anulacion-up') }}" class="btn green waves-effect waves-light full-width">
                        <i class="material-icons left">add</i>
                        Nueva Anulación
                    </a>
                </div>
                <div class="col s12 m3">
                    <a href="{{ url('/admin/anulaciones-up') }}" class="btn grey waves-effect waves-light full-width">
                        <i class="material-icons left">arrow_back</i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        M.AutoInit();
    });
</script>
</body>
</html>
