<!DOCTYPE html>
<html>
<head>
    <title>Ticket de Rechazo - Transacción AP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        .ticket { max-width: 400px; margin: 0 auto; border: 1px solid #000; padding: 15px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #666; }
        .section { margin: 15px 0; padding: 10px 0; border-top: 1px dashed #ccc; }
        .field { margin: 5px 0; }
        .label { font-weight: bold; display: inline-block; width: 100px; }
        .value { display: inline-block; }
        .rechazo { background: #ffe6e6; padding: 10px; border: 1px solid #ff9999; margin: 10px 0; }
        .firma-section { margin-top: 30px; border-top: 2px solid #000; padding-top: 20px; }
        .firma-box { border: 1px solid #000; height: 60px; margin: 10px 0; }
        .firma-label { font-weight: bold; margin-top: 10px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('siscon.png') }}" alt="SISCON" class="logo">
            <div class="title">COMPROBANTE DE RECHAZO</div>
            <div class="subtitle">Transacción de Autorización Previa (AP)</div>
        </div>

        <!-- Datos de la transacción -->
        <div class="section">
            <div class="field">
                <span class="label">Fecha:</span>
                <span class="value">{{ $fecha }}</span>
            </div>
            <div class="field">
                <span class="label">ID Transacción:</span>
                <span class="value">{{ $idtran }}</span>
            </div>
        </div>

        <!-- Datos del afiliado -->
        <div class="section">
            <div class="field">
                <span class="label">Afiliado:</span>
                <span class="value">{{ $afiliado }}</span>
            </div>
            <div class="field">
                <span class="label">Nombre:</span>
                <span class="value">{{ $apellido }}, {{ $nombre }}</span>
            </div>
            <div class="field">
                <span class="label">Plan:</span>
                <span class="value">{{ $plan }}</span>
            </div>
        </div>

        <!-- Datos del medicamento -->
        <div class="section">
            <div class="field">
                <span class="label">Medicamento:</span>
                <span class="value">{{ $medicamento }}</span>
            </div>
            <div class="field">
                <span class="label">Código:</span>
                <span class="value">{{ $codigo_prestacion }}</span>
            </div>
            <div class="field">
                <span class="label">Cantidad:</span>
                <span class="value">{{ $cantidad }}</span>
            </div>
        </div>

        <!-- Motivo del rechazo -->
        <div class="rechazo">
            <div style="font-weight: bold; color: #cc0000; margin-bottom: 10px;">
                ❌ AUTORIZACIÓN RECHAZADA
            </div>
            <div class="field">
                <span class="label">Código:</span>
                <span class="value">{{ $codigo_error }}</span>
            </div>
            <div class="field">
                <span class="label">Motivo:</span>
                <span class="value">{{ $motivo_rechazo }}</span>
            </div>
            @if($detalle_rechazo)
            <div class="field">
                <span class="label">Detalle:</span>
                <span class="value">{{ $detalle_rechazo }}</span>
            </div>
            @endif
        </div>

        <!-- Sección de firma -->
        <div class="firma-section">
            <div style="font-weight: bold; margin-bottom: 15px;">
                CONSENTIMIENTO DEL AFILIADO
            </div>
            <div style="font-size: 11px; margin-bottom: 15px;">
                Declaro haber sido informado sobre el rechazo de la autorización solicitada y los motivos del mismo.
            </div>
            
            <div class="firma-label">Firma del Afiliado:</div>
            <div class="firma-box"></div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                <div style="width: 45%;">
                    <div class="firma-label">DNI:</div>
                    <div style="border-bottom: 1px solid #000; height: 25px; margin-top: 5px;"></div>
                </div>
                <div style="width: 45%;">
                    <div class="firma-label">Aclaración:</div>
                    <div style="border-bottom: 1px solid #000; height: 25px; margin-top: 5px;"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            Sistema de Gestión SISCON - {{ now()->format('Y') }}
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; margin: 5px; cursor: pointer; border-radius: 4px;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; margin: 5px; cursor: pointer; border-radius: 4px;">
            ❌ Cerrar
        </button>
    </div>

    <script>
        // Auto-imprimir al cargar
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
