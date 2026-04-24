<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Seguimiento de Envíos — Global Médica</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;background:#f0f4f8;color:#212121;min-height:100vh}

.top-bar{background:linear-gradient(135deg,#0099cc,#0077b6);padding:0 24px;display:flex;align-items:center;justify-content:space-between;height:60px;box-shadow:0 2px 10px rgba(0,119,182,.35);position:sticky;top:0;z-index:100}
.logo{font-size:20px;font-weight:900;color:#fff}
.logo small{font-size:11px;font-weight:400;opacity:.75;display:block;margin-top:1px}
.db-pill{font-size:11px;border-radius:20px;padding:4px 12px;font-weight:600;border:1.5px solid rgba(255,255,255,.4);color:#fff}
.db-pill.ok{border-color:#69f0ae;color:#69f0ae}
.db-pill.err{border-color:#ff8a80;color:#ff8a80}

.container{max-width:940px;margin:0 auto;padding:20px 14px}

.dashboard{margin-bottom:18px}
.dash-stats{display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap}
.dash-stat{background:#fff;border-radius:12px;padding:14px 16px;flex:1;min-width:120px;box-shadow:0 1px 5px rgba(0,0,0,.08);border-top:3px solid #e0e0e0;display:flex;flex-direction:column;align-items:flex-start;gap:3px}
.dash-stat.s-blue{border-top-color:#0099cc}.dash-stat.s-teal{border-top-color:#00897b}
.dash-stat.s-green{border-top-color:#43a047}.dash-stat.s-warn{border-top-color:#e65100}
.dash-stat.s-ok{border-top-color:#2e7d32}.dash-stat.s-gray{border-top-color:#90a4ae}
.ds-icon{font-size:18px;line-height:1}
.ds-num{font-size:26px;font-weight:900;line-height:1;color:#212121}
.ds-label{font-size:10px;color:#999;text-transform:uppercase;letter-spacing:.4px}

.dash-panels{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.dash-panel{background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 5px rgba(0,0,0,.08)}
.panel-title{font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}

.t-bars{display:flex;flex-direction:column;gap:8px}
.t-bar-row{display:grid;grid-template-columns:130px 1fr 140px;align-items:center;gap:8px}
.t-bar-name{font-size:12px;font-weight:600;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.t-bar-wrap{height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden}
.t-bar-fill{height:100%;border-radius:4px}
.t-bar-val{font-size:11px;color:#888;white-space:nowrap}

.mes-chips{display:flex;flex-wrap:wrap;gap:8px}
.mes-chip{background:#f5f7fa;border:1px solid #e0e0e0;border-radius:9px;padding:6px 12px;display:flex;flex-direction:column;align-items:center;min-width:70px}
.mes-name{font-size:11px;color:#888}
.mes-n{font-size:20px;font-weight:800;color:#0099cc;line-height:1.2}

.search-box{background:#fff;border-radius:14px;padding:16px 18px;margin-bottom:14px;box-shadow:0 1px 5px rgba(0,0,0,.08)}
.search-row{display:flex;gap:8px;flex-wrap:wrap}
.input-wrap{flex:2;min-width:180px;position:relative}
.input-wrap svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.35;pointer-events:none}
.s-input{width:100%;padding:9px 10px 9px 33px;border:2px solid #e0e0e0;border-radius:9px;font-size:13px;outline:none;color:#333}
.s-input:focus{border-color:#0099cc;box-shadow:0 0 0 3px rgba(0,153,204,.1)}
.s-select{padding:9px 10px;border:2px solid #e0e0e0;border-radius:9px;font-size:13px;outline:none;color:#333;cursor:pointer;min-width:130px}
.s-select:focus{border-color:#0099cc}
.btn-primary{background:#0099cc;color:#fff;border:none;border-radius:9px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap}
.btn-primary:hover{background:#0077b6}
.btn-ghost{background:#fff;color:#666;border:2px solid #e0e0e0;border-radius:9px;padding:8px 14px;font-size:13px;cursor:pointer;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center}
.btn-ghost:hover{border-color:#aaa;color:#333}

.results-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;padding:0 2px;flex-wrap:wrap;gap:6px}
.results-info{font-size:12px;color:#888}
.results-info strong{color:#333}
.active-filters{display:flex;gap:6px;flex-wrap:wrap}
.filter-tag{background:#e3f2fd;color:#1565c0;border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:4px}
.filter-tag a{color:#1565c0;text-decoration:none;font-weight:700;margin-left:2px}

.envio-card{background:#fff;border-radius:13px;margin-bottom:11px;overflow:hidden;box-shadow:0 1px 5px rgba(0,0,0,.08);transition:box-shadow .18s,transform .14s}
.envio-card:hover{box-shadow:0 5px 20px rgba(0,0,0,.12);transform:translateY(-1px)}
.card-header{display:flex;justify-content:space-between;align-items:flex-start;padding:13px 18px;background:#fafafa;border-bottom:1px solid #f0f0f0;gap:10px}
.header-left{display:flex;flex-direction:column;gap:4px;flex:1}
.envio-id-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.envio-id{font-size:15px;font-weight:800;color:#0077b6;font-family:'SF Mono',Consolas,monospace}
.transp-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700}
.t-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.card-sub{font-size:11px;color:#aaa;line-height:1.5}
.estado-badge{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0}

.warn-bar{background:#fff3e0;border-bottom:1px solid #ffe0b2;padding:7px 18px;font-size:12px;color:#e65100;font-weight:500}

.guia-section{background:#f7faff;border-bottom:1px solid #e8f0ff;padding:10px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.guia-item{display:flex;align-items:center;gap:10px}
.guia-label{font-size:10px;color:#0099cc;font-weight:700;text-transform:uppercase;letter-spacing:.6px;white-space:nowrap}
.guia-num{font-size:14px;font-weight:700;color:#1a1a1a;font-family:'SF Mono',Consolas,monospace;background:#fff;border:1.5px solid #90d0f0;border-radius:6px;padding:2px 10px}
.guia-missing{color:#aaa;border-color:#e0e0e0;font-style:italic;font-size:12px}
.guia-meta{display:flex;gap:6px;flex-wrap:wrap}
.meta-pill{background:#fff;border:1px solid #e0e8f0;border-radius:8px;padding:4px 10px;font-size:12px;color:#555;font-weight:500}
.cost-pill{background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;font-weight:700}

.timeline{display:flex;padding:13px 18px 10px;overflow-x:auto}
.timeline-step{display:flex;flex-direction:column;align-items:center;flex:1;min-width:68px;position:relative}
.timeline-step:not(:last-child)::after{content:'';position:absolute;top:12px;left:50%;width:100%;height:2px;background:#e0e0e0;z-index:0}
.step-done:not(:last-child)::after,.step-active:not(:last-child)::after{background:#0099cc}
.step-active:not(:last-child)::after{background:linear-gradient(90deg,#0099cc 50%,#e0e0e0 50%)}
.step-icon{width:25px;height:25px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;z-index:1;background:#fff;border:2px solid #e0e0e0}
.step-done .step-icon{background:#0099cc;color:#fff;border-color:#0099cc;font-size:12px}
.step-active .step-icon{background:#fff;color:#0099cc;border-color:#0099cc;box-shadow:0 0 0 3px rgba(0,153,204,.18)}
.step-pending .step-icon{color:#ccc}
.step-label{font-size:9px;color:#bbb;margin-top:5px;text-align:center;line-height:1.3;max-width:68px}
.step-done .step-label{color:#0099cc;font-weight:600}
.step-active .step-label{color:#0077b6;font-weight:700}

.card-body{padding:13px 18px}
.details-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-bottom:12px}
.detail-item{background:#f7f9fb;border-radius:8px;padding:9px 12px}
.d-label{display:block;font-size:9px;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px}
.d-val{font-size:13px;font-weight:600;color:#333}
.cost-highlight{color:#2e7d32;font-size:14px}

.remitos-section{border-top:1px solid #f0f0f0;padding-top:11px}
.rem-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:4px}
.rem-title{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.4px}
.rem-range{font-size:11px;color:#aaa;font-family:'SF Mono',Consolas,monospace}
.rem-chips{display:flex;flex-wrap:wrap;gap:4px}
.rem-chip{background:#f0f4f8;border:1px solid #dde3ea;border-radius:5px;padding:2px 8px;font-size:11px;font-family:'SF Mono',Consolas,monospace;color:#444}
.rem-chip:hover{background:#e3f2fd;border-color:#90caf9;color:#1565c0}
.rem-more{background:#e3f2fd;border:1px solid #90caf9;border-radius:5px;padding:2px 8px;font-size:11px;color:#1565c0;font-weight:600}

.pagination{display:flex;align-items:center;justify-content:center;gap:7px;padding:18px 0 6px;flex-wrap:wrap}
.pg-btn{background:#fff;border:2px solid #0099cc;color:#0099cc;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:600;text-decoration:none}
.pg-btn:hover{background:#0099cc;color:#fff}
.pg-dis{background:#f5f5f5;border-color:#e0e0e0;color:#ccc;pointer-events:none}
.pg-nums{display:flex;gap:3px;flex-wrap:wrap;justify-content:center}
.pg-num{background:#fff;border:1.5px solid #e0e0e0;color:#555;border-radius:7px;padding:6px 11px;font-size:12px;text-decoration:none;min-width:34px;text-align:center;display:inline-block}
.pg-num:hover{border-color:#0099cc;color:#0099cc}
.pg-cur{background:#0099cc!important;border-color:#0099cc!important;color:#fff!important;font-weight:700}
.pg-ell{color:#aaa;padding:6px 3px;font-size:12px}

.empty{text-align:center;padding:50px 20px;color:#ccc}
.empty-icon{font-size:44px;margin-bottom:10px}
.alert-error{background:#fff0f0;border:1px solid #ffcdd2;border-radius:10px;padding:14px 18px;color:#c62828;font-size:13px;margin-bottom:14px}
.footer{text-align:center;padding:22px 0 10px;font-size:11px;color:#ccc}

@media(max-width:700px){
  .top-bar{height:54px;padding:0 12px}
  .container{padding:10px 8px}
  .dash-panels{grid-template-columns:1fr}
  .dash-stats{gap:7px}
  .t-bar-row{grid-template-columns:90px 1fr 100px}
  .card-header{flex-direction:column}
  .timeline-step{min-width:54px}
  .step-label{font-size:8px}
  .details-grid{grid-template-columns:1fr 1fr}
  .guia-section{flex-direction:column;align-items:flex-start}
  .search-row{flex-direction:column}
}
</style>
</head>
<body>

@php
use App\Http\Controllers\TrackingController;

$timelineLabels = ['Pedido recibido','En preparación','Despachado','En tránsito','Entregado'];

$fmtFecha = function($v) {
    if (!$v) return '—';
    try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch (\Exception $e) { return $v; }
};

$fmtMes = function($v) {
    if (!$v) return $v;
    try { return ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $v)->translatedFormat('M Y')); } catch (\Exception $e) { return $v; }
};

$fmtPrecio = function($v) {
    if (!$v) return '—';
    $f = (float)$v;
    return $f > 0 ? '$'.number_format($f, 0, ',', '.') : '—';
};

$qsExtra = collect([['q',$search],['t',$transporte],['op',$operador],['mes',$mes]])
    ->filter(fn($p) => $p[1])
    ->map(fn($p) => $p[0].'='.urlencode($p[1]))
    ->implode('&');
$qsExtra = $qsExtra ? '&'.$qsExtra : '';
@endphp

<div class="top-bar">
  <div class="logo">GlobalMédica <small>Sistema de seguimiento de envíos</small></div>
  <span class="db-pill {{ $dbOnline ? 'ok' : 'err' }}">{{ $dbOnline ? '● En línea' : '● Sin conexión' }}</span>
</div>

<div class="container">

{{-- Dashboard --}}
@if($totals)
@php
$sinGuia     = (int)($totals->sin_guia ?? 0);
$totalEnvios = (int)($totals->total_envios ?? 0);
$totalRem    = (int)($totals->total_remitos ?? 0);
$totalBultos = (int)($totals->total_bultos ?? 0);
$ultDespacho = $fmtFecha($totals->ultimo_despacho ?? null);
@endphp
<div class="dashboard">
  <div class="dash-stats">
    <div class="dash-stat s-blue"><div class="ds-icon">📦</div><div class="ds-num">{{ number_format($totalEnvios) }}</div><div class="ds-label">Envíos totales</div></div>
    <div class="dash-stat s-teal"><div class="ds-icon">🧾</div><div class="ds-num">{{ number_format($totalRem) }}</div><div class="ds-label">Remitos despachados</div></div>
    <div class="dash-stat s-green"><div class="ds-icon">🚚</div><div class="ds-num">{{ number_format($totalBultos) }}</div><div class="ds-label">Bultos despachados</div></div>
    <div class="dash-stat {{ $sinGuia > 0 ? 's-warn' : 's-ok' }}"><div class="ds-icon">{{ $sinGuia > 0 ? '⚠️' : '✅' }}</div><div class="ds-num">{{ $sinGuia }}</div><div class="ds-label">Sin guía asignada</div></div>
    <div class="dash-stat s-gray"><div class="ds-icon">📅</div><div class="ds-num" style="font-size:18px">{{ $ultDespacho }}</div><div class="ds-label">Último despacho</div></div>
  </div>
  <div class="dash-panels">
    <div class="dash-panel">
      <div class="panel-title">Envíos por transportista</div>
      <div class="t-bars">
        @php $maxE = collect($porTransporte)->max('envios') ?: 1; @endphp
        @foreach($porTransporte as $r)
        @php $tc = TrackingController::transportColor($r->descripcion); $pct = round($r->envios / $maxE * 100); @endphp
        <div class="t-bar-row">
          <span class="t-bar-name" style="color:{{ $tc['color'] }}">{{ $r->descripcion }}</span>
          <div class="t-bar-wrap"><div class="t-bar-fill" style="width:{{ $pct }}%;background:{{ $tc['dot'] }}"></div></div>
          <span class="t-bar-val">{{ $r->envios }} env · {{ (int)$r->remitos }} rem</span>
        </div>
        @endforeach
      </div>
    </div>
    <div class="dash-panel">
      <div class="panel-title">Actividad por mes</div>
      <div class="mes-chips">
        @foreach($porMes as $r)
        <div class="mes-chip"><span class="mes-name">{{ $fmtMes($r->mes) }}</span><span class="mes-n">{{ $r->envios }}</span></div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

{{-- Search --}}
<div class="search-box">
  <form method="GET" action="{{ route('tracking.index') }}">
    <div class="search-row">
      <div class="input-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="s-input" name="q" value="{{ $search }}" placeholder="N° guía, N° remito o ID envío…" autocomplete="off">
      </div>
      <select class="s-select" name="t">
        <option value="">Todos los transportes</option>
        @foreach($transportesList as $t)<option value="{{ $t }}" {{ $t === $transporte ? 'selected' : '' }}>{{ $t }}</option>@endforeach
      </select>
      <select class="s-select" name="op">
        <option value="">Todos los operadores</option>
        @foreach($operadoresList as $o)<option value="{{ $o }}" {{ $o === $operador ? 'selected' : '' }}>{{ $o }}</option>@endforeach
      </select>
      <select class="s-select" name="mes">
        <option value="">Todos los meses</option>
        @foreach($mesesList as $m)<option value="{{ $m }}" {{ $m === $mes ? 'selected' : '' }}>{{ $fmtMes($m) }}</option>@endforeach
      </select>
      <button class="btn-primary" type="submit">Buscar</button>
      <a class="btn-ghost" href="{{ route('tracking.index') }}">✕ Limpiar</a>
    </div>
  </form>
</div>

{{-- Results bar --}}
<div class="results-bar">
  <p class="results-info">Mostrando <strong>{{ count($rows) }}</strong> de <strong>{{ $total }}</strong> envíos · Página <strong>{{ $page }}/{{ $pages }}</strong></p>
  <div class="active-filters">
    @if($search)<span class="filter-tag">🔍 "{{ $search }}" <a href="{{ route('tracking.index') }}?{{ http_build_query(array_filter(['t'=>$transporte,'op'=>$operador,'mes'=>$mes])) }}">×</a></span>@endif
    @if($transporte)<span class="filter-tag">🚚 {{ $transporte }} <a href="{{ route('tracking.index') }}?{{ http_build_query(array_filter(['q'=>$search,'op'=>$operador,'mes'=>$mes])) }}">×</a></span>@endif
    @if($operador)<span class="filter-tag">👤 {{ $operador }} <a href="{{ route('tracking.index') }}?{{ http_build_query(array_filter(['q'=>$search,'t'=>$transporte,'mes'=>$mes])) }}">×</a></span>@endif
    @if($mes)<span class="filter-tag">📅 {{ $fmtMes($mes) }} <a href="{{ route('tracking.index') }}?{{ http_build_query(array_filter(['q'=>$search,'t'=>$transporte,'op'=>$operador])) }}">×</a></span>@endif
  </div>
</div>

@if($error)
  <div class="alert-error"><strong>Error de conexión:</strong> {{ $error }}</div>
@elseif(empty($rows))
  <div class="empty"><div class="empty-icon">📦</div><p>No se encontraron envíos con esos criterios.</p></div>
@else
  @foreach($rows as $row)
  @php
    $estado  = TrackingController::calcularEstado($row->nro_guia ?? null, $row->dias_transcurridos ?? null);
    $tc      = TrackingController::transportColor($row->transporte ?? '');
    $fecha   = $fmtFecha($row->fecha_guia ?? null);
    $nro     = $row->nro_guia ?: 'Sin guía asignada';
    $bultos  = $row->bultos ?? '—';
    $costo   = $fmtPrecio($row->costo_total ?? null);
    $usuario = strtoupper($row->id_usuario ?? '—');
    $cantR   = (int)($row->cant_remitos ?? 0);
    $primerR = $row->primer_remito ?? '—';
    $ultimoR = $row->ultimo_remito ?? '—';
    $dias    = $row->dias_transcurridos ?? null;
    $diasTxt = ($dias !== null && $dias >= 0) ? "hace {$dias} días" : '—';
    $remitos = $row->remitos ?? [];
    $step    = $estado['step'];
  @endphp
  <div class="envio-card">
    <div class="card-header">
      <div class="header-left">
        <div class="envio-id-row">
          <span class="envio-id">Envío #{{ $row->id_envio }}</span>
          <span class="transp-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">
            <span class="t-dot" style="background:{{ $tc['dot'] }}"></span>{{ $row->transporte ?? '—' }}
          </span>
        </div>
        <span class="card-sub">Despachado el {{ $fecha }} · {{ $diasTxt }} · Operador: {{ $usuario }}</span>
      </div>
      <span class="estado-badge" style="background:{{ $estado['bg'] }};color:{{ $estado['color'] }}">{{ $estado['icon'] }} {{ $estado['label'] }}</span>
    </div>

    @if(!$row->nro_guia)<div class="warn-bar">⚠️ Este envío no tiene número de guía asignado todavía</div>@endif

    <div class="guia-section">
      <div class="guia-item">
        <span class="guia-label">N° Guía de envío</span>
        <span class="guia-num {{ !$row->nro_guia ? 'guia-missing' : '' }}">{{ $nro }}</span>
      </div>
      <div class="guia-meta">
        <span class="meta-pill">📦 {{ $bultos }} bulto{{ $bultos != 1 ? 's' : '' }}</span>
        <span class="meta-pill">🧾 {{ $cantR }} remito{{ $cantR != 1 ? 's' : '' }}</span>
        @if($costo !== '—')<span class="meta-pill cost-pill">💰 {{ $costo }} total</span>@endif
      </div>
    </div>

    {{-- Timeline --}}
    <div class="timeline">
      @foreach(['Pedido recibido','En preparación','Despachado','En tránsito','Entregado'] as $i => $label)
      @php
        if ($i < $step)       { $cls = 'step-done';    $icon = '✓'; }
        elseif ($i === $step) { $cls = 'step-active';  $icon = '●'; }
        else                   { $cls = 'step-pending'; $icon = '○'; }
      @endphp
      <div class="timeline-step {{ $cls }}">
        <span class="step-icon">{{ $icon }}</span>
        <span class="step-label">{{ $label }}</span>
      </div>
      @endforeach
    </div>

    <div class="card-body">
      <div class="details-grid">
        <div class="detail-item"><span class="d-label">Transportista</span><span class="d-val" style="color:{{ $tc['color'] }}">{{ $row->transporte ?? '—' }}</span></div>
        <div class="detail-item"><span class="d-label">Fecha de despacho</span><span class="d-val">{{ $fecha }}</span></div>
        <div class="detail-item"><span class="d-label">Días en tránsito</span><span class="d-val">{{ $diasTxt }}</span></div>
        <div class="detail-item"><span class="d-label">Bultos despachados</span><span class="d-val">{{ $bultos }}</span></div>
        <div class="detail-item"><span class="d-label">Costo por bulto</span><span class="d-val">{{ $fmtPrecio($row->precio_bulto ?? null) }}</span></div>
        <div class="detail-item"><span class="d-label">Costo total envío</span><span class="d-val {{ $costo !== '—' ? 'cost-highlight' : '' }}">{{ $costo }}</span></div>
        <div class="detail-item"><span class="d-label">Operador</span><span class="d-val">{{ $usuario }}</span></div>
        <div class="detail-item"><span class="d-label">Cantidad de remitos</span><span class="d-val">{{ $cantR }}</span></div>
      </div>

      <div class="remitos-section">
        <div class="rem-header">
          <span class="rem-title">Remitos incluidos ({{ $cantR }})</span>
          <span class="rem-range">de {{ $primerR }} a {{ $ultimoR }}</span>
        </div>
        @if(!empty($remitos))
        <div class="rem-chips">
          @foreach(array_slice($remitos, 0, 40) as $r)
            <span class="rem-chip">{{ $r->id_remito }}</span>
          @endforeach
          @if(count($remitos) > 40)<span class="rem-more">+{{ count($remitos) - 40 }} más</span>@endif
        </div>
        @endif
      </div>
    </div>
  </div>
  @endforeach
@endif

{{-- Pagination --}}
@if($pages > 1)
<div class="pagination">
  @if($page > 1)
    <a class="pg-btn" href="{{ route('tracking.index') }}?page={{ $page - 1 }}{{ $qsExtra }}">‹ Ant.</a>
  @else
    <span class="pg-btn pg-dis">‹ Ant.</span>
  @endif

  <div class="pg-nums">
    @php $lo = max(1, $page - 3); $hi = min($pages, $page + 3); @endphp
    @if($lo > 1)<a class="pg-num" href="{{ route('tracking.index') }}?page=1{{ $qsExtra }}">1</a>@endif
    @if($lo > 2)<span class="pg-ell">…</span>@endif
    @for($p = $lo; $p <= $hi; $p++)
      <a class="pg-num {{ $p === $page ? 'pg-cur' : '' }}" href="{{ route('tracking.index') }}?page={{ $p }}{{ $qsExtra }}">{{ $p }}</a>
    @endfor
    @if($hi < $pages - 1)<span class="pg-ell">…</span>@endif
    @if($hi < $pages)<a class="pg-num" href="{{ route('tracking.index') }}?page={{ $pages }}{{ $qsExtra }}">{{ $pages }}</a>@endif
  </div>

  @if($page < $pages)
    <a class="pg-btn" href="{{ route('tracking.index') }}?page={{ $page + 1 }}{{ $qsExtra }}">Sig. ›</a>
  @else
    <span class="pg-btn pg-dis">Sig. ›</span>
  @endif
</div>
@endif

<div class="footer">Global Médica · Sistema de seguimiento · {{ now()->format('d/m/Y H:i') }}</div>
</div>
</body>
</html>
