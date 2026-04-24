@php
$style = $pedido->estado_style;
$meds  = $pedido->medicamentos_arr;
@endphp

<div class="modal-header">
  <div>
    <div class="modal-title">{{ $pedido->pedido_id }}</div>
    <div class="modal-sub">
      {{ $pedido->pedido_at ? \Carbon\Carbon::parse($pedido->pedido_at)->format('d/m/Y H:i') : '—' }}
      @if($pedido->usuario_id) · 📱 {{ $pedido->usuario_id }}@endif
    </div>
  </div>
  <button class="modal-close" onclick="cerrarModal()">×</button>
</div>

<div class="modal-body">

  {{-- Estado + alerta --}}
  <div id="msg-{{ $pedido->pedido_id }}" class="alert-success"></div>

  <div class="m-section">
    <div class="m-section-title">Estado del pedido</div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <span class="estado-badge" style="background:{{ $style['bg'] }};color:{{ $style['color'] }};font-size:13px;padding:5px 14px">
        <span class="estado-dot" style="background:{{ $style['dot'] }}"></span>{{ $pedido->estado }}
      </span>
      <select id="estado-sel-{{ $pedido->pedido_id }}" class="estado-select" style="max-width:180px">
        @foreach(['CONFIRMADO','PROCESANDO','ENTREGADO','CANCELADO'] as $e)
        <option value="{{ $e }}" {{ $e === $pedido->estado ? 'selected' : '' }}>{{ ucfirst(strtolower($e)) }}</option>
        @endforeach
      </select>
      <button class="btn-update-estado" onclick="actualizarEstado('{{ $pedido->pedido_id }}')">Actualizar</button>
    </div>
  </div>

  {{-- Cliente --}}
  <div class="m-section">
    <div class="m-section-title">Cliente</div>
    <div class="m-grid">
      <div class="m-field">
        <span class="m-field-label">Nombre</span>
        <span class="m-field-val">{{ $pedido->cliente_nombre ?? '—' }}</span>
      </div>
      <div class="m-field">
        <span class="m-field-label">DNI</span>
        <span class="m-field-val">{{ ($pedido->cliente_dni && $pedido->cliente_dni !== 'NO_REGISTRADO') ? $pedido->cliente_dni : '—' }}</span>
      </div>
      <div class="m-field">
        <span class="m-field-label">WhatsApp</span>
        <span class="m-field-val" style="font-family:'SF Mono',Consolas,monospace">
          @if($pedido->usuario_id)
            <a href="https://wa.me/{{ $pedido->usuario_id }}" target="_blank" style="color:#25d366;text-decoration:none">📱 {{ $pedido->usuario_id }}</a>
          @else —
          @endif
        </span>
      </div>
      @if($pedido->sucursal_id)
      <div class="m-field">
        <span class="m-field-label">Sucursal</span>
        <span class="m-field-val">#{{ $pedido->sucursal_id }}</span>
      </div>
      @endif
    </div>
  </div>

  {{-- Medicamentos --}}
  <div class="m-section">
    <div class="m-section-title">Medicamentos ({{ count($meds) }})</div>
    @foreach($meds as $m)
    <div class="med-row">
      <div class="med-info">
        <b>{{ $m['nombre'] ?? '?' }}</b>
        <span>
          @if(!empty($m['codigo']))Cód: {{ $m['codigo'] }}@endif
          @if(!empty($m['id_articulo']) && ($m['id_articulo'] ?? '') !== ($m['codigo'] ?? '')) · Art: {{ $m['id_articulo'] }}@endif
        </span>
      </div>
      <div style="text-align:right">
        <div class="med-sub">${{ number_format((float)($m['subtotal'] ?? 0), 2, ',', '.') }}</div>
        <div class="med-qty">{{ $m['cantidad'] ?? 1 }} × ${{ number_format((float)($m['precio_unitario'] ?? ($m['subtotal'] ?? 0) / max(1, $m['cantidad'] ?? 1)), 2, ',', '.') }}</div>
      </div>
    </div>
    @endforeach
    <div class="total-row">
      <span class="total-label">Total</span>
      <span class="total-val">${{ number_format((float)$pedido->total, 2, ',', '.') }}</span>
    </div>
  </div>

  {{-- Entrega --}}
  <div class="m-section">
    <div class="m-section-title">Entrega y pago</div>
    <div class="m-grid">
      <div class="m-field">
        <span class="m-field-label">Tipo de envío</span>
        <span class="m-field-val">
          @if($pedido->tipo_envio === 'A_DOMICILIO') 🛵 A domicilio
          @elseif($pedido->tipo_envio === 'RETIRO_LOCAL') 🏪 Retiro local
          @else {{ $pedido->tipo_envio ?? '—' }}
          @endif
        </span>
      </div>
      <div class="m-field">
        <span class="m-field-label">Método de pago</span>
        <span class="m-field-val">💳 {{ ucfirst($pedido->metodo_pago ?? '—') }}</span>
      </div>
    </div>
    @if($pedido->ubicacion)
    <div class="m-field" style="margin-top:8px">
      <span class="m-field-label">Ubicación / dirección</span>
      <span class="m-field-val" style="font-size:13px">
        @if(str_starts_with($pedido->ubicacion, 'http'))
          <a href="{{ $pedido->ubicacion }}" target="_blank" style="color:#0077b6">📍 Ver en mapa</a>
        @else
          📍 {{ $pedido->ubicacion }}
        @endif
      </span>
    </div>
    @endif
  </div>

  {{-- Notas --}}
  <div class="m-section">
    <div class="m-section-title">Notas internas</div>
    <textarea id="notas-{{ $pedido->pedido_id }}" class="notas-area" placeholder="Agregar notas sobre este pedido…">{{ $pedido->notas ?? '' }}</textarea>
    <button id="notas-btn-{{ $pedido->pedido_id }}" class="btn-save" onclick="guardarNotas('{{ $pedido->pedido_id }}')">Guardar notas</button>
  </div>

</div>
