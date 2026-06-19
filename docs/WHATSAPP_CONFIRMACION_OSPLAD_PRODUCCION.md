# Confirmación de retiro por WhatsApp (OSPLAD) — Puesta en producción

Guía paso a paso para dejar funcionando el flujo de **aviso + confirmación por WhatsApp**
en los *pendientes* de OSPLAD:

1. La farmacia presiona el botón de WhatsApp en **PENDIENTES** → se le envía al afiliado
   la lista de su medicación pendiente con las opciones **1‑Confirmar / 2‑Cancelar**.
2. El afiliado responde **1** o **2** en WhatsApp.
3. Twilio reenvía esa respuesta al **webhook** de SISCON, que actualiza
   `drogueria.osplad_consumos`:
   - `1` → estado **`CONF`** (Confirmado por afiliado) + `fecha_validacion = ahora`.
   - `2` → estado **`ANUL`**.

> El **envío** ya funciona hoy (microservicio Go + Twilio). Lo que requiere configuración
> es la **recepción** de la respuesta del afiliado: hay que decirle a Twilio a qué URL
> mandar los mensajes entrantes. Eso es lo que cubre esta guía.

---

## 0. Arquitectura (resumen)

```
[Farmacia]
   │  click "Enviar WhatsApp"  (POST /admin/osplad/confirmar-whatsapp)
   ▼
[SISCON Laravel] ──POST /general-message──► [Microservicio Go] ──API──► [Twilio] ──► WhatsApp del afiliado
                                                                                          │
                                                                                          │ el afiliado responde "1"/"2"
                                                                                          ▼
[SISCON Laravel] ◄──── POST /api/osplad/whatsapp/inbound?token=… ◄──── [Twilio inbound webhook]
   │  estado_pedido = CONF + fecha_validacion   (ó ANUL)
   ▼
[drogueria.osplad_consumos]
```

Datos reales de la cuenta (verificados vía API de Twilio):

| Elemento | Valor |
|---|---|
| Número WhatsApp (sender / `From`) | `whatsapp:+5493804230241` |
| Messaging Service "SISCON Mensajería" | `MGf18c4a8e3bda912196d46d036c65d143` (hoy **sin** senders adjuntos) |
| Template usado para el aviso | `general_message` (es_AR, 4 variables) |
| Microservicio de envío (Go) | `send-notification-twilio` (`:8081`, endpoint `/general-message`) |

---

## 0.bis Valores concretos de PRODUCCIÓN

