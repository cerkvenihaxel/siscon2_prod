<!DOCTYPE html>
<html>
@php
    $primero = $consumos->first();
    $fechaEntrega = optional($primero->fecha_validacion)->format('d/m/Y') ?: now()->format('d/m/Y');
    $logo = public_path('SISCON.png');
@endphp
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10.5px; line-height: 1.35; color: #222; }
        .page { padding: 4px 14px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header img { max-width: 95px; margin-bottom: 4px; }
        .header h1 { font-size: 15px; margin: 4px 0 1px; color: #333; }
        .header h2 { font-size: 12px; color: #667eea; }
        .header p  { font-size: 10px; color: #666; margin-top: 3px; }
        .section { margin: 8px 0; padding: 7px 10px; border: 1px solid #ddd; border-radius: 6px; }
        .section-title { font-size: 11.5px; font-weight: bold; color: #333; margin-bottom: 5px;
                         padding-bottom: 4px; border-bottom: 2px solid #667eea; }
        table { width: 100%; border-collapse: collapse; margin: 3px 0; }
        table td, table th { padding: 4px 7px; border: 1px solid #ddd; font-size: 10.5px; text-align: left; }
        table.datos td:first-child { font-weight: bold; width: 38%; background: #f5f5f5; }
        table.items th { background: #f0f0f0; }
        .declaracion { text-align: justify; margin: 8px 0; padding: 8px 10px; background: #f9f9f9; border-left: 4px solid #667eea; }
        .firma td { border: none; }
        .firma-line td { border-top: 2px solid #333; text-align: center; padding-top: 4px; font-weight: bold; }
        .footer { margin-top: 12px; padding-top: 8px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #666; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        @if(file_exists($logo))<img src="{{ $logo }}" alt="Logo">@endif
        <h1>CONSTANCIA / CONSENTIMIENTO DE ENTREGA</h1>
        <h2>{{ $obraSocialNombre }} — Global Médica S.A.</h2>
        <p>Remito: {{ $remito }} &nbsp;|&nbsp; Fecha de entrega: {{ $fechaEntrega }}</p>
    </div>

    <div class="section">
        <div class="section-title">DATOS DEL AFILIADO</div>
        <table class="datos">
            <tr><td>Afiliado</td><td>{{ $primero->afiliado }}</td></tr>
            <tr><td>DNI</td><td>{{ $primero->dni }}</td></tr>
            <tr><td>Teléfono</td><td>{{ $primero->telefono }}</td></tr>
            <tr><td>Localidad / Provincia</td><td>{{ $primero->localidad }} / {{ $primero->provincia }}</td></tr>
            <tr><td>Farmacia</td><td>{{ $primero->cliente ?: '—' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">MEDICACIÓN ENTREGADA</div>
        <table class="items">
            <thead>
                <tr><th>Artículo</th><th>Código de barra</th><th style="width:70px;">Cantidad</th></tr>
            </thead>
            <tbody>
                @foreach($consumos as $c)
                    <tr><td>{{ $c->articulo }}</td><td>{{ $c->cod_barra }}</td><td>{{ $c->cantidad }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DATOS DE LA ENTREGA</div>
        <table class="datos">
            <tr><td>Remito N°</td><td>{{ $remito }}</td></tr>
            <tr><td>Fecha de entrega</td><td>{{ $fechaEntrega }}</td></tr>
            <tr><td>N° de pedido (Zafiro)</td><td>{{ $primero->id_pedido_zafiro ?: '—' }}</td></tr>
            <tr><td>Total de ítems</td><td>{{ $consumos->count() }}</td></tr>
        </table>
    </div>

    <div class="declaracion">
        <p><strong>DECLARACIÓN DE RECEPCIÓN Y CONFORMIDAD</strong></p>
        <br>
        <p>
            Por medio de la presente, el afiliado <strong>{{ $primero->afiliado }}</strong>
            (DNI {{ $primero->dni }}) declara haber recibido de conformidad la medicación detallada,
            correspondiente a la obra social <strong>{{ $obraSocialNombre }}</strong> y al remito
            <strong>{{ $remito }}</strong>, en perfecto estado y cumpliendo con lo solicitado.
        </p>
    </div>

    <table class="firma" style="margin-top: 28px;">
        <tr style="height: 32px;"><td></td><td></td></tr>
        <tr class="firma-line">
            <td style="width: 50%;">Firma del afiliado / receptor</td>
            <td style="width: 50%;">Aclaración y DNI</td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>IMPORTANTE:</strong> este documento constituye comprobante oficial de entrega.</p>
        <p>{{ $obraSocialNombre }} — Global Médica S.A. · Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
