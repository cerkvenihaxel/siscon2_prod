# Despliegue del módulo OSPLAD (flujo de obras sociales)

**Proyecto:** SISCON 2
**Módulo:** Flujo de consumos de obra social (OSPLAD) — `PENDIENTES → En tránsito → Entregas`
**Última actualización:** 2026-06-10

Este documento describe cómo desplegar en producción todo el módulo OSPLAD:
conexión a la base `drogueria`, migraciones, menús/privilegios, importación de
farmacias y notificaciones WhatsApp.

> Script automatizado: `docs/deploy_osplad.sh` ejecuta los pasos 4, 5 y 6.

---

## 1. Componentes nuevos

| Archivo | Descripción |
|---|---|
| `config/database.php` (conexión `drogueria`) | Ya existente; lee `DROGUERIA_DB_*` |
| `database/migrations/2026_06_10_000001_create_obras_sociales_table.php` | Catálogo `obras_sociales` (en `drogueria`) + seed OSPLAD |
| `database/migrations/2026_06_10_000002_add_os_id_to_osplad_consumos.php` | `os_id` en `osplad_consumos` + backfill + estado `PEND` |
| `database/migrations/2026_06_10_000003_add_id_cliente_to_cms_users.php` | `id_cliente` (farmacia) en `cms_users` |
| `database/migrations/2026_06_10_000004_add_notif_transito_at_to_osplad_consumos.php` | Marca de notificación "procesando" |
| `database/seeders/OspladMenusSeeder.php` | Privilegio "Farmacias OSPLAD" + menús CRUDBooster |
| `app/Support/ObraSocial/EstadoConsumo.php` | Estados PEND / EM / ENT + pills |
| `app/Models/Drogueria/ObraSocial.php`, `ObraSocialConsumo.php` | Modelos (conexión `drogueria`) |
| `app/Http/Controllers/ObraSocial/FlowController.php`, `OspladController.php` | Lógica del flujo + selector OS/farmacia (admin) |
| `app/Http/Middleware/ObraSocialMiddleware.php` (`obra.social`) | Control de acceso a las rutas del flujo |
| `app/Http/Middleware/RestrictObraSocialFarmacia.php` | Confina a "Farmacias OSPLAD" a su módulo (grupo `web`) |
| `app/Services/TwilioSender.php` | Mensajes "procesando" y "entregado" |
| `app/Console/Commands/OspladNotificarTransito.php` | Notificación automática "procesando" |
| `app/Console/Commands/OspladImportarFarmacias.php` | Alta de usuarios de farmacia desde CSV |
| `app/Exports/OspladEntregasExport.php` | Reporte Excel de entregas |
| `resources/views/obra_social/*`, `components/osplad_sidebar.blade.php` | Vistas (Materialize) + consentimiento PDF |
| `routes/web.php` (grupo `/admin/osplad`) | Rutas del flujo |

---

## 2. Requisitos previos

- PHP 7.4+, Composer, acceso a las bases `siscon` y `drogueria`.
- La tabla `drogueria.osplad_consumos` debe existir (origen de datos).
- El usuario de la conexión `drogueria` necesita permisos **ALTER/CREATE**
  (las migraciones crean la tabla `obras_sociales` y agregan columnas a `osplad_consumos`).

---

## 3. Variables de entorno (`.env`)

```dotenv
# Conexión a la base de droguería (donde vive osplad_consumos)
DROGUERIA_DB_HOST=190.136.183.125     # o el host real en prod
DROGUERIA_DB_PORT=3306
DROGUERIA_DB_DATABASE=drogueria
DROGUERIA_DB_USERNAME=admin
DROGUERIA_DB_PASSWORD=********

# Servicio de notificaciones WhatsApp (Go/Twilio)
SEND_NOTIFICATION_WSP_URL=http://IP_DEL_SERVICIO:8081/
SEND_NOTIFICATION_WSP_FLAG=true        # false para desactivar envíos (testing)
```

> Si cacheás config, recordá `php artisan config:clear` tras editar el `.env`.

---

## 4. Migraciones

> **Importante:** correr **solo las migraciones de OSPLAD** con `--path` (NO `migrate --force`
> a secas, que ejecutaría cualquier migración pendiente ajena al módulo).

