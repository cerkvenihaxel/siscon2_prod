<!DOCTYPE html>
<html>
<head>
    <title>Solicitud Completa - Consumo #{{ $consumo->id }}</title>
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
        .header img {
            max-width: 200px;
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
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 5px;
        }
        .row {
            display: flex;
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            width: 200px;
        }
        .value {
            flex: 1;
        }
        .divider {
            border-top: 1px dashed #666;
            margin: 15px 0;
        }
        .footer {
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border: 2px solid #333;
            font-weight: bold;
            margin: 10px 0;
        }
        @media print {
            body {
                padding: 0;
            }
            .ticket {
                border: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('siscon.png') }}" alt="SISCON Logo">
            <h1>SOLICITUD COMPLETA DE PRESTACIÓN</h1>
            <h2>Unión Personal - Sistema SISCON</h2>
            <p>ID Consumo: #{{ $consumo->id }}</p>
            <p>Fecha de Impresión: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Información del Afiliado -->
        <div class="section">
            <div class="section-title">DATOS DEL AFILIADO</div>
            <div class="row">
                <span class="label">Código Afiliado:</span>
                <span class="value">{{ $consumo->afiliado }}</span>
            </div>
            <div class="row">
                <span class="label">Apellidos:</span>
                <span class="value">{{ $consumo->apellidos }}</span>
            </div>
            <div class="row">
                <span class="label">Nombres:</span>
                <span class="value">{{ $consumo->nombres }}</span>
            </div>
            <div class="row">
                <span class="label">Plan:</span>
                <span class="value">{{ $consumo->nombre_modelo_plan }} ({{ $consumo->modelo_plan }})</span>
            </div>
            <div class="row">
                <span class="label">Localidad:</span>
                <span class="value">{{ $consumo->localidad }}{{ $consumo->provincia ? ', ' . $consumo->provincia : '' }}</span>
            </div>
            <div class="row">
                <span class="label">Edad:</span>
                <span class="value">{{ $consumo->edad }} años</span>
            </div>
            <div class="row">
                <span class="label">Tipo Afiliado:</span>
                <span class="value">{{ $consumo->tipo_afiliado }}</span>
            </div>
        </div>

        <!-- Información de la Prestación -->
        <div class="section">
            <div class="section-title">DATOS DE LA PRESTACIÓN</div>
            <div class="row">
                <span class="label">Código Prestación:</span>
                <span class="value">{{ $consumo->cod_prestacion }}</span>
            </div>
            <div class="row">
                <span class="label">Descripción:</span>
                <span class="value">{{ $consumo->desc }}</span>
            </div>
            <div class="row">
                <span class="label">Tipo:</span>
                <span class="value">
                    @if($consumo->tipo_pres == 'P')
                        Prestación
                    @elseif($consumo->tipo_pres == 'M')
                        Medicamento
                    @else
                        {{ $consumo->tipo_pres }}
                    @endif
                </span>
            </div>
            <div class="row">
                <span class="label">Cantidad:</span>
                <span class="value">{{ $consumo->cant }}</span>
            </div>
        </div>

        <!-- Información Económica -->
        <div class="section">
            <div class="section-title">INFORMACIÓN ECONÓMICA</div>
            <div class="row">
                <span class="label">Cargo:</span>
                <span class="value">${{ number_format($consumo->cargo ?? 0, 2) }}</span>
            </div>
            <div class="row">
                <span class="label">Importe OS:</span>
                <span class="value">${{ number_format($consumo->impos ?? 0, 2) }}</span>
            </div>
            <div class="row">
                <span class="label">Importe Total:</span>
                <span class="value"><strong>${{ number_format($consumo->imptot ?? 0, 2) }}</strong></span>
            </div>
            <div class="row">
                <span class="label">Total a Pagar:</span>
                <span class="value"><strong style="font-size: 16px;">${{ number_format(($consumo->imptot ?? 0) + ($consumo->cargo ?? 0), 2) }}</strong></span>
            </div>
        </div>

        <!-- Estado de la Transacción -->
        <div class="section">
            <div class="section-title">ESTADO DE LA TRANSACCIÓN</div>
            <div class="row">
                <span class="label">Estado Actual:</span>
                <span class="value"><span class="status-badge">{{ strtoupper($consumo->estado_flujo) }}</span></span>
            </div>
            @if($consumo->num_tran || $consumo->idtran_aprobacion)
            <div class="row">
                <span class="label">ID Transacción:</span>
                <span class="value">{{ $consumo->num_tran ?? $consumo->idtran_aprobacion }}</span>
            </div>
            @endif
            @if($consumo->idaut)
            <div class="row">
                <span class="label">ID Autorización:</span>
                <span class="value">{{ $consumo->idaut }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">Fecha Transacción:</span>
                <span class="value">{{ $consumo->fecha_tran ? $consumo->fecha_tran->format('d/m/Y H:i:s') : 'N/A' }}</span>
            </div>
            @if($consumo->fecha_aprobacion)
            <div class="row">
                <span class="label">Fecha Aprobación:</span>
                <span class="value">{{ $consumo->fecha_aprobacion->format('d/m/Y H:i:s') }}</span>
            </div>
            @endif
            @if($consumo->usuario_aprobacion)
            <div class="row">
                <span class="label">Usuario Aprobación:</span>
                <span class="value">{{ $consumo->usuario_aprobacion }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">Prestador:</span>
                <span class="value">{{ $consumo->cod_prestador }}</span>
            </div>
            <div class="row">
                <span class="label">Aplicación:</span>
                <span class="value">{{ $consumo->emisor_app }}</span>
            </div>
        </div>

        <!-- Información de Logística (si existe) -->
        @if($consumo->nro_pedido || $consumo->remito || $consumo->nro_transporte)
        <div class="section">
            <div class="section-title">INFORMACIÓN DE LOGÍSTICA</div>
            @if($consumo->nro_pedido)
            <div class="row">
                <span class="label">Nro. Pedido:</span>
                <span class="value">{{ $consumo->nro_pedido }}</span>
            </div>
            @endif
            @if($consumo->remito)
            <div class="row">
                <span class="label">Remito:</span>
                <span class="value">{{ $consumo->remito }}</span>
            </div>
            @endif
            @if($consumo->nro_transporte)
            <div class="row">
                <span class="label">Nro. Transporte:</span>
                <span class="value">{{ $consumo->nro_transporte }}</span>
            </div>
            @endif
            @if($consumo->fecha_entrega)
            <div class="row">
                <span class="label">Fecha Entrega:</span>
                <span class="value">{{ $consumo->fecha_entrega->format('d/m/Y H:i:s') }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Observaciones (si existen) -->
        @if($consumo->observaciones_flujo)
        <div class="section">
            <div class="section-title">OBSERVACIONES</div>
            <p style="margin: 10px 0;">{{ $consumo->observaciones_flujo }}</p>
            @if($consumo->usuario_observaciones)
            <p style="font-size: 10px; margin-top: 5px;">
                Por: {{ $consumo->usuario_observaciones }} -
                {{ $consumo->fecha_observaciones ? $consumo->fecha_observaciones->format('d/m/Y H:i') : '' }}
            </p>
            @endif
        </div>
        @endif

        <div class="footer">
            <p>Este documento es una copia impresa de la solicitud de prestación.</p>
            <p>Para verificar la autenticidad, consulte en el sistema SISCON con el ID: {{ $consumo->id }}</p>
            <p style="margin-top: 10px;">Impreso: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Imprimir Documento
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Cerrar Ventana
        </button>
    </div>

    <script>
        // Auto-imprimir cuando se carga la página (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
