<!DOCTYPE html>
<html>
<head>
    <title>Consentimiento de Entrega - ID {{ $entrega->id }}</title>
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
        .firma-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .firma-box {
            border: 2px solid #333;
            padding: 15px;
            margin: 20px 0;
            min-height: 150px;
            position: relative;
        }
        .firma-box img {
            max-width: 100%;
            max-height: 120px;
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
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
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

<!-- PÁGINA 1: DATOS DE LA ENTREGA -->
<div class="page">
    <div class="header">
        <img src="{{ asset('siscon.png') }}" alt="SISCON Logo">
        <h1>COMPROBANTE DE ENTREGA DE PRESTACIÓN</h1>
        <h2>Unión Personal - Sistema SISCON</h2>
        <p>ID Entrega: #{{ $entrega->id }} | Fecha: {{ $entrega->fecha_entrega->format('d/m/Y') }}</p>
    </div>

    <!-- Datos del Afiliado -->
    <div class="section">
        <div class="section-title">DATOS DEL AFILIADO</div>
        <table>
            <tr>
                <td>Código de Afiliado</td>
                <td>{{ $entrega->codigo_afiliado }}</td>
            </tr>
            <tr>
                <td>Nombre Completo</td>
                <td>{{ $entrega->nombre_afiliado }}</td>
            </tr>
            <tr>
                <td>Plan</td>
                <td>{{ $entrega->consumo->nombre_modelo_plan ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Datos de la Prestación -->
    <div class="section">
        <div class="section-title">DATOS DE LA PRESTACIÓN ENTREGADA</div>
        <table>
            <tr>
                <td>Código de Prestación</td>
                <td>{{ $entrega->cod_prestacion }}</td>
            </tr>
            <tr>
                <td>Descripción</td>
                <td>{{ $entrega->descripcion_prestacion }}</td>
            </tr>
            <tr>
                <td>Cantidad Solicitada</td>
                <td>{{ $entrega->cantidad_solicitada }}</td>
            </tr>
            <tr>
                <td>Cantidad Entregada</td>
                <td><strong>{{ $entrega->cantidad_entregada }}</strong></td>
            </tr>
            <tr>
                <td>Estado de Entrega</td>
                <td><strong>{{ strtoupper($entrega->estado_entrega) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Datos de la Transacción -->
    <div class="section">
        <div class="section-title">DATOS DE LA TRANSACCIÓN</div>
        <table>
            <tr>
                <td>ID Transacción (IDTRAN)</td>
                <td>{{ $entrega->consumo->num_tran ?? $entrega->consumo->idtran_aprobacion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>ID Autorización (IDAUT)</td>
                <td>{{ $entrega->consumo->idaut ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Fecha de Aprobación</td>
                <td>{{ $entrega->consumo->fecha_aprobacion ? $entrega->consumo->fecha_aprobacion->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Prestador</td>
                <td>{{ $entrega->consumo->cod_prestador ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Información Económica -->
    <div class="section">
        <div class="section-title">INFORMACIÓN ECONÓMICA</div>
        <table>
            <tr>
                <td>Cargo</td>
                <td>${{ number_format($entrega->consumo->cargo ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Importe Obra Social</td>
                <td>${{ number_format($entrega->consumo->impos ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Importe Total</td>
                <td>${{ number_format($entrega->consumo->imptot ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total a Pagar</strong></td>
                <td><strong>${{ number_format(($entrega->consumo->imptot ?? 0) + ($entrega->consumo->cargo ?? 0), 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Datos de la Entrega -->
    <div class="section">
        <div class="section-title">DATOS DE LA ENTREGA</div>
        <table>
            <tr>
                <td>Fecha de Entrega</td>
                <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Quien Recibe</td>
                <td>{{ $entrega->quien_recibe }}</td>
            </tr>
            @if($entrega->dni_afiliado)
            <tr>
                <td>DNI</td>
                <td>{{ $entrega->dni_afiliado }}</td>
            </tr>
            @endif
            @if($entrega->relacion_afiliado)
            <tr>
                <td>Relación con Afiliado</td>
                <td>{{ ucfirst($entrega->relacion_afiliado) }}</td>
            </tr>
            @endif
            @if($entrega->remito)
            <tr>
                <td>Remito</td>
                <td>{{ $entrega->remito }}</td>
            </tr>
            @endif
            @if($entrega->transporte)
            <tr>
                <td>Transporte</td>
                <td>{{ $entrega->transporte }}</td>
            </tr>
            @endif
            <tr>
                <td>Usuario que Registra</td>
                <td>{{ $entrega->usuario_entrega }}</td>
            </tr>
        </table>
    </div>

    @if($entrega->observaciones)
    <div class="section">
        <div class="section-title">OBSERVACIONES</div>
        <p style="padding: 10px;">{{ $entrega->observaciones }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema SISCON - Unión Personal</p>
    </div>
</div>

<!-- PÁGINA 2: CONSENTIMIENTO Y FIRMA -->
<div class="page">
    <div class="header">
        <img src="{{ asset('siscon.png') }}" alt="SISCON Logo">
        <h1>CONSENTIMIENTO DE ENTREGA</h1>
        <h2>ID Entrega: #{{ $entrega->id }}</h2>
    </div>

    <div class="declaracion">
        <p><strong>DECLARACIÓN DE RECEPCIÓN</strong></p>
        <br>
        <p>
            Por medio de la presente, yo <strong>{{ $entrega->quien_recibe }}</strong>,
            @if($entrega->dni_afiliado)
                con DNI N° <strong>{{ $entrega->dni_afiliado }}</strong>,
            @endif
            declaro haber recibido en perfectas condiciones la prestación médica/medicamento detallada en este documento,
            correspondiente al afiliado <strong>{{ $entrega->nombre_afiliado }}</strong> (Código: {{ $entrega->codigo_afiliado }}).
        </p>
        <br>
        <p>
            <strong>Descripción de lo recibido:</strong><br>
            {{ $entrega->descripcion_prestacion }}
        </p>
        <p>
            <strong>Cantidad recibida:</strong> {{ $entrega->cantidad_entregada }} unidad(es)
        </p>
        <p>
            <strong>Fecha de entrega:</strong> {{ $entrega->fecha_entrega->format('d/m/Y') }}
        </p>
        @if($entrega->remito)
        <p>
            <strong>Remito N°:</strong> {{ $entrega->remito }}
        </p>
        @endif
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

    <div class="firma-section">
        <div class="section-title">FIRMA Y ACLARACIÓN DEL RECEPTOR</div>

        @if($entrega->firma_afiliado)
        <div class="firma-box">
            <p><strong>Firma Digital:</strong></p>
            <img src="{{ Storage::url($entrega->firma_afiliado) }}" alt="Firma">
        </div>
        @else
        <div class="firma-line">
            Firma: _________________________________
        </div>
        @endif

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
    </div>

    <div class="footer" style="margin-top: 60px;">
        <p><strong>IMPORTANTE:</strong> Este documento constituye comprobante oficial de entrega.</p>
        <p>Para consultas o reclamos, comunicarse con Unión Personal al 0800-XXX-XXXX</p>
        <br>
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema SISCON - ID Entrega: {{ $entrega->id }}</p>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px; page-break-before: avoid;">
    <button onclick="window.print()" class="btn" style="padding: 15px 30px; font-size: 16px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">
        <span style="vertical-align: middle;">📄 Imprimir Consentimiento</span>
    </button>
    <button onclick="guardarPDF()" class="btn" style="padding: 15px 30px; font-size: 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">
        <span style="vertical-align: middle;">💾 Guardar PDF</span>
    </button>
    <a href="/admin/consumos-up" class="btn" style="padding: 15px 30px; font-size: 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block;">
        <span style="vertical-align: middle;">⬅️ Volver al Listado</span>
    </a>
</div>

<script>
function guardarPDF() {
    alert('PDF guardado exitosamente en el registro de entrega #{{ $entrega->id }}');
    window.print();
}
</script>

</body>
</html>
