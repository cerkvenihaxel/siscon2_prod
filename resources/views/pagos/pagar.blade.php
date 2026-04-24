<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pago — Global Médica</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:420px;width:90%;overflow:hidden}
.header{background:linear-gradient(135deg,#00b4d8,#0077b6);padding:24px;text-align:center;color:#fff}
.header h1{font-size:20px;margin-bottom:4px}
.header .monto{font-size:36px;font-weight:700;margin:12px 0}
.header .ref{font-size:12px;opacity:.8}
.body{padding:24px}
.desc{background:#f8f9fa;border-radius:8px;padding:12px;margin-bottom:20px;font-size:14px;color:#555}
.metodo{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
.metodo label{flex:1;min-width:120px;border:2px solid #e0e0e0;border-radius:10px;padding:14px 10px;text-align:center;cursor:pointer;font-size:13px;transition:.2s}
.metodo label.active{border-color:#0077b6;background:#e8f4fd}
.btn{display:block;width:100%;padding:16px;background:#0077b6;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none}
.btn:hover{background:#005f8a}
.seguro{text-align:center;margin-top:16px;font-size:11px;color:#999}
.seguro span{color:#2ecc71}
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <h1>Global Médica — Pago Seguro</h1>
    <div class="monto">${{ number_format((float)$pago->total, 2) }}</div>
    <div class="ref">Ref: {{ $pago->pedido_id }}</div>
  </div>
  <div class="body">
    <div class="desc">{{ $pago->descripcion }}</div>
    <div class="metodo">
      <label class="active" id="lbl-tarjeta"><input type="radio" name="m" value="tarjeta" checked> 💳 Tarjeta</label>
      <label id="lbl-transferencia"><input type="radio" name="m" value="transferencia"> 🏦 Transferencia</label>
      <label id="lbl-qr"><input type="radio" name="m" value="qr"> 📱 QR</label>
    </div>
    <a class="btn" id="pagarBtn" href="{{ route('pagos.confirmar', $pago->pago_id) }}?metodo=tarjeta">
      Pagar ${{ number_format((float)$pago->total, 2) }}
    </a>
    <div class="seguro"><span>🔒</span> Conexión segura — Global Médica</div>
  </div>
</div>
<script>
document.querySelectorAll('input[name="m"]').forEach(r => {
  r.addEventListener('change', e => {
    document.querySelectorAll('.metodo label').forEach(l => l.classList.remove('active'));
    e.target.parentElement.classList.add('active');
    document.getElementById('pagarBtn').href =
      '{{ route('pagos.confirmar', $pago->pago_id) }}?metodo=' + e.target.value;
  });
});
</script>
</body>
</html>