```bash
php artisan migrate --force --path=database/migrations/2026_06_10_000001_create_obras_sociales_table.php
php artisan migrate --force --path=database/migrations/2026_06_10_000002_add_os_id_to_osplad_consumos.php
php artisan migrate --force --path=database/migrations/2026_06_10_000003_add_id_cliente_to_cms_users.php
php artisan migrate --force --path=database/migrations/2026_06_10_000004_add_notif_transito_at_to_osplad_consumos.php
```

(El script `docs/deploy_osplad.sh` ejecuta exactamente estas 4, en orden.)

Las migraciones son **idempotentes** (verifican existencia de tablas/columnas).
Backfillean los consumos existentes a OSPLAD (`os_id`) y estado `PEND`.

---

## 5. Menús y privilegio CRUDBooster

```bash
php artisan db:seed --class=Database\\Seeders\\OspladMenusSeeder --force
```

Crea (idempotente):
- Privilegio **"Farmacias OSPLAD"**.
- Menú **OSPLAD** → PENDIENTES / En tránsito / Entregas.
- Permisos para Super Admin, Administrador General y Farmacias OSPLAD.

---

## 6. Importación de farmacias (usuarios)

Subir el CSV de farmacias al servidor (ej. `storage/app/farmacias.csv` o junto al
proyecto) y ejecutar:

```bash
# 1) Simular (no escribe):
php artisan osplad:importar-farmacias /ruta/farmacias.csv --dry-run

# 2) Ejecutar (password genérica por defecto: farmaciaosplad2026):
php artisan osplad:importar-farmacias /ruta/farmacias.csv

# Password personalizada:
php artisan osplad:importar-farmacias /ruta/farmacias.csv --password=ClaveProd2026
```

- Crea `cms_users` con email + password genérica + privilegio "Farmacias OSPLAD" + `id_cliente`.
- **Idempotente**: si el email ya existe, actualiza datos pero no pisa la contraseña.
- Reporta filas omitidas (sin email o sin N° de cliente Zafiro).

> Columnas requeridas en el CSV: `N° CLIENTE ZAFIRO`, `FARMACIA`, `CORREO ELECTRONICO`.

---

## 7. Confinamiento de perfiles de farmacia

Los usuarios con privilegio **"Farmacias OSPLAD"** quedan restringidos a su módulo:

- **Al loguearse** aterrizan en `/admin/osplad/pendientes` (closure de `/admin` en `routes/web.php`).
- **Solo ven** los menús PENDIENTES / En tránsito / Entregas (permisos del privilegio + sidebar propio del módulo).
- **Cualquier otra ruta** del admin (`/admin/...` que no sea `/admin/osplad/*`, login o logout) los **redirige** de vuelta a su módulo.

Esto lo aplica el middleware `App\Http\Middleware\RestrictObraSocialFarmacia`, registrado
en el grupo `web` de `app/Http/Kernel.php`:

```php
'web' => [
    // ...
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \App\Http\Middleware\RestrictObraSocialFarmacia::class,
],
```

> No requiere comandos: se despliega con el código. Para sumar otra obra social,
> agregar su privilegio y ruta destino al array `$confinados` del middleware.

Verificación rápida (logueado como una farmacia del CSV):

| Ruta | Esperado |
|------|----------|
| `/admin` | 302 → `/admin/osplad/pendientes` |
| `/admin/osplad/pendientes` | 200 (solo sus consumos) |
| `/admin/consumos_up_v2`, `/admin/transaccion-ap`, etc. | 302 → `/admin/osplad/pendientes` |

---

## 8. Notificación automática "procesando" (cron)

El mensaje #1 ("su medicación está siendo procesada…") se dispara cuando un consumo
pasa a **En tránsito**. Como el cambio de estado lo hace un proceso externo (Zafiro),
se envía mediante un comando agendado.

Asegurá que el **scheduler de Laravel** esté activo (cron del sistema):

```cron
* * * * * cd /var/www/siscon2_prod && php artisan schedule:run >> /dev/null 2>&1
```

El `App\Console\Kernel` ya agenda `osplad:notificar-transito` cada 5 minutos.
También se puede ejecutar manualmente:

```bash
php artisan osplad:notificar-transito
```

---

## 9. Verificación

