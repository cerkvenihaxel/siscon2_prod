<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ya procesado — Global Médica</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:420px;width:90%;padding:40px;text-align:center}
h1{font-size:22px;margin:16px 0 8px;color:#333}
p{color:#666;font-size:14px}
.badge{display:inline-block;background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;margin-top:16px}
</style>
</head>
<body>
<div class="card">
  <div style="font-size:56px">✅</div>
  <h1>Este pago ya fue procesado</h1>
  <p>Ref: {{ $pago->pedido_id }}</p>
  <span class="badge">{{ $pago->estado }}</span>
</div>
</body>
</html>
