<!DOCTYPE html>
<html>
@php
    use App\Support\ObraSocial\EstadoConsumo;
    $etapaLabels = ['pendientes' => 'PENDIENTES', 'transito' => 'En tránsito', 'entregas' => 'Entregas'];
    $etapa = $cfg['etapa'];
    $base  = $cfg['ruta_base'];
@endphp
<head>
    <title>{{ $cfg['titulo'] }} — {{ $etapaLabels[$etapa] ?? ucfirst($etapa) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('SISCON.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-container { margin-top: 20px; padding: 20px; }
        .step-card { margin-bottom: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .step-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; }
        .step-content { padding: 16px 24px 24px; }
        .pill { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .pill-warning { background: #fff3cd; color: #856404; }
        .pill-info    { background: #cfe8ff; color: #084298; }
        .pill-success { background: #d4edda; color: #155724; }
        .pill-danger  { background: #f8d7da; color: #721c24; }
        .pill-default { background: #e0e0e0; color: #555; }
        table.osplad td, table.osplad th { padding: 9px 12px; }
        table.osplad thead { background: #f0f0f7; }
        .filtros .input-field { margin-top: 0; margin-bottom: 0; }
        .empty-row td { text-align: center; color: #999; padding: 30px; }
        .pagination li.active { background: #667eea; }
        .resumen-card { border-radius: 10px; padding: 16px; color: #fff; text-align: center; }
        .resumen-num { font-size: 30px; font-weight: 700; line-height: 1; }
        .resumen-lbl { font-size: 13px; opacity: .9; }
        .sticky-bar { position: sticky; top: 60px; z-index: 5; background: #fffae6; border: 1px solid #ffe08a;
                      padding: 10px 16px; border-radius: 8px; margin-bottom: 12px; display: none; }
    </style>
</head>
<body>
@include('components.up_topbar')
@include('components.osplad_sidebar')

<div class="container main-container" style="width: 95%; max-width: 1400px;">

    <div class="card step-card">
        <div class="step-header">
            <div class="row valign-wrapper" style="margin-bottom: 0;">
                <div class="col s8">
                    <h5 class="white-text" style="margin: 0;">
                        <i class="material-icons left">medical_services</i>
                        {{ $cfg['titulo'] }} — {{ $etapaLabels[$etapa] ?? ucfirst($etapa) }}
                    </h5>
                    <p class="white-text" style="opacity: .85; margin: 6px 0 0;">Gestión de consumos de obra social</p>
                </div>
                <div class="col s4 right-align">
                    <span class="white-text" style="font-size: 13px;">Total en esta etapa</span><br>
                    <span class="white-text" style="font-size: 28px; font-weight: 700;">{{ $consumos->total() }}</span>
                </div>
            </div>
        </div>

        <div class="step-content">

            {{-- ENTREGAS: resumen para facturación + export (solo admin) --}}
            @if($etapa === 'entregas' && isset($resumen))
            <div class="row" style="margin-bottom: 6px;">
                <div class="col s12 m4"><div class="resumen-card" style="background:#5c6bc0;"><div class="resumen-num">{{ $resumen['pedidos'] }}</div><div class="resumen-lbl">Pedidos entregados</div></div></div>
                <div class="col s12 m4"><div class="resumen-card" style="background:#26a69a;"><div class="resumen-num">{{ $resumen['remitos'] }}</div><div class="resumen-lbl">Remitos entregados</div></div></div>
                <div class="col s12 m4"><div class="resumen-card" style="background:#7e57c2;"><div class="resumen-num">{{ $resumen['afiliados'] }}</div><div class="resumen-lbl">Afiliados</div></div></div>
            </div>
            @if($cfg['ve_todo'])
            <div style="margin: 6px 0 14px;">
                <span class="grey-text">Exportar a Excel para facturación:</span>
                <a class="btn-small green" href="{{ $base }}/export/semana"><i class="material-icons left">file_download</i>Semana</a>
                <a class="btn-small green darken-2" href="{{ $base }}/export/mes"><i class="material-icons left">file_download</i>Mes</a>
                <a class="btn-small teal" href="{{ $base }}/export/rango?{{ http_build_query(['fecha_desde' => request('fecha_desde'), 'fecha_hasta' => request('fecha_hasta')]) }}"><i class="material-icons left">file_download</i>Rango filtrado</a>
            </div>
            @endif
            @endif

            {{-- Selector de Obra Social y Farmacias (solo admin) --}}
            @if($cfg['ve_todo'])
            <form method="GET" class="row" style="margin-bottom: 0; background:#f4f4fb; border-radius:8px; padding:10px 12px 0;">
                <div class="input-field col s12 m4">
                    <select name="os_id" onchange="this.form.submit()">
                        @foreach($cfg['obras_sociales'] as $os)
                            <option value="{{ $os->id }}" {{ $cfg['os_activa_id'] == $os->id ? 'selected' : '' }}>{{ $os->nombre }}</option>
                        @endforeach
                    </select>
                    <label>Obra social</label>
                </div>
                <div class="input-field col s12 m6">
                    <select name="farmacias[]" multiple>
                        @foreach($cfg['farmacias'] as $f)
                            <option value="{{ $f->id_cliente }}" {{ in_array((int)$f->id_cliente, $cfg['farmacias_sel'], true) ? 'selected' : '' }}>
                                {{ $f->cliente ?: ('Farmacia ' . $f->id_cliente) }}
                            </option>
                        @endforeach
                    </select>
                    <label>Farmacias a visualizar (vacío = todas)</label>
                </div>
                <div class="input-field col s12 m2">
                    <button type="submit" class="btn waves-effect" style="background:#7e57c2; width:100%;"><i class="material-icons left">filter_list</i>Aplicar</button>
                </div>
            </form>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="row filtros" style="margin-bottom: 4px;">
                <div class="input-field col s12 m3">
                    <input id="afiliado" type="text" name="afiliado" value="{{ request('afiliado') }}">
                    <label for="afiliado" class="{{ request('afiliado') ? 'active' : '' }}">Afiliado</label>
                </div>
                <div class="input-field col s6 m2">
                    <input id="dni" type="text" name="dni" value="{{ request('dni') }}">
                    <label for="dni" class="{{ request('dni') ? 'active' : '' }}">DNI</label>
                </div>
                @if($etapa !== 'pendientes')
                <div class="input-field col s6 m2">
                    <input id="remito" type="text" name="remito" value="{{ request('remito') }}">
                    <label for="remito" class="{{ request('remito') ? 'active' : '' }}">Remito</label>
                </div>
                @endif
                <div class="input-field col s6 m2">
                    <input id="fecha_desde" type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    <label for="fecha_desde" class="active">Desde</label>
                </div>
                <div class="input-field col s6 m2">
                    <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    <label for="fecha_hasta" class="active">Hasta</label>
                </div>
                <div class="col s12" style="margin-bottom: 10px;">
                    <button type="submit" class="btn waves-effect" style="background:#667eea;"><i class="material-icons left">search</i>Buscar</button>
                    <a href="{{ $base }}/{{ $etapa }}" class="btn-flat waves-effect"><i class="material-icons left">clear</i>Limpiar</a>
                </div>
            </form>

            {{-- En tránsito: barra de entrega unificada --}}
            @if($etapa === 'transito')
            <div id="bar-entrega" class="sticky-bar">
                <span id="bar-info"></span>
                <button class="btn green right" id="btn-entregar"><i class="material-icons left">check</i>Entregar seleccionados</button>
            </div>
            @endif

            <table class="osplad striped highlight responsive-table">
                <thead>
                    <tr>
                        @if($etapa === 'transito')<th style="width:36px;"><label><input type="checkbox" id="check-all" class="filled-in" /><span></span></label></th>@endif
                        <th>Fecha</th>
                        <th>Afiliado</th>
                        <th>DNI</th>
                        <th>Artículo</th>
                        <th>Cant</th>
                        @if($cfg['ve_todo'])<th>Farmacia</th>@endif
                        <th>Remito</th>
                        <th>Estado</th>
                        <th class="right-align">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consumos as $c)
                        <tr>
                            @if($etapa === 'transito')
                            <td>
                                <label>
                                    <input type="checkbox" class="filled-in chk-row" value="{{ $c->id_consumo }}"
                                           data-dni="{{ $c->dni }}" data-remito="{{ $c->id_remito }}" data-afiliado="{{ $c->afiliado }}" />
                                    <span></span>
                                </label>
                            </td>
                            @endif
                            <td>{{ optional($etapa === 'entregas' ? $c->fecha_validacion : $c->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $c->afiliado }}</td>
                            <td>{{ $c->dni }}</td>
                            <td>{{ $c->articulo }}</td>
                            <td>{{ $c->cantidad }}</td>
                            @if($cfg['ve_todo'])<td>{{ $c->cliente ?: '—' }}</td>@endif
                            <td>{{ $c->id_remito ?: '—' }}</td>
                            <td><span class="pill pill-{{ EstadoConsumo::color($c->estado_codigo) }}">{{ $c->estado_label }}</span></td>
                            <td class="right-align" style="white-space: nowrap;">
                                <a class="btn-small blue lighten-1" href="{{ $base }}/{{ $c->id_consumo }}/detalle" title="Ver detalle">
                                    <i class="material-icons">visibility</i>
                                </a>
                                <a class="btn-small grey darken-1" href="{{ $base }}/{{ $c->id_consumo }}/detalle?print=1" target="_blank" title="Imprimir detalle">
                                    <i class="material-icons">print</i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="11">Sin registros en esta etapa.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($consumos->hasPages())
            <ul class="pagination center-align" style="margin-top: 20px;">
                <li class="{{ $consumos->onFirstPage() ? 'disabled' : 'waves-effect' }}"><a href="{{ $consumos->previousPageUrl() ?? '#' }}"><i class="material-icons">chevron_left</i></a></li>
                @php $start = max(1, $consumos->currentPage() - 3); $end = min($consumos->lastPage(), $consumos->currentPage() + 3); @endphp
                @if($start > 1)<li class="waves-effect"><a href="{{ $consumos->url(1) }}">1</a></li>@if($start > 2)<li class="disabled"><a href="#">…</a></li>@endif @endif
                @for($p = $start; $p <= $end; $p++)
                    <li class="{{ $p == $consumos->currentPage() ? 'active' : 'waves-effect' }}"><a href="{{ $consumos->url($p) }}" class="{{ $p == $consumos->currentPage() ? 'white-text' : '' }}">{{ $p }}</a></li>
                @endfor
                @if($end < $consumos->lastPage())@if($end < $consumos->lastPage() - 1)<li class="disabled"><a href="#">…</a></li>@endif<li class="waves-effect"><a href="{{ $consumos->url($consumos->lastPage()) }}">{{ $consumos->lastPage() }}</a></li>@endif
                <li class="{{ $consumos->hasMorePages() ? 'waves-effect' : 'disabled' }}"><a href="{{ $consumos->nextPageUrl() ?? '#' }}"><i class="material-icons">chevron_right</i></a></li>
            </ul>
            <p class="center-align grey-text">Mostrando {{ $consumos->firstItem() }}–{{ $consumos->lastItem() }} de {{ $consumos->total() }}</p>
            @endif
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const base  = @json($base);
    const token = $('meta[name="csrf-token"]').attr('content');

    // Inicializar selects de Materialize (selector OS / farmacias del admin)
    if (window.M && M.FormSelect) { M.FormSelect.init(document.querySelectorAll('select')); }

    @if($etapa === 'transito')
    function selected() { return $('.chk-row:checked'); }

    function refreshBar() {
        const sel = selected();
        const bar = $('#bar-entrega');
        if (sel.length === 0) { bar.hide(); return; }
        // Validar entrega unificada: mismo DNI y mismo remito
        const dnis = new Set(sel.map((i, el) => $(el).data('dni')).get());
        const rems = new Set(sel.map((i, el) => String($(el).data('remito') || '')).get());
        let info = sel.length + ' seleccionado(s).';
        let okUnif = (dnis.size === 1 && rems.size === 1);
        if (!okUnif) info += ' ⚠ Deben ser del mismo afiliado y mismo remito.';
        else info += ' Afiliado y remito coinciden.';
        $('#bar-info').text(info);
        $('#btn-entregar').prop('disabled', !okUnif);
        bar.show();
    }

    $('#check-all').on('change', function () { $('.chk-row').prop('checked', this.checked); refreshBar(); });
    $('.chk-row').on('change', refreshBar);

    $('#btn-entregar').on('click', function () {
        const ids = selected().map((i, el) => $(el).val()).get();
        if (ids.length === 0) return;
        if (!confirm('¿Entregar ' + ids.length + ' pedido(s) y notificar al afiliado?')) return;
        fetch(base + '/entregar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ ids: ids })
        }).then(async r => ({ ok: r.ok, data: await r.json().catch(() => ({})) }))
          .then(res => {
              if (res.ok && res.data.success) {
                  M.toast({ html: res.data.message, classes: 'green' });
                  if (res.data.redirect) { window.open(res.data.redirect, '_blank'); }
                  setTimeout(() => location.reload(), 1400);
              } else {
                  M.toast({ html: (res.data && res.data.message) || 'No se pudo entregar', classes: 'red' });
              }
          });
    });
    @endif
});
</script>
</body>
</html>
