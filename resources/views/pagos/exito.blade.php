<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pago Exitoso — Global Médica</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:420px;width:90%;padding:40px;text-align:center}
.check{font-size:64px;margin-bottom:16px}
h1{color:#2ecc71;margin-bottom:8px}
.info{background:#f8f9fa;border-radius:8px;padding:16px;margin:20px 0;text-align:left;font-size:14px;line-height:1.8}
.info b{color:#333}
.msg{color:#666;font-size:14px;margin-top:16px}
</style>
</head>
<body>
<div class="card">
  <div class="check">✅</div>
  <h1>Pago Aprobado</h1>
  <p style="color:#666">Tu compra fue procesada con éxito</p>
  <div class="info">
    <b>Referencia:</b> {{ $pago->pedido_id }}<br>
    <b>Monto:</b> ${{ number_format((float)$pago->total, 2) }}<br>
    <b>Método:</b> {{ ucfirst($pago->metodo ?? 'tarjeta') }}<br>
    <b>Fecha:</b> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
  </div>
  <p class="msg">Podés cerrar esta ventana. Recibirás la confirmación por Telegram.</p>
</div>
</body>
</html>