| Dato | Valor |
|---|---|
| Dominio de producción | `sisconsalud.com` → `147.182.161.199` |
| Esquema | **HTTPS** (Let's Encrypt válido); HTTP (80) redirige 301 a HTTPS |
| **URL del webhook** | `https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>` |
| Token | el que definas en el `.env` de **producción** (no el de tu entorno local) |

> ✅ Verificado: `sisconsalud.com` resuelve a `147.182.161.199`, responde por HTTPS con
> certificado Let's Encrypt vigente y redirige HTTP→HTTPS. Al usar HTTPS, el token y el
> contenido del mensaje viajan **cifrados**. El path del webhook responde 404 hasta que se
> despliegue el código mergeado (después, sin token responderá 401).

### Auto‑respuesta al afiliado (TwiML)

El webhook responde con **TwiML**, por lo que Twilio le reenvía automáticamente un mensaje
al afiliado (sesión dentro de la ventana de 24 h, sin plantilla):

| Respuesta del afiliado | Acción en BD | Auto‑respuesta |
|---|---|---|
| `1` (o "confirmar/sí/ok") | estado `CONF` + `fecha_validacion` | ✅ ¡Confirmamos tu retiro! Tu medicación quedará reservada en la farmacia. ¡Gracias! |
| `2` (o "cancelar/no") | estado `ANUL` | Tu pedido fue cancelado. Si fue un error, respondé *1* para confirmar el retiro. |
| Texto no reconocido | (sin cambio) | No entendimos tu respuesta. Respondé *1* para CONFIRMAR o *2* para CANCELAR. |
| Sin pedidos esperando | (sin cambio) | *(sin auto‑respuesta — TwiML vacío)* |

---

## 1. Requisitos previos

- **SISCON accesible públicamente por HTTPS** (Twilio solo llama a URLs públicas y válidas).
  Ej.: `https://siscon.tudominio.com`. Si hoy corre detrás de Apache en el contenedor,
  exponelo con un dominio + certificado (Let's Encrypt / reverse proxy).
- El microservicio Go (`send-notification-twilio`) corriendo y alcanzable por Laravel
  (`SEND_NOTIFICATION_WSP_URL`).
- Acceso al **Twilio Console** del proyecto dueño del número `+5493804230241`.

---

## 2. Variables de entorno en SISCON (`siscon2_prod/.env`)

```dotenv
# Envío (microservicio Go)
SEND_NOTIFICATION_WSP_URL=http://notifications:8081   # en Docker Compose
SEND_NOTIFICATION_WSP_FLAG=true                       # true = envía WhatsApp reales

# Webhook entrante (respuesta del afiliado). Token largo y secreto.
OSPLAD_WSP_WEBHOOK_TOKEN=tkn_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Generar un token seguro:

```bash
echo "OSPLAD_WSP_WEBHOOK_TOKEN=tkn_$(head -c16 /dev/urandom | od -An -tx1 | tr -d ' \n')"
```

> En Docker Compose, `OSPLAD_WSP_WEBHOOK_TOKEN` se toma del `.env` (no está pisado en el
> bloque `environment:`). Tras editarlo, recrear el contenedor: `docker compose up -d app`.

Aplicar la migración de columnas (una sola vez):

```bash
docker compose exec app php artisan migrate \
  --path=/database/migrations/2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos.php --force
```

---

## 3. URL del webhook entrante

```
# Producción:
https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<OSPLAD_WSP_WEBHOOK_TOKEN>

Método: POST
```

- Twilio envía los campos `From` (ej. `whatsapp:+549...`) y `Body` (el texto del afiliado).
- El token va en el **query string** (Twilio no permite headers personalizados); el webhook
  también acepta el header `X-Webhook-Token` si en el futuro se llama desde el microservicio.
- Sin token o con token inválido el webhook responde **401** (no procesa nada).

---

## 4. Configurar el inbound en Twilio

> ✅ **Verificado en pruebas (19/06/2026):** el WhatsApp sender `+5493804230241`
> **pertenece al Messaging Service** `Default Messaging Service for Conversations`
> (SID `MG8daedd0bf109a956ef4bb73a08762cae`). Por eso el inbound se enruta por **ese
> Messaging Service**, NO por el webhook a nivel del sender. Configurar el webhook del
> sender directamente **no tiene efecto** mientras el número esté en un Messaging Service
> con `use_inbound_webhook_on_number = false`.

### Opción A — Inbound en el Messaging Service (la que aplica a esta cuenta)

**Por API (rápido, reemplazá `<TOKEN>`):**
```bash
SK=...; SEC=...   # API Key TWILIO_SID / TWILIO_SECRET
curl -s -u "$SK:$SEC" -X POST \
  "https://messaging.twilio.com/v1/Services/MG8daedd0bf109a956ef4bb73a08762cae" \
  --data-urlencode "InboundRequestUrl=https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>" \
  --data-urlencode "InboundMethod=POST" \
  --data-urlencode "UseInboundWebhookOnNumber=false"
```

**Por Console:**
1. **Messaging → Services → "Default Messaging Service for Conversations"**.
2. **Integration** → "Send a webhook":
   - **Request URL (When a message comes in)**:
     `https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>` — **POST**.
   - **Use inbound webhook on number**: *desactivado*.
3. Guardar.

> ⚠️ Es el Messaging Service por defecto de Conversations. Si en el futuro usás Twilio
> Conversations con este número, considerá mover el WhatsApp sender a un Messaging Service
> dedicado (p. ej. "SISCON Mensajería", `MGf18c4a8e3bda912196d46d036c65d143`) y configurar
> el inbound ahí, para no mezclar ambos usos.

### Opción B — Mover el sender a un Messaging Service dedicado (opcional, más prolijo)

1. Console → **Messaging → Services → SISCON Mensajería** → **Sender Pool** → agregar
   el WhatsApp sender `+5493804230241`.
2. **Integration** → "Send a webhook":
   - **Request URL**: `https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>` (POST).
   - Desactivar **"Use inbound webhook on number"**.
3. Guardar. (Equivalente por API: `POST /v1/Services/MGf18c4a8e3bda912196d46d036c65d143`
   con `InboundRequestUrl`, `InboundMethod=POST`, `UseInboundWebhookOnNumber=false`.)

> **Regla general:** si el número de WhatsApp está en un Messaging Service, el inbound se
> configura en ese **service** (con `use_inbound_webhook_on_number=false`). Solo si el número
> **no** pertenece a ningún service, se usa el webhook a nivel del sender/número.

### Si usás el **Sandbox** de WhatsApp (entorno de prueba)

Si el `From` fuese el sandbox (`whatsapp:+14155238886`):
Console → **Messaging → Try it out → Send a WhatsApp message → Sandbox settings** →
**"When a message comes in"** = la misma URL del webhook (POST). En sandbox cada
destinatario debe unirse enviando `join <palabra>` primero.

---

## 5. Plantilla (template) del aviso

El aviso de confirmación usa un template **dedicado**: **`osplad_confirmacion_retiro`**
(idioma `es_AR`, SID `HX8961d040d712b13d987ba0ffd7a75087`), enviado por el endpoint
**`/osplad-confirmacion`** del microservicio Go. Los saltos de línea y la aclaración de
equivalentes están en el **cuerpo fijo del template**; SISCON solo envía 2 variables
(`App\Services\TwilioSender::sendObraSocialConfirmacion`):

| Var | Contenido |
|---|---|
| `1` | Nombre del afiliado (ej. `PAEZ CARLOS CESAR`) |
| `2` | Listado de medicación en bullets en línea (ej. `• ISLOTIN 1000… • INSULINA BASAGLAR…`) |

Cuerpo del template (los `\n` viven acá, no en las variables):
```
Buenos días, nos contactamos desde Global Médica.

Queremos informarle que tenemos la siguiente medicación para Sr/a {{1}}:

{{2}}

Aclaración: los medicamentos del listado podrán entregarse en su presentación original o por sus equivalentes.

Responda *1* para CONFIRMAR el retiro o *2* para CANCELAR la operación.

Desde ya muchas gracias.

Global Médica S.A.
```

> **Por qué un template dedicado:** WhatsApp **no permite saltos de línea** (ni tabs ni 4+
> espacios) dentro de las variables de un template → provocan **error 21656** (verificado).
> Para tener el layout multipárrafo, los saltos van en el **cuerpo fijo**. La **lista de
> medicación** sí queda en una sola variable (`{{2}}`), por lo que los ítems van como
> **bullets en línea** (no un ítem por línea): un listado de longitud variable no puede
> partirse en líneas dentro de una única variable.
>
> Los otros mensajes del flujo (procesando / entregado) siguen usando el template
> `general_message` vía `/general-message`.

> ⚠️ El template `osplad_confirmacion_retiro` debe estar **aprobado por Meta** (estado
> `approved`) antes de poder enviarse. Verificar:
> ```bash
> curl -s -u "$TWILIO_SID:$TWILIO_SECRET" \
>   "https://content.twilio.com/v1/Content/HX8961d040d712b13d987ba0ffd7a75087/ApprovalRequests" \
>   | python3 -c 'import sys,json;print(json.load(sys.stdin).get("whatsapp",{}).get("status"))'
> ```

---

## 6. Probar end‑to‑end

### 6.1 Webhook (sin enviar WhatsApp real)

```bash
# Debe responder 401 sin token:
curl -s -o /dev/null -w "%{http_code}\n" -X POST \
  "https://sisconsalud.com/api/osplad/whatsapp/inbound" -d '{"phone":"+549...","body":"1"}'

# Simular respuesta "1" del afiliado (con token):
curl -s -X POST "https://sisconsalud.com/api/osplad/whatsapp/inbound?token=<TOKEN>" \
  --data-urlencode "From=whatsapp:+5493804230241" \
  --data-urlencode "Body=1"
# Respuesta esperada (TwiML que Twilio reenvía al afiliado):
# <?xml version="1.0" encoding="UTF-8"?><Response><Message>✅ ¡Confirmamos tu retiro! ...</Message></Response>
```

Para que se actualice algún ítem, el afiliado de ese teléfono debe tener ítems **PEND** a
los que ya se les envió el aviso (es decir, `notif_confirmacion_at` cargado por el botón).
Si no hay ítems esperando, el webhook devuelve un TwiML vacío (sin auto‑respuesta).

### 6.2 Flujo real

1. En **/admin/osplad/pendientes**, presionar el botón de WhatsApp de un afiliado de prueba
   (con tu propio número cargado en `telefono`).
2. Verificar que llegue el WhatsApp con la lista y las opciones 1/2.
3. Responder **1** desde WhatsApp.
4. Confirmar en la base / en la grilla que el/los ítems pasaron a **CONF** y tienen
   `fecha_validacion`.

### 6.3 Ver estado de mensajes en Twilio (diagnóstico)

```bash
curl -s -u "$TWILIO_SID:$TWILIO_SECRET" \
  "https://api.twilio.com/2010-04-01/Accounts/<ACCOUNT_SID>/Messages.json?PageSize=10"
# Revisar status (queued/sent/delivered/read/failed) y error_code de cada mensaje.
```

---

## 7. Seguridad

- El webhook es **público**; su única protección es `OSPLAD_WSP_WEBHOOK_TOKEN`. Debe ser
  largo, secreto y viajar siempre por **HTTPS**.
- **Recomendado (endurecer):** validar además la **firma de Twilio** (`X-Twilio-Signature`)
  con el Auth Token, para asegurar que el request proviene de Twilio. Hoy NO está
  implementado; el token cumple esa función de forma más simple. Si se requiere, se agrega
  en `FlowController::webhookRespuesta()`.
- No commitear `.env`, tokens, API Keys ni Auth Tokens a un repo.

---

## 8. Matching del teléfono (cómo se asocia la respuesta)

La respuesta entrante se asocia a los consumos por **teléfono**, tolerando formatos
distintos: se comparan los **últimos 8 dígitos** tras quitar símbolos, prefijo país (`54`),
el `9` de WhatsApp, ceros iniciales y el `15`
(`ObraSocialConsumo::digitosComparables()`).

Solo se actualizan los ítems que están **PEND** y que ya recibieron el aviso
(`notif_confirmacion_at` no nulo), es decir, los que están esperando confirmación.

---

## 9. Troubleshooting

| Síntoma | Causa probable | Solución |
|---|---|---|
| Webhook responde `401` | Falta `OSPLAD_WSP_WEBHOOK_TOKEN` o token incorrecto en la URL | Setear el env y usar `?token=` correcto; recrear contenedor |
| `matched: 0` | El teléfono no coincide o no hay ítems PEND con aviso enviado | Verificar `telefono` del afiliado y que se haya presionado el botón (carga `notif_confirmacion_at`) |
| Mensaje `failed` err **21656** | Variable de plantilla con saltos de línea | Ya saneado; si editaron el template, revisar placeholders |
| Mensaje `failed` err **63016/63003** | Fuera de ventana de 24 h / template no aprobado | Usar template aprobado (el aviso usa uno); el afiliado debe tener conversación válida |
| No llega nada a SISCON | Webhook no configurado en Twilio o URL no pública | Configurar Sección 4; verificar HTTPS y que la URL sea accesible desde internet |
| Llega pero no actualiza | Token OK pero sin candidatos | Ver `storage/logs/laravel.log` (el webhook loguea `OSPLAD webhook: …`) |

Logs útiles:

```bash
docker compose exec app tail -f storage/logs/laravel.log     # eventos del webhook
docker compose logs -f notifications                          # microservicio Go / Twilio
```

---

## 10. Checklist de producción

- [ ] SISCON publicado por HTTPS con dominio válido.
- [ ] `OSPLAD_WSP_WEBHOOK_TOKEN` seteado en `.env` y contenedor recreado.
- [ ] Migración `2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos` aplicada.
- [ ] `SEND_NOTIFICATION_WSP_FLAG=true` y microservicio Go operativo.
- [ ] Webhook entrante configurado en Twilio (Sección 4) apuntando a la URL con token.
- [ ] Prueba 6.1 (webhook) y 6.2 (flujo real con un número propio) OK.
- [ ] (Opcional) Validación de firma de Twilio habilitada.