```bash
# Conexión a droguería y datos
php artisan tinker
>>> \App\Models\Drogueria\ObraSocial::porCodigo('OSPLAD');
>>> \App\Models\Drogueria\ObraSocialConsumo::deObraSocial(1)->count();

# Usuarios de farmacia creados
>>> DB::table('cms_users')->where('id_cms_privileges', DB::table('cms_privileges')->where('name','Farmacias OSPLAD')->value('id'))->count();
```

URLs:
- `/admin/osplad/pendientes` — Super Admin: ve todo + selectores OS/farmacia.
- Login de una farmacia (email del CSV / `farmaciaosplad2026`): ve solo sus consumos.

Checklist:
- [ ] `.env` con `DROGUERIA_DB_*` y `SEND_NOTIFICATION_WSP_*`.
- [ ] Migraciones OSPLAD (las 4 con `--path`) OK — sin tocar migraciones ajenas.
- [ ] Seeder de menús OK (aparece menú OSPLAD).
- [ ] Importación de farmacias OK (usuarios creados).
- [ ] Confinamiento de perfil verificado (la farmacia solo accede a `/admin/osplad/*`).
- [ ] Cron de `schedule:run` activo.
- [ ] Prueba de envío WhatsApp (con `SEND_NOTIFICATION_WSP_FLAG=true`).

---

## 10. Notas

- **Precios ocultos**: `osplad_consumos` no expone importes; las vistas/PDF no muestran costos.
- **Escalable**: para sumar otra obra social, agregar una fila en `obras_sociales`,
  etiquetar sus consumos con `os_id` y (opcional) crear su menú/rutas. El admin la verá
  en el selector y el título/PDF tomarán su nombre automáticamente.
- **Twilio (error 21656)**: las variables de plantilla no admiten saltos de línea/tabs;
  `TwilioSender::limpiarVariableTwilio()` ya lo maneja.

---

## 11. Confirmación de retiro por WhatsApp (estado CONF)

Permite que, desde **PENDIENTES**, se le envíe al afiliado por WhatsApp la lista de su
medicación pendiente y éste confirme (1) o cancele (2). La confirmación marca el consumo
como **CONF** (Confirmado por afiliado) y completa `fecha_validacion`.

### Componentes
- **Estado** `CONF` en `App\Support\ObraSocial\EstadoConsumo` (etapa *pendientes*, badge azul).
- **Migración** `2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos`:
  agrega `notif_confirmacion_at`, `confirmacion_respuesta`, `confirmacion_respuesta_at`.
  Ejecutar (igual que las otras, contra la conexión `drogueria`):
  ```bash
  php artisan migrate --path=/database/migrations/2026_06_19_000001_add_confirmacion_columns_to_osplad_consumos.php
  ```
- **Envío**: `TwilioSender::sendObraSocialConfirmacion()` vía `/general-message` del gateway.
- **Acción admin** (protegida por login/perfil): `POST /admin/osplad/confirmar-whatsapp`
  con body `{ "dni": "<dni>" }` — agrupa toda la medicación pendiente del afiliado.
- **Webhook entrante** (público, sin sesión): `POST /api/osplad/whatsapp/inbound`.

### Configuración requerida
1. En `.env` definir un token compartido para el webhook:
   ```
   OSPLAD_WSP_WEBHOOK_TOKEN=<token-largo-aleatorio>
   ```
2. Configurar el **gateway de WhatsApp/Twilio** (`SEND_NOTIFICATION_WSP_URL`) para que,
   al recibir una respuesta del afiliado, haga `POST` a:
   ```
   POST https://<host-siscon>/api/osplad/whatsapp/inbound
   Header: X-Webhook-Token: <token>           (o ?token=<token>)
   Body  : From=whatsapp:+549XXXXXXXXXX & Body=1   (formato Twilio)
           ó  { "phone": "+549...", "body": "1" } (JSON)
   ```
   - `1` (o "confirmar"/"sí") → estado **CONF** + `fecha_validacion = ahora`.
   - `2` (o "cancelar"/"no")  → estado **ANUL**.
   - El matching afiliado↔respuesta es por teléfono (últimos 8 dígitos, tolerante a formatos)
     entre los consumos PEND que ya recibieron el pedido de confirmación.

> Seguridad: el webhook es un endpoint **público**. Su única protección es el token
> compartido (`OSPLAD_WSP_WEBHOOK_TOKEN`), por lo que debe ser largo y secreto, y servirse
> siempre por HTTPS. Sin el token configurado, el webhook responde 401.

