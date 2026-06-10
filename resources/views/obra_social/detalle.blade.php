<!DOCTYPE html>
<html>
<head>
    <title>Detalle consumo #{{ $consumo->id_consumo }}</title>
    <link rel="icon" type="image/png" href="{{ asset('SISCON.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-container { margin-top: 20px; padding: 20px; max-width: 900px; }
        .step-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; padding: 20px; border-radius: 12px 12px 0 0; }
        .det-table th { width: 220px; color:#667eea; text-align:left; }
    </style>
</head>
<body>
@include('components.up_topbar')
@include('components.osplad_sidebar')

<div class="container main-container">
    <div class="card" style="border-radius:12px;">
        <div class="step-header">
            <h5 class="white-text" style="margin:0;"><i class="material-icons left">description</i>Detalle del consumo</h5>
        </div>
        <div style="padding: 20px;">
            @php use App\Support\ObraSocial\EstadoConsumo; @endphp
            <table class="det-table striped">
                <tr><th>Estado</th><td><span style="padding:4px 12px;border-radius:20px;font-weight:600;background:#eee;">{{ $consumo->estado_label }}</span></td></tr>
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

            <div style="margin-top: 20px;">
                <a href="{{ $cfg['ruta_base'] }}/{{ $consumo->id_consumo }}/detalle?print=1" target="_blank" class="btn" style="background:#667eea;">
                    <i class="material-icons left">print</i>Imprimir
                </a>
                <a href="javascript:history.back()" class="btn-flat"><i class="material-icons left">arrow_back</i>Volver</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
