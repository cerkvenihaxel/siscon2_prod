<!DOCTYPE html>
<html>
<head>
    <title>Comprobante de Anulación - #{{ $anulacion->id }}</title>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            background: white;
        }
        .ticket {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #333;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        .label {
            font-weight: bold;
            width: 40%;
        }
        .value {
            width: 60%;
            text-align: right;
        }
        .status-ok { background: #d4edda; color: #155724; padding: 2px 8px; }
        .status-no { background: #f8d7da; color: #721c24; padding: 2px 8px; }
        .status-error { background: #f8d7da; color: #721c24; padding: 2px 8px; }
        .status-pend { background: #fff3cd; color: #856404; padding: 2px 8px; }
        .footer {
            text-align: center;
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 20px;
            font-size: 10px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <h1>GLOBAL MÉDICA S.A.</h1>
            <h2>COMPROBANTE DE ANULACIÓN UP</h2>
            <p>Sistema de Gestión - Unión Personal</p>
            <p>Fecha de Emisión: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Información General -->
        <div class="section">
            <div class="section-title">Información General</div>
            <div class="row">
                <span class="label">ID Anulación:</span>
                <span class="value">#{{ $anulacion->id }}</span>
            </div>
            <div class="row">
                <span class="label">Fecha de Anulación:</span>
                <span class="value">{{ $anulacion->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="row">
                <span class="label">Estado:</span>
                <span class="value">
                    <span class="status-{{ strtolower($anulacion->status) }}">{{ $anulacion->status }}</span>
                </span>
            </div>
            <div class="row">
                <span class="label">Tipo de Anulación:</span>
                <span class="value">{{ $anulacion->tipoidanul }} - {{ $anulacion->tipo_anulacion_texto }}</span>
            </div>
            <div class="row">
                <span class="label">Usuario:</span>
                <span class="value">{{ $anulacion->usuario_creador ?: 'Sistema' }}</span>
            </div>
        </div>

        <!-- Información del Afiliado -->
        <div class="section">
            <div class="section-title">Información del Afiliado</div>
            <div class="row">
                <span class="label">Código Afiliado:</span>
                <span class="value">{{ $anulacion->codigo_afiliado }}</span>
            </div>
            @if($anulacion->afi_apellido || $anulacion->afi_nombre)
            <div class="row">
                <span class="label">Nombre Completo:</span>
                <span class="value">{{ $anulacion->nombre_completo }}</span>
            </div>
            @endif
            @if($anulacion->plan)
            <div class="row">
                <span class="label">Plan:</span>
                <span class="value">{{ $anulacion->plan }}</span>
            </div>
            @endif
        </div>

        <!-- Información de Transacciones -->
        <div class="section">
            <div class="section-title">Información de Transacciones</div>
            <div class="row">
                <span class="label">ID Anulado:</span>
                <span class="value">{{ $anulacion->idanul }}</span>
            </div>
            @if($anulacion->idtran)
            <div class="row">
                <span class="label">ID Transacción ATR:</span>
                <span class="value">{{ $anulacion->idtran }}</span>
            </div>
            @endif
            @if($anulacion->msgid)
            <div class="row">
                <span class="label">Message ID:</span>
                <span class="value">{{ $anulacion->msgid }}</span>
            </div>
            @endif
            @if($anulacion->idaut_anulado)
            <div class="row">
                <span class="label">ID Autorización Anulado:</span>
                <span class="value">{{ $anulacion->idaut_anulado }}</span>
            </div>
            @endif
        </div>

        <!-- Motivo y Respuesta -->
        <div class="section">
            <div class="section-title">Motivo y Respuesta</div>
            <div class="row">
                <span class="label">Motivo:</span>
                <span class="value">{{ $anulacion->motivo }}</span>
            </div>
            @if($anulacion->response_message)
            <div class="row">
                <span class="label">Respuesta UP:</span>
                <span class="value">{{ $anulacion->response_message }}</span>
            </div>
            @endif
            @if($anulacion->response_code)
            <div class="row">
                <span class="label">Código Respuesta:</span>
                <span class="value">{{ $anulacion->response_code }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este comprobante certifica la anulación de la transacción especificada</p>
            <p>Sistema SISCON - Global Médica S.A.</p>
            <p>Documento generado automáticamente - No requiere firma</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Imprimir</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Cerrar</button>
    </div>
</body>
</html>
