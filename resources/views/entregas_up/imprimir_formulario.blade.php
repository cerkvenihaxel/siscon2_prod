<!DOCTYPE html>
<html>
<head>
    <title>Formulario de Entrega - Consumo #{{ $consumo->id }}</title>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 22px;
            margin: 10px 0;
            color: #333;
        }
        .header h2 {
            font-size: 16px;
            color: #666;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            width: 40%;
            background: #f5f5f5;
        }
        .form-field {
            margin: 15px 0;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            min-height: 40px;
        }
        .form-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }
        .form-field .line {
            border-bottom: 1px solid #333;
            min-height: 30px;
        }
        .firma-box {
            border: 2px solid #333;
            padding: 15px;
            margin: 20px 0;
            min-height: 150px;
            position: relative;
        }
        .firma-box label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }
        .firma-line {
            border-top: 2px solid #333;
            margin-top: 100px;
            padding-top: 10px;
            text-align: center;
        }
        .declaracion {
            text-align: justify;
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            line-height: 1.8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .checkbox {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            margin-right: 10px;
            vertical-align: middle;
        }
        @media print {
            body {
                background: white;
            }
            .page {
                margin: 0;
                border: none;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- PÁGINA 1: FORMULARIO DE ENTREGA -->
<div class="page">
    <div class="header">
        <img src="{{ asset('SISCON.png') }}" alt="SISCON Logo">
        <h1>FORMULARIO DE ENTREGA DE PRESTACIÓN</h1>
        <h2>Unión Personal - Sistema SISCON</h2>
        <p>Consumo ID: #{{ $consumo->id }} | Fecha: {{ now()->format('d/m/Y') }}</p>
    </div>

    <!-- Datos del Consumo -->
    <div class="section">
        <div class="section-title">DATOS DEL CONSUMO</div>
        <table>
            <tr>
                <td>Código de Afiliado</td>
                <td>{{ $consumo->afiliado }}</td>
            </tr>
            <tr>
                <td>Nombre Completo</td>
                <td>{{ trim($consumo->nombres . ' ' . $consumo->apellidos) }}</td>
            </tr>
            <tr>
                <td>Plan</td>
                <td>{{ $consumo->nombre_modelo_plan ?? 'N/A' }} ({{ $consumo->modelo_plan }})</td>
            </tr>
            <tr>
                <td>Código de Prestación</td>
                <td>{{ $consumo->cod_prestacion }}</td>
            </tr>
            <tr>
                <td>Descripción</td>
                <td>{{ $consumo->desc }}</td>
            </tr>
            <tr>
                <td>Cantidad Solicitada</td>
                <td><strong>{{ $consumo->cant }}</strong></td>
            </tr>
            <tr>
                <td>ID Transacción (IDTRAN)</td>
                <td>{{ $consumo->num_tran ?? $consumo->idtran_aprobacion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>ID Autorización (IDAUT)</td>
                <td>{{ $consumo->idaut ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Información Económica -->
    <div class="section">
        <div class="section-title">INFORMACIÓN ECONÓMICA</div>
        <table>
            <tr>
                <td>Cargo</td>
                <td>${{ number_format($consumo->cargo ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Importe Obra Social</td>
                <td>${{ number_format($consumo->impos ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Importe Total</td>
                <td>${{ number_format($consumo->imptot ?? 0, 2) }}</td>
            </tr>
            <tr style="background: #f0f0f0;">
                <td><strong>TOTAL A PAGAR</strong></td>
                <td><strong style="font-size: 14px;">${{ number_format(($consumo->imptot ?? 0) + ($consumo->cargo ?? 0), 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Datos de la Entrega -->
    <div class="section">
        <div class="section-title">DATOS DE LA ENTREGA</div>

        <div class="form-field">
            <label>Fecha de Entrega:</label>
            <div class="line">{{ now()->format('d/m/Y') }}</div>
        </div>

        <div class="form-field">
            <label>Cantidad Entregada:</label>
            <div class="line"></div>
        </div>

        <div class="form-field">
            <label>Estado de Entrega:</label>
            <div>
                <span class="checkbox"></span> Entrega Completa &nbsp;&nbsp;&nbsp;
                <span class="checkbox"></span> Entrega Parcial &nbsp;&nbsp;&nbsp;
                <span class="checkbox"></span> Rechazada
            </div>
        </div>

        <div class="form-field">
            <label>Quien Recibe:</label>
            <div class="line"></div>
        </div>

        <div class="form-field">
            <label>DNI del que Recibe:</label>
            <div class="line"></div>
        </div>

        <div class="form-field">
            <label>Relación con Afiliado:</label>
            <div>
                <span class="checkbox"></span> Afiliado &nbsp;&nbsp;&nbsp;
                <span class="checkbox"></span> Familiar &nbsp;&nbsp;&nbsp;
                <span class="checkbox"></span> Tercero Autorizado
            </div>
        </div>

        @if($consumo->remito)
        <div class="form-field">
            <label>Número de Remito:</label>
            <div class="line">{{ $consumo->remito }}</div>
        </div>
        @else
        <div class="form-field">
            <label>Número de Remito:</label>
            <div class="line"></div>
        </div>
        @endif

        @if($consumo->nro_transporte)
        <div class="form-field">
            <label>Transporte:</label>
            <div class="line">{{ $consumo->nro_transporte }}</div>
        </div>
        @else
        <div class="form-field">
            <label>Transporte:</label>
            <div class="line"></div>
        </div>
        @endif

        <div class="form-field">
            <label>Observaciones:</label>
            <div class="line" style="min-height: 60px;"></div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema SISCON - Unión Personal</p>
    </div>
</div>

<!-- PÁGINA 2: CONSENTIMIENTO -->
<div class="page">
    <div class="header">
        <img src="{{ asset('SISCON.png') }}" alt="SISCON Logo">
        <h1>CONSENTIMIENTO DE ENTREGA</h1>
        <h2>Consumo ID: #{{ $consumo->id }}</h2>
    </div>

    <div class="declaracion">
        <p><strong>DECLARACIÓN DE RECEPCIÓN</strong></p>
        <br>
        <p>
            Por medio de la presente, yo _________________________________, con DNI N° _________________,
            declaro haber recibido en perfectas condiciones la prestación médica/medicamento detallada en este documento,
            correspondiente al afiliado <strong>{{ trim($consumo->nombres . ' ' . $consumo->apellidos) }}</strong> (Código: {{ $consumo->afiliado }}).
        </p>
        <br>
        <p>
            <strong>Descripción de lo recibido:</strong><br>
            {{ $consumo->desc }}
        </p>
        <p>
            <strong>Código de Prestación:</strong> {{ $consumo->cod_prestacion }}
        </p>
        <p>
            <strong>Cantidad recibida:</strong> _________ unidad(es) de {{ $consumo->cant }} solicitadas
        </p>
        <p>
            <strong>Fecha de entrega:</strong> {{ now()->format('d/m/Y') }}
        </p>
        @if($consumo->remito)
        <p>
            <strong>Remito N°:</strong> {{ $consumo->remito }}
        </p>
        @endif
        <p>
            <strong>ID Transacción:</strong> {{ $consumo->num_tran ?? $consumo->idtran_aprobacion ?? 'N/A' }}
        </p>
        <p>
            <strong>ID Autorización:</strong> {{ $consumo->idaut ?? 'N/A' }}
        </p>
    </div>

    <div class="declaracion">
        <p><strong>DECLARACIÓN DE CONFORMIDAD</strong></p>
        <br>
        <p>
            Manifiesto que he verificado la prestación recibida y que la misma se encuentra en perfecto estado,
            cumpliendo con las especificaciones solicitadas. Asimismo, declaro haber sido informado sobre el uso
            correcto y las indicaciones médicas correspondientes.
        </p>
        <br>
        <p>
            Acepto y reconozco la veracidad de los datos consignados en este documento y autorizo el registro
            de esta entrega en el sistema SISCON de Unión Personal.
        </p>
    </div>

    <div class="firma-box">
        <label>FIRMA DEL RECEPTOR:</label>
        <div style="height: 80px;"></div>
    </div>

    <table style="margin-top: 30px; border: none;">
        <tr style="border: none;">
            <td style="border: none; border-bottom: 2px solid #333; width: 50%;">
                <br><br>
            </td>
            <td style="border: none; width: 10%;"></td>
            <td style="border: none; border-bottom: 2px solid #333; width: 40%;">
                <br><br>
            </td>
        </tr>
        <tr style="border: none;">
            <td style="border: none; text-align: center; padding-top: 5px;">
                <strong>Aclaración</strong>
            </td>
            <td style="border: none;"></td>
            <td style="border: none; text-align: center; padding-top: 5px;">
                <strong>DNI N°</strong>
            </td>
        </tr>
    </table>

    <div class="footer" style="margin-top: 60px;">
        <p><strong>IMPORTANTE:</strong> Este documento constituye comprobante oficial de entrega.</p>
        <p>Para consultas o reclamos, comunicarse con Unión Personal al 0800-XXX-XXXX</p>
        <br>
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema SISCON - Consumo ID: {{ $consumo->id }}</p>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px; page-break-before: avoid;">
    <button onclick="window.print()" style="padding: 15px 30px; font-size: 16px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">
        🖨️ Imprimir Formulario
    </button>
    <button onclick="window.close()" style="padding: 15px 30px; font-size: 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">
        ✖️ Cerrar
    </button>
</div>

</body>
</html>
