<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pedidos WhatsApp — SISCON</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;background:#f0f4f8;color:#212121;min-height:100vh}

/* Top bar */
.top-bar{background:linear-gradient(135deg,#25d366,#128c7e);padding:0 24px;display:flex;align-items:center;justify-content:space-between;height:60px;box-shadow:0 2px 10px rgba(18,140,126,.35);position:sticky;top:0;z-index:100}
.logo{font-size:20px;font-weight:900;color:#fff;display:flex;align-items:center;gap:10px}
.logo small{font-size:11px;font-weight:400;opacity:.8;display:block}
.top-actions{display:flex;gap:8px;align-items:center}
.top-btn{background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:.15s}
.top-btn:hover{background:rgba(255,255,255,.25)}

.container{max-width:1100px;margin:0 auto;padding:20px 14px}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:16px}
.stat-card{background:#fff;border-radius:12px;padding:14px 16px;box-shadow:0 1px 5px rgba(0,0,0,.08);border-left:4px solid #e0e0e0;display:flex;flex-direction:column;gap:3px}
.stat-card.s-green{border-left-color:#25d366}
.stat-card.s-blue{border-left-color:#1565c0}
.stat-card.s-orange{border-left-color:#f57c00}
.stat-card.s-teal{border-left-color:#00897b}
.stat-card.s-red{border-left-color:#c62828}
.stat-card.s-gray{border-left-color:#78909c}
.stat-icon{font-size:18px}
.stat-num{font-size:26px;font-weight:900;color:#212121;line-height:1}
.stat-label{font-size:10px;color:#999;text-transform:uppercase;letter-spacing:.4px}

/* Panels */
.panels-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.panel{background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 5px rgba(0,0,0,.08)}
.panel-title{font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.panel-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f5f5f5;font-size:13px}
.panel-row:last-child{border-bottom:none}
.panel-label{color:#555;display:flex;align-items:center;gap:6px}
.panel-val{font-weight:700;color:#333}
.panel-val.money{color:#2e7d32}

/* Bar chart */
.bar-row{display:flex;align-items:center;gap:8px;padding:4px 0}
.bar-label{font-size:12px;color:#555;min-width:80px;text-align:right}
.bar-wrap{flex:1;height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden}
.bar-fill{height:100%;border-radius:4px;transition:width .3s}
.bar-val{font-size:11px;color:#888;min-width:30px}

/* Search */
.search-box{background:#fff;border-radius:14px;padding:14px 18px;margin-bottom:12px;box-shadow:0 1px 5px rgba(0,0,0,.08)}
.filter-row{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.inp-wrap{flex:2;min-width:180px;position:relative}
.inp-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.35;pointer-events:none}
.s-input{width:100%;padding:9px 10px 9px 33px;border:2px solid #e0e0e0;border-radius:9px;font-size:13px;outline:none;color:#333}
.s-input:focus{border-color:#25d366}
.s-select{padding:9px 10px;border:2px solid #e0e0e0;border-radius:9px;font-size:13px;outline:none;color:#333;min-width:130px}
.s-select:focus{border-color:#25d366}
.btn-search{background:#25d366;color:#fff;border:none;border-radius:9px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer}
.btn-search:hover{background:#1da851}
.btn-clear{background:#fff;color:#666;border:2px solid #e0e0e0;border-radius:9px;padding:8px 14px;font-size:13px;cursor:pointer;text-decoration:none}

/* Results */
.results-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:6px;padding:0 2px}
.results-info{font-size:12px;color:#888}
.results-info strong{color:#333}
.filter-tags{display:flex;gap:6px;flex-wrap:wrap}
.ftag{background:#e8f5e9;color:#1b5e20;border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:4px}
.ftag a{color:#1b5e20;text-decoration:none;font-weight:700}

/* Table */
.table-wrap{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 1px 5px rgba(0,0,0,.08);margin-bottom:12px}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:#f8fafb;border-bottom:2px solid #eef2f7}
th{padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
tbody tr{border-bottom:1px solid #f5f7fa;transition:.12s;cursor:pointer}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:#f0fdf4}
td{padding:10px 14px;vertical-align:middle}

.td-id{font-family:'SF Mono',Consolas,monospace;font-size:11px;color:#0077b6;font-weight:700}
.td-num{font-size:12px;color:#555;font-family:'SF Mono',Consolas,monospace}
.td-cliente b{display:block;font-size:13px;color:#333}
.td-cliente span{font-size:11px;color:#aaa}
.td-meds{max-width:200px}
.med-pill{display:inline-block;background:#f0f4f8;border:1px solid #dde3ea;border-radius:4px;padding:1px 7px;font-size:10px;color:#444;margin:1px}
.med-more{font-size:10px;color:#888}
.td-total{font-weight:700;color:#2e7d32;white-space:nowrap}
.td-fecha{font-size:11px;color:#888;white-space:nowrap}
.td-envio{font-size:12px;white-space:nowrap}
.td-pago{font-size:12px;white-space:nowrap}

.estado-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;white-space:nowrap}
.estado-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}

.btn-estado{background:transparent;border:1.5px solid #e0e0e0;border-radius:7px;padding:4px 10px;font-size:11px;cursor:pointer;color:#555;transition:.12s}
.btn-estado:hover{border-color:#25d366;color:#25d366}

.empty{text-align:center;padding:50px 20px;color:#ccc}
.empty-icon{font-size:44px;margin-bottom:10px}

/* Pagination */
.pagination{display:flex;align-items:center;justify-content:center;gap:6px;padding:16px 0 4px;flex-wrap:wrap}
.pg-btn{background:#fff;border:2px solid #25d366;color:#25d366;border-radius:8px;padding:6px 13px;font-size:12px;font-weight:600;text-decoration:none;transition:.12s}
.pg-btn:hover{background:#25d366;color:#fff}
.pg-dis{background:#f5f5f5;border-color:#e0e0e0;color:#ccc;pointer-events:none}
.pg-nums{display:flex;gap:3px}
.pg-num{background:#fff;border:1.5px solid #e0e0e0;color:#555;border-radius:7px;padding:5px 10px;font-size:12px;text-decoration:none;min-width:32px;text-align:center;display:inline-block;transition:.12s}
.pg-num:hover{border-color:#25d366;color:#25d366}
.pg-cur{background:#25d366!important;border-color:#25d366!important;color:#fff!important;font-weight:700}
.pg-ell{color:#aaa;padding:5px 3px;font-size:12px}

/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;align-items:center;justify-content:center;padding:20px}
.modal-bg.open{display:flex}
.modal{background:#fff;border-radius:16px;max-width:580px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 12px 48px rgba(0,0,0,.2)}
.modal-header{padding:18px 20px 14px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:flex-start;position:sticky;top:0;background:#fff;z-index:1}
.modal-title{font-size:17px;font-weight:800;color:#128c7e}
.modal-sub{font-size:11px;color:#aaa;margin-top:2px}
.modal-close{background:none;border:none;font-size:20px;cursor:pointer;color:#aaa;line-height:1;padding:2px 6px}
.modal-close:hover{color:#333}
.modal-body{padding:18px 20px}
.m-section{margin-bottom:16px}
.m-section-title{font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.m-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.m-field{background:#f7f9fb;border-radius:8px;padding:9px 12px}
.m-field-label{font-size:9px;color:#aaa;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:2px}
.m-field-val{font-size:13px;font-weight:600;color:#333}
.med-row{background:#f7f9fb;border-radius:8px;padding:10px 12px;margin-bottom:6px;display:flex;justify-content:space-between;align-items:flex-start;gap:10px}
.med-info b{display:block;font-size:13px;color:#333}
.med-info span{font-size:11px;color:#888}
.med-sub{text-align:right;font-weight:700;color:#2e7d32;white-space:nowrap;font-size:13px}
.med-qty{font-size:10px;color:#888}
.total-row{background:#e8f5e9;border-radius:8px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;margin-top:8px}
.total-label{font-size:13px;color:#555;font-weight:600}
.total-val{font-size:20px;font-weight:900;color:#2e7d32}
.notas-area{width:100%;border:2px solid #e0e0e0;border-radius:8px;padding:10px;font-size:13px;resize:vertical;min-height:70px;outline:none;font-family:inherit}
.notas-area:focus{border-color:#25d366}
.btn-save{background:#25d366;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;margin-top:6px}
.btn-save:hover{background:#1da851}
.estado-select{width:100%;padding:9px;border:2px solid #e0e0e0;border-radius:8px;font-size:13px;outline:none}
.estado-select:focus{border-color:#25d366}
.btn-update-estado{background:#128c7e;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;margin-top:6px}
.btn-update-estado:hover{background:#0e7268}
.alert-success{background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;color:#2e7d32;font-size:13px;font-weight:600;margin-bottom:12px;display:none}

.footer{text-align:center;padding:20px 0 8px;font-size:11px;color:#ccc}

@media(max-width:800px){
  .panels-row{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:1fr 1fr}
  .filter-row{flex-direction:column}
  table{display:block;overflow-x:auto}
  .m-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<div class="top-bar">
  <div class="logo">
    <span style="font-size:28px">💬</span>
    <div>Pedidos WhatsApp<small>FarmanorBot · Panel de gestión</small></div>
  </div>
  <div class="top-actions">
    <a class="top-btn" href="{{ route('wsp.pedidos.index') }}">↺ Actualizar</a>
    <a class="top-btn" href="/admin">← SISCON</a>
  </div>
</div>

<div class="container">

@if(session('success'))
<div class="alert-success" style="display:block;margin-bottom:12px">✓ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
  <div class="stat-card s-green"><span class="stat-icon">📋</span><span class="stat-num">{{ number_format($stats->total) }}</span><span class="stat-label">Total pedidos</span></div>
  <div class="stat-card s-teal"><span class="stat-icon">💰</span><span class="stat-num" style="font-size:18px">${{ number_format($stats->total_monto, 0, ',', '.') }}</span><span class="stat-label">Monto total</span></div>
  <div class="stat-card s-blue"><span class="stat-icon">🔵</span><span class="stat-num">{{ $stats->confirmados }}</span><span class="stat-label">Confirmados</span></div>
  <div class="stat-card s-orange"><span class="stat-icon">🟡</span><span class="stat-num">{{ $stats->procesando }}</span><span class="stat-label">Procesando</span></div>
  <div class="stat-card s-green"><span class="stat-icon">✅</span><span class="stat-num">{{ $stats->entregados }}</span><span class="stat-label">Entregados</span></div>
  <div class="stat-card s-gray"><span class="stat-icon">📅</span><span class="stat-num">{{ $stats->hoy }}</span><span class="stat-label">Hoy</span></div>
</div>

{{-- Panels --}}
<div class="panels-row">
  <div class="panel">
    <div class="panel-title">Por método de pago</div>
    @foreach($stats->por_metodo as $r)
    @php $icon = $pagoIcon[strtolower($r->metodo_pago ?? '')] ?? '💳'; @endphp
    <div class="panel-row">
      <span class="panel-label">{{ $icon }} {{ ucfirst($r->metodo_pago ?? '—') }}</span>
      <span class="panel-val">{{ $r->cnt }} pedidos <span class="money" style="font-size:11px;margin-left:4px">${{ number_format($r->monto, 0, ',', '.') }}</span></span>
    </div>
    @endforeach
  </div>
  <div class="panel">
    <div class="panel-title">Actividad últimos 7 días</div>
    @php $maxDia = $stats->ultimos_7dias->max('cnt') ?: 1; @endphp
    @foreach($stats->ultimos_7dias as $r)
    @php $pct = round($r->cnt / $maxDia * 100); @endphp
    <div class="bar-row">
      <span class="bar-label">{{ \Carbon\Carbon::parse($r->dia)->format('d/m') }}</span>
      <div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct }}%;background:#25d366"></div></div>
      <span class="bar-val">{{ $r->cnt }}</span>
    </div>
    @endforeach
    @if($stats->ultimos_7dias->isEmpty())<p style="font-size:12px;color:#ccc;text-align:center">Sin datos</p>@endif
  </div>
</div>

{{-- Search --}}
<div class="search-box">
  <form method="GET" action="{{ route('wsp.pedidos.index') }}">
    <div class="filter-row">
      <div class="inp-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="s-input" name="q" value="{{ $search }}" placeholder="ID, cliente, DNI, número WhatsApp, dirección…" autocomplete="off">
      </div>
      <select class="s-select" name="estado">
        <option value="">Todos los estados</option>
        @foreach($estados as $e)<option value="{{ $e }}" {{ $e === $estado ? 'selected' : '' }}>{{ ucfirst(strtolower($e)) }}</option>@endforeach
      </select>
      <select class="s-select" name="tipo">
        <option value="">Todos los envíos</option>
        <option value="A_DOMICILIO" {{ $tipoEnvio === 'A_DOMICILIO' ? 'selected' : '' }}>🛵 A domicilio</option>
        <option value="RETIRO_LOCAL" {{ $tipoEnvio === 'RETIRO_LOCAL' ? 'selected' : '' }}>🏪 Retiro local</option>
      </select>
      <select class="s-select" name="pago">
        <option value="">Todos los pagos</option>
        @foreach(['mercadopago','transferencia','tarjeta','efectivo'] as $p)
        <option value="{{ $p }}" {{ $metodoPago === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
        @endforeach
      </select>
      <input class="s-select" type="date" name="fecha" value="{{ $fecha }}" style="min-width:140px">
      <button class="btn-search" type="submit">Buscar</button>
      <a class="btn-clear" href="{{ route('wsp.pedidos.index') }}">✕</a>
    </div>
  </form>
</div>

{{-- Results bar --}}
<div class="results-bar">
  <p class="results-info">Mostrando <strong>{{ count($rows) }}</strong> de <strong>{{ $total }}</strong> pedidos · Página <strong>{{ $page }}/{{ $pages }}</strong></p>
  <div class="filter-tags">
    @if($search)<span class="ftag">🔍 "{{ $search }}" <a href="{{ route('wsp.pedidos.index') }}?{{ http_build_query(array_filter(['estado'=>$estado,'tipo'=>$tipoEnvio,'pago'=>$metodoPago,'fecha'=>$fecha])) }}">×</a></span>@endif
    @if($estado)<span class="ftag">● {{ $estado }} <a href="{{ route('wsp.pedidos.index') }}?{{ http_build_query(array_filter(['q'=>$search,'tipo'=>$tipoEnvio,'pago'=>$metodoPago,'fecha'=>$fecha])) }}">×</a></span>@endif
    @if($tipoEnvio)<span class="ftag">{{ $tipoEnvio === 'A_DOMICILIO' ? '🛵' : '🏪' }} {{ str_replace('_',' ',$tipoEnvio) }} <a href="{{ route('wsp.pedidos.index') }}?{{ http_build_query(array_filter(['q'=>$search,'estado'=>$estado,'pago'=>$metodoPago,'fecha'=>$fecha])) }}">×</a></span>@endif
    @if($metodoPago)<span class="ftag">💳 {{ $metodoPago }} <a href="{{ route('wsp.pedidos.index') }}?{{ http_build_query(array_filter(['q'=>$search,'estado'=>$estado,'tipo'=>$tipoEnvio,'fecha'=>$fecha])) }}">×</a></span>@endif
    @if($fecha)<span class="ftag">📅 {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }} <a href="{{ route('wsp.pedidos.index') }}?{{ http_build_query(array_filter(['q'=>$search,'estado'=>$estado,'tipo'=>$tipoEnvio,'pago'=>$metodoPago])) }}">×</a></span>@endif
  </div>
</div>

{{-- Table --}}
@if($rows->isEmpty())
<div class="empty"><div class="empty-icon">💬</div><p>No se encontraron pedidos con esos criterios.</p></div>
@else
<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>ID Pedido</th>
        <th>WhatsApp</th>
        <th>Cliente</th>
        <th>Medicamentos</th>
        <th>Total</th>
        <th>Envío</th>
        <th>Pago</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
    @php
      $meds   = json_decode($row->medicamentos, true) ?? [];
      $style  = $estadoStyle[$row->estado] ?? $estadoStyle['CONFIRMADO'];
      $eIcon  = $envioIcon[$row->tipo_envio] ?? '📦';
      $pIcon  = $pagoIcon[strtolower($row->metodo_pago ?? '')] ?? '💳';
      $fecha  = $row->pedido_at ? \Carbon\Carbon::parse($row->pedido_at)->format('d/m/Y H:i') : '—';
    @endphp
    <tr onclick="abrirDetalle('{{ $row->pedido_id }}')">
      <td><span class="td-id">{{ $row->pedido_id }}</span></td>
      <td><span class="td-num">{{ $row->usuario_id ?? '—' }}</span></td>
      <td class="td-cliente">
        <b>{{ $row->cliente_nombre ?? '—' }}</b>
        <span>DNI: {{ $row->cliente_dni && $row->cliente_dni !== 'NO_REGISTRADO' ? $row->cliente_dni : '—' }}</span>
      </td>
      <td class="td-meds">
        @foreach(array_slice($meds, 0, 2) as $m)
          <span class="med-pill">{{ $m['nombre'] ?? '?' }}</span>
        @endforeach
        @if(count($meds) > 2)<span class="med-more">+{{ count($meds) - 2 }} más</span>@endif
      </td>
      <td class="td-total">${{ number_format((float)$row->total, 2, ',', '.') }}</td>
      <td class="td-envio">{{ $eIcon }} {{ str_replace('_',' ', $row->tipo_envio ?? '—') }}</td>
      <td class="td-pago">{{ $pIcon }} {{ ucfirst($row->metodo_pago ?? '—') }}</td>
      <td>
        <span class="estado-badge" style="background:{{ $style['bg'] }};color:{{ $style['color'] }}">
          <span class="estado-dot" style="background:{{ $style['dot'] }}"></span>{{ $row->estado }}
        </span>
      </td>
      <td class="td-fecha">{{ $fecha }}</td>
      <td onclick="event.stopPropagation()">
        <button class="btn-estado" onclick="abrirDetalle('{{ $row->pedido_id }}')">Ver</button>
      </td>
    </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- Pagination --}}
@if($pages > 1)
@php
$qsExtra = collect([['q',$search],['estado',$estado],['tipo',$tipoEnvio],['pago',$metodoPago],['fecha',$fecha]])
    ->filter(fn($p) => $p[1])
    ->map(fn($p) => $p[0].'='.urlencode($p[1]))
    ->implode('&');
$qsExtra = $qsExtra ? '&'.$qsExtra : '';
@endphp
<div class="pagination">
  @if($page > 1)<a class="pg-btn" href="?page={{ $page-1 }}{{ $qsExtra }}">‹ Ant.</a>@else<span class="pg-btn pg-dis">‹ Ant.</span>@endif
  <div class="pg-nums">
    @php $lo = max(1,$page-3); $hi = min($pages,$page+3); @endphp
    @if($lo>1)<a class="pg-num" href="?page=1{{ $qsExtra }}">1</a>@endif
    @if($lo>2)<span class="pg-ell">…</span>@endif
    @for($p=$lo;$p<=$hi;$p++)<a class="pg-num {{ $p===$page?'pg-cur':'' }}" href="?page={{ $p }}{{ $qsExtra }}">{{ $p }}</a>@endfor
    @if($hi<$pages-1)<span class="pg-ell">…</span>@endif
    @if($hi<$pages)<a class="pg-num" href="?page={{ $pages }}{{ $qsExtra }}">{{ $pages }}</a>@endif
  </div>
  @if($page < $pages)<a class="pg-btn" href="?page={{ $page+1 }}{{ $qsExtra }}">Sig. ›</a>@else<span class="pg-btn pg-dis">Sig. ›</span>@endif
</div>
@endif

<div class="footer">SISCON · Pedidos WhatsApp · {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- Modal detalle --}}
<div class="modal-bg" id="modalBg" onclick="if(event.target===this)cerrarModal()">
  <div class="modal" id="modalContent">
    <div style="padding:40px;text-align:center;color:#aaa">Cargando…</div>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function abrirDetalle(pedidoId) {
  document.getElementById('modalBg').classList.add('open');
  document.getElementById('modalContent').innerHTML = '<div style="padding:40px;text-align:center;color:#aaa">Cargando…</div>';

  fetch('/wsp/pedidos/' + pedidoId + '/detalle', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => { document.getElementById('modalContent').innerHTML = html; });
}

function cerrarModal() {
  document.getElementById('modalBg').classList.remove('open');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

function actualizarEstado(pedidoId) {
  const sel   = document.getElementById('estado-sel-' + pedidoId);
  const msg   = document.getElementById('msg-' + pedidoId);
  const nuevo = sel.value;

  fetch('/wsp/pedidos/' + pedidoId + '/estado', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ estado: nuevo })
  })
  .then(r => r.json())
  .then(d => {
    if (d.ok) {
      msg.style.display = 'block';
      msg.textContent = '✓ Estado actualizado a ' + nuevo;
      setTimeout(() => { msg.style.display='none'; }, 3000);
      // Actualizar badge en la tabla
      const badge = document.querySelector(`tr[data-id="${pedidoId}"] .estado-badge`);
      if (badge) badge.textContent = nuevo;
    }
  });
}

function guardarNotas(pedidoId) {
  const notas = document.getElementById('notas-' + pedidoId).value;
  const btn   = document.getElementById('notas-btn-' + pedidoId);
  btn.textContent = 'Guardando…';

  fetch('/wsp/pedidos/' + pedidoId + '/notas', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ notas: notas })
  })
  .then(r => r.json())
  .then(d => {
    btn.textContent = d.ok ? '✓ Guardado' : 'Error';
    setTimeout(() => { btn.textContent = 'Guardar notas'; }, 2500);
  });
}
</script>
</body>
</html>
