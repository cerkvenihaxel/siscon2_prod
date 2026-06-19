# Runbook de despliegue a PRODUCCIÓN — Confirmación de retiro por WhatsApp (OSPLAD)

Guía operativa, paso a paso, para activar la funcionalidad en producción el día del deploy.
Es la versión "checklist para ejecutar"; la explicación detallada y el troubleshooting están en
[`WHATSAPP_CONFIRMACION_OSPLAD_PRODUCCION.md`](./WHATSAPP_CONFIRMACION_OSPLAD_PRODUCCION.md).

> Todo lo de abajo fue **verificado en pruebas locales** el 2026‑06‑19 (envío del aviso,
> respuesta `1`/`2` del afiliado, cambio de estado a `CONF`/`ANUL`, `fecha_validacion` y
> auto‑reply TwiML). El único paso que cambia entre local y prod es la **URL del webhook**
> y dónde se configura el inbound (Messaging Service `MG8…`).

---

## Datos fijos del entorno

| Dato | Valor |
|---|---|
| Dominio prod | `https://sisconsalud.com` → `147.182.161.199` (HTTPS Let's Encrypt OK) |
| URL del webhook | `https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>` |
| WhatsApp sender | `whatsapp:+5493804230241` (sid `XE27d4b99d353ed03541f685c7957381d2`) |
| Messaging Service del sender | `Default Messaging Service for Conversations` → `MG8daedd0bf109a956ef4bb73a08762cae` |
| Template del aviso | `osplad_confirmacion_retiro` (`HX8961d040d712b13d987ba0ffd7a75087`, es_AR) — **debe estar `approved`** |
| Endpoint Go del aviso | `POST /osplad-confirmacion` (usa `TWILIO_CONTENT_SID_OSPLAD_CONFIRM`) |
| Twilio Account SID | `AC…` (en `sendNotificationTwilio/.env`) |
| API Key para administrar Twilio | `TWILIO_SID` (`SK…`) / `TWILIO_SECRET` (en `siscon2_prod/.env`) |
| Migración nueva | `2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos` |

---

## Paso 1 — Mergear y desplegar el código

```bash
# en el server de prod (147.182.161.199)
git checkout main && git pull           # o el merge de la rama feature/osplad_*
cd /ruta/al/stack   # donde está el docker-compose.yml
docker compose build app
```

Archivos que entran en el deploy (código de la feature):
- `app/Support/ObraSocial/EstadoConsumo.php` (estado `CONF`)
- `app/Models/Drogueria/ObraSocialConsumo.php` (scopes + matching de teléfono)
- `app/Services/TwilioSender.php` (`sendObraSocialConfirmacion`)
- `app/Http/Controllers/ObraSocial/FlowController.php` (`enviarConfirmacion`, `webhookRespuesta`, TwiML)
- `routes/web.php` (`POST /admin/osplad/confirmar-whatsapp`)
- `routes/api.php` (`POST /api/osplad/whatsapp/inbound`)
- `resources/views/obra_social/index.blade.php` (botón en PENDIENTES)
- `database/migrations/2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos.php`

> El `.env` **no** viaja en el merge (está gitignored). Configurarlo en el server (Paso 2).

---

## Paso 1.bis — Microservicio Go (`sendNotificationTwilio`)

El aviso de confirmación usa el endpoint **`/osplad-confirmacion`** (template dedicado).
Asegurar que el microservicio incluya ese endpoint y la variable de entorno.

En `sendNotificationTwilio/.env`:
```dotenv
TWILIO_CONTENT_SID_OSPLAD_CONFIRM=HX8961d040d712b13d987ba0ffd7a75087
```
Rebuild + recreate:
```bash
docker compose build notifications && docker compose up -d notifications
```

> El template `osplad_confirmacion_retiro` debe estar **`approved`** por Meta antes de enviar
> (ver Paso 5 / Sección 5 de la guía de referencia). Si no está aprobado, el envío falla.

---

## Paso 2 — Variables de entorno en el `.env` de PRODUCCIÓN

```dotenv
SEND_NOTIFICATION_WSP_URL=http://notifications:8081     # microservicio Go (red interna compose)
SEND_NOTIFICATION_WSP_FLAG=true                          # true = envía WhatsApp reales
OSPLAD_WSP_WEBHOOK_TOKEN=<token-largo-secreto>           # ver generación abajo
```

Generar token:
```bash
echo "OSPLAD_WSP_WEBHOOK_TOKEN=tkn_$(head -c16 /dev/urandom | od -An -tx1 | tr -d ' \n')"
```

> ⚠️ El flag `SEND_NOTIFICATION_WSP_FLAG` está **pisado** en el bloque `environment:` del
> `docker-compose.yml` con `${SEND_NOTIFICATION_WSP_FLAG:-false}`. Para que valga `true`,
> exportalo al levantar **o** editá el compose. Opciones:
> ```bash
> SEND_NOTIFICATION_WSP_FLAG=true docker compose up -d app
> ```
> (o poné el valor fijo en el compose y `docker compose up -d app`).

---

## Paso 3 — Migración (columnas de confirmación)

```bash
docker compose exec app php artisan migrate \
  --path=/database/migrations/2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos.php --force
```

Verificar:
```bash
docker compose exec -T app php artisan tinker --execute='
foreach(["notif_confirmacion_at","confirmacion_respuesta","confirmacion_respuesta_at"] as $c)
  echo $c.": ".(\Schema::connection("drogueria")->hasColumn("osplad_consumos",$c)?"OK":"FALTA").PHP_EOL;'
```

---

## Paso 4 — Configurar el inbound en Twilio (Messaging Service MG8)

El sender está en el Messaging Service `MG8…`, así que el inbound se setea **ahí**
(no en el webhook del sender).

```bash
# desde un equipo con acceso a las credenciales:
SK=$(grep -E '^TWILIO_SID='     siscon2_prod/.env | cut -d= -f2 | tr -d '\r')
SEC=$(grep -E '^TWILIO_SECRET=' siscon2_prod/.env | cut -d= -f2 | tr -d '\r')
TKN=$(grep -E '^OSPLAD_WSP_WEBHOOK_TOKEN=' siscon2_prod/.env | cut -d= -f2 | tr -d '\r')

curl -s -u "$SK:$SEC" -X POST \
  "https://messaging.twilio.com/v1/Services/MG8daedd0bf109a956ef4bb73a08762cae" \
  --data-urlencode "InboundRequestUrl=https://sisconsalud.com/api/osplad/whatsapp/inbound?token=$TKN" \
  --data-urlencode "InboundMethod=POST" \
  --data-urlencode "UseInboundWebhookOnNumber=false"
```

Equivalente por Console: **Messaging → Services → "Default Messaging Service for Conversations"
→ Integration → Send a webhook → Request URL** = la URL del webhook (POST), con
**Use inbound webhook on number = OFF**.

> Recomendación: si este número se usa también para Twilio Conversations, mové el sender a un
> Messaging Service dedicado (ej. `SISCON Mensajería` `MGf18c4a8e3bda912196d46d036c65d143`) y
> configurá el inbound ahí, para no mezclar usos.

---

## Paso 5 — Verificación post‑deploy

```bash
# 5.1 webhook público responde 401 sin token (ruta viva):
curl -s -o /dev/null -w "%{http_code}\n" -X POST https://sisconsalud.com/api/osplad/whatsapp/inbound
# esperado: 401

# 5.2 inbound del service quedó seteado:
curl -s -u "$SK:$SEC" "https://messaging.twilio.com/v1/Services/MG8daedd0bf109a956ef4bb73a08762cae" \
 | python3 -c 'import sys,json;d=json.load(sys.stdin);print("inbound:",d.get("inbound_request_url"))'
```

Prueba funcional (con un número propio de WhatsApp):
1. En `https://sisconsalud.com/admin/osplad/pendientes`, cargar tu número en una fila de prueba
   y tocar el botón verde de WhatsApp → te llega el aviso.
2. Responder **1** → la fila pasa a `CONF` + `fecha_validacion`; recibís "✅ ¡Confirmamos tu retiro!".
3. Responder **2** en otra prueba → la fila pasa a `ANUL`.

Diagnóstico:
```bash
docker compose exec -T app tail -n 30 storage/logs/laravel.log     # eventos "OSPLAD webhook"
# estado de mensajes en Twilio:
AC=$(grep -E '^TWILIO_ACCOUNT_SID=' sendNotificationTwilio/.env | cut -d= -f2 | tr -d '\r')
curl -s -u "$SK:$SEC" "https://api.twilio.com/2010-04-01/Accounts/$AC/Messages.json?PageSize=10"
```

---

## Paso 6 — Programar el cron (si no está)

El cambio a "En tránsito" (mensaje "procesando") ya usa el scheduler. Asegurar que el cron de
Laravel corra cada minuto en el server:
```bash
* * * * *  cd /ruta/al/proyecto && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1
```
(La confirmación de retiro NO depende del cron: el aviso lo dispara la farmacia con el botón y la
respuesta llega por el webhook.)

---

## Seguridad (recordatorio)

- El webhook es público; lo protege solo `OSPLAD_WSP_WEBHOOK_TOKEN`. Token largo, secreto y
  siempre por **HTTPS** (ya hay cert en `sisconsalud.com`).
- Opcional para endurecer: validar la firma `X-Twilio-Signature` en `webhookRespuesta()`.
- No commitear `.env`, tokens, API Keys ni Auth Tokens.

---

## Rollback

Si hay que desactivar la recepción de respuestas sin tocar el resto:
```bash
# vaciar el inbound del service (deja de reenviar respuestas al webhook):
curl -s -u "$SK:$SEC" -X POST \
  "https://messaging.twilio.com/v1/Services/MG8daedd0bf109a956ef4bb73a08762cae" \
  --data-urlencode "InboundRequestUrl="
```
Para frenar también el envío de avisos: `SEND_NOTIFICATION_WSP_FLAG=false` y recrear `app`.
La migración es aditiva (columnas nullable); no requiere rollback. Si aun así se quiere revertir:
`php artisan migrate:rollback --path=/database/migrations/2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos.php`.

---

## Checklist final

- [ ] Código mergeado y `docker compose build app` OK.
- [ ] Microservicio Go con endpoint `/osplad-confirmacion` y `TWILIO_CONTENT_SID_OSPLAD_CONFIRM` seteado; `docker compose build/up notifications` OK.
- [ ] Template `osplad_confirmacion_retiro` (`HX8961d040…`) en estado **`approved`** en Twilio/Meta.
- [ ] `.env` de prod: `SEND_NOTIFICATION_WSP_FLAG=true`, `OSPLAD_WSP_WEBHOOK_TOKEN` seteado, `SEND_NOTIFICATION_WSP_URL` OK.
- [ ] `docker compose up -d app` con el flag en true.
- [ ] Migración aplicada y columnas verificadas.
- [ ] Inbound del Messaging Service `MG8…` apuntando a `https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>` (POST, UseInboundWebhookOnNumber=false).
- [ ] `curl` al webhook sin token → 401.
- [ ] Prueba real con número propio: aviso → `1` → `CONF` + auto‑reply; `2` → `ANUL`.
- [ ] Cron `schedule:run` activo.
- [ ] Microservicio Go (`notifications`) operativo.
