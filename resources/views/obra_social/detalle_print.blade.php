<!DOCTYPE html>
<html>
<head>
    <title>Consumo #{{ $consumo->id_consumo }}</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#222; margin: 30px; }
        h2 { margin: 0 0 4px; }
        .sub { color:#666; margin: 0 0 20px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 14px; }
        th { background: #f0f0f0; width: 220px; }
        .foot { margin-top: 30px; font-size: 12px; color:#888; }
        @media print { .noprint { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2>Detalle de consumo — {{ $cfg['titulo'] }}</h2>
    <p class="sub">Comprobante interno · Consumo N° {{ $consumo->id_consumo }} · {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <tr><th>Estado</th><td>{{ $consumo->estado_label }}</td></tr>
        <tr><th>Fecha</th><td>{{ optional($consumo->fecha)->format('d/m/Y') }}</td></tr>
        <tr><th>Afiliado</th><td>{{ $consumo->afiliado }}</td></tr>
        <tr><th>DNI</th><td>{{ $consumo->dni }}</td></tr>
        <tr><th>Teléfono</th><td>{{ $consumo->telefono }}</td></tr>
        <tr><th>Localidad / Provincia</th><td>{{ $consumo->localidad }} / {{ $consumo->provincia }}</td></tr>
        <tr><th>Artículo</th><td>{{ $consumo->articulo }}</td></tr>
        <tr><th>Código de barra</th><td>{{ $consumo->cod_barra }}</td></tr>
        <tr><th>Cantidad</th><td>{{ $consumo->cantidad }}</td></tr>
        <tr><th>N° pedido Zafiro</th><td>{{ $consumo->id_pedido_zafiro ?: '—' }}</td></tr>
        <tr><th>Remito</th><td>{{ $consumo->id_remito ?: '—' }}</td></tr>
        <tr><th>Farmacia</th><td>{{ $consumo->cliente ?: '—' }}</td></tr>
    </table>

    <p class="foot">Global Médica S.A. — Documento generado automáticamente.</p>
    <button class="noprint" onclick="window.print()">Imprimir</button>
</body>
</html>
