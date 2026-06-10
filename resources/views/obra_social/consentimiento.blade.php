<!DOCTYPE html>
<html>
@php
    $primero = $consumos->first();
    $remito  = $primero->id_remito ?: ($primero->id_pedido_zafiro ?: '—');
@endphp
<head>
    <title>Consentimiento de entrega — Remito {{ $remito }}</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#222; margin: 30px; }
        h2 { margin: 0 0 2px; }
        .sub { color:#666; margin: 0 0 18px; font-size: 13px; }
        .datos td { padding: 4px 8px; font-size: 14px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 8px 10px; font-size: 13px; text-align: left; }
        table.items th { background: #f0f0f0; }
        .texto { margin: 20px 0; font-size: 13px; line-height: 1.6; }
        .firma { margin-top: 60px; display: flex; justify-content: space-between; }
        .firma div { width: 45%; border-top: 1px solid #333; text-align: center; padding-top: 6px; font-size: 13px; }
        .foot { margin-top: 30px; font-size: 12px; color:#888; }
        @media print { .noprint { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2>Constancia / Consentimiento de Entrega</h2>
    <p class="sub">{{ $cfg['titulo'] }} · Global Médica S.A. · {{ now()->format('d/m/Y H:i') }}</p>

    <table class="datos">
        <tr><td><strong>Afiliado:</strong></td><td>{{ $primero->afiliado }}</td><td><strong>DNI:</strong></td><td>{{ $primero->dni }}</td></tr>
        <tr><td><strong>Remito:</strong></td><td>{{ $remito }}</td><td><strong>Fecha de entrega:</strong></td><td>{{ optional($primero->fecha_validacion)->format('d/m/Y') ?? now()->format('d/m/Y') }}</td></tr>
        <tr><td><strong>Farmacia:</strong></td><td colspan="3">{{ $primero->cliente ?: '—' }}</td></tr>
    </table>

    <table class="items">
        <thead><tr><th>Artículo</th><th>Código de barra</th><th>Cantidad</th></tr></thead>
        <tbody>
            @foreach($consumos as $c)
                <tr><td>{{ $c->articulo }}</td><td>{{ $c->cod_barra }}</td><td>{{ $c->cantidad }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <p class="texto">
        Por la presente dejo constancia de haber recibido de conformidad la medicación detallada,
        correspondiente al remito indicado. Declaro que los datos son correctos y que la entrega
        fue realizada en tiempo y forma.
    </p>

    <div class="firma">
        <div>Firma del afiliado / receptor</div>
        <div>Aclaración y DNI</div>
    </div>

    <p class="foot">Documento generado automáticamente — Global Médica S.A.</p>
    <button class="noprint" onclick="window.print()">Imprimir</button>
</body>
</html>
