# Guía de Despliegue en Producción

**Proyecto:** SISCON + FarmanorBot (whatsapp_agent)  
**Stack:** PHP 8.x / Laravel 9, MySQL 8, Python 3.10+, Laravel Herd  
**Última actualización:** 2026-04-24

---

## Índice

1. [Requisitos del servidor](#1-requisitos-del-servidor)
2. [Estructura de bases de datos](#2-estructura-de-bases-de-datos)
3. [Configurar MySQL](#3-configurar-mysql)
4. [Clonar y configurar SISCON](#4-clonar-y-configurar-siscon)
5. [Variables de entorno (.env)](#5-variables-de-entorno-env)
6. [Migraciones y datos iniciales](#6-migraciones-y-datos-iniciales)
7. [Configurar el whatsapp_agent](#7-configurar-el-whatsapp_agent)
8. [Configurar WhatsApp / Telegram](#8-configurar-whatsapp--telegram)
9. [Supervisor (procesos en background)](#9-supervisor-procesos-en-background)
10. [Permisos de acceso en SISCON](#10-permisos-de-acceso-en-siscon)
11. [Nginx / Web server](#11-nginx--web-server)
12. [Verificación final](#12-verificación-final)

---

## 1. Requisitos del servidor

| Componente | Versión mínima | Notas |
|------------|---------------|-------|
| PHP        | 8.1           | Extensiones: pdo_mysql, mbstring, json, openssl, curl, xml |
| MySQL      | 8.0           | |
| Python     | 3.10          | Para el agente WhatsApp |
| Composer   | 2.x           | |
| Node.js    | 16+           | Solo para compilar assets si se modifican |
| Git        | 2.x           | |

**Python — paquetes requeridos:**
```bash
pip install mysql-connector-python requests python-telegram-bot pymysql
```

---

## 2. Estructura de bases de datos

El sistema usa **tres bases de datos**:

| BD | Host | Usuario | Propósito |
|----|------|---------|-----------|
| `siscon` | localhost | `heap` (producción) / `root` (dev) | BD principal: artículos, pedidos, usuarios CMS |
| `apos` | localhost | `openclaw` | Validación de afiliados por DNI |
| `whatsapp_agent` | localhost | `openclaw` | Historial de pedidos y clientes del bot |

**BD remota (solo lectura):**

| BD | Host | Usuario | Propósito |
|----|------|---------|-----------|
| `drogueria` | 190.136.183.125 | `admin` | Tracking de envíos Global Médica |

---

## 3. Configurar MySQL

### 3.1 Crear BDs y datos de prueba

Ejecutar el script incluido en el repositorio:

```bash
mysql -u root -p < docs/whatsapp_agent_setup.sql
```

Este script:
- Crea la BD `whatsapp_agent` con sus cuatro tablas
- Crea la BD `apos` con la tabla `afiliados`
- Crea el usuario `openclaw` con los permisos correctos
- Inyecta datos de prueba realistas

### 3.2 Verificar conexión a BD remota

```bash
mysql -h 190.136.183.125 -u admin -p drogueria -e "SELECT COUNT(*) FROM envios_global_medica;"
# Password: amarok
```

Si no conecta, verificar que el firewall del servidor de producción permita salida al puerto 3306 de esa IP.

### 3.3 Usuario de producción para siscon

En producción usar el usuario `heap` (no root):

```bash
# Verificar que heap tenga permisos sobre wsp_pedidos y pagos
mysql -u root -p -e "
GRANT SELECT, INSERT, UPDATE ON siscon.wsp_pedidos TO 'heap'@'localhost';
GRANT SELECT, INSERT, UPDATE ON siscon.pagos        TO 'heap'@'localhost';
FLUSH PRIVILEGES;
"
```

---

## 4. Clonar y configurar SISCON

```bash
cd /var/www
git clone <repo-url> siscon2_prod
cd siscon2_prod

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 5. Variables de entorno (.env)

Copiar `.env.example` y completar:

```bash
cp .env.example .env
php artisan key:generate
```

**Valores clave para producción:**

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sisconsalud.com

# BD principal (local)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=siscon
DB_USERNAME=heap
DB_PASSWORD=<password_produccion>

# BD Droguería Global Médica (remota — tracking de envíos)
DROGUERIA_DB_HOST=190.136.183.125
DROGUERIA_DB_PORT=3306
DROGUERIA_DB_DATABASE=drogueria
DROGUERIA_DB_USERNAME=admin
DROGUERIA_DB_PASSWORD=amarok

# WhatsApp Agent — ruta a los JSONs de pedidos
# Ajustar al path real donde corre el agente en producción
WSP_PEDIDOS_PATH=/home/openclaw/whatsapp_agent/casos/pedidos
```

---

## 6. Migraciones y datos iniciales

Ejecutar **en este orden**:

```bash
# Tabla para links de pago
php artisan migrate --path=database/migrations/2026_04_24_000001_create_pagos_table.php

# Tabla wsp_pedidos (pedidos del bot)
php artisan migrate --path=database/migrations/2026_04_24_000002_create_wsp_pedidos_table.php

# Módulo CMS + permisos por defecto
php artisan migrate --path=database/migrations/2026_04_24_000003_add_wsp_pedidos_module.php

# Importar pedidos JSON históricos a la BD
php artisan wsp:importar-pedidos
```

O correr todas las migraciones pendientes de una sola vez:

```bash
php artisan migrate
php artisan wsp:importar-pedidos
```

> **Nota:** `wsp:importar-pedidos` es idempotente — se puede correr múltiples veces sin duplicar datos.

---

## 7. Configurar el whatsapp_agent

### 7.1 Clonar o copiar el agente

```bash
cp -r /ruta/origen/whatsapp_agent /home/openclaw/whatsapp_agent
cd /home/openclaw/whatsapp_agent
```

### 7.2 Configurar el .env del agente

```bash
cp config/.env.example config/.env
nano config/.env
```

```dotenv
# BD SISCON (local — catálogo de medicamentos)
SISCON_HOST=localhost
SISCON_PORT=3306
SISCON_USER=openclaw
SISCON_PASS=Openclaw2026!
SISCON_DB=siscon
SISCON_TABLE=articulosZafiro

# BD APOS (local — validación afiliados)
APOS_HOST=localhost
APOS_PORT=3306
APOS_USER=openclaw
APOS_PASS=Openclaw2026!
APOS_DB=apos
APOS_TABLE=afiliados

# BD PANEL (remota — producción Droguería)
PANEL_HOST=190.136.183.125
PANEL_PORT=3306
PANEL_USER=admin
PANEL_PASS=amarok
PANEL_DB=drogueria

# Telegram Bot
TELEGRAM_BOT_TOKEN=<token_del_bot>
TELEGRAM_ADMIN_ID=<tu_telegram_id>

# Servidor tracking
TRACKING_PORT=8088
```

### 7.3 Verificar conexiones

```bash
cd /home/openclaw/whatsapp_agent
python3 test_siscon.py
```

Salida esperada:
```
✅ Conexión a siscon: OK
✅ articulosZafiro: 55,866 artículos
✅ Conexión a apos: OK
✅ afiliados: 153,218 registros
```

---

## 8. Configurar WhatsApp / Telegram

### Obtener token del bot (Telegram)

1. Hablar con `@BotFather` en Telegram
2. Crear o usar el bot existente `@FarmanorBot`
3. Copiar el token al `.env` del agente

### Obtener tu Telegram ID

1. Hablar con `@userinfobot`
2. Copiar el `Id` numérico
3. Pegarlo en `TELEGRAM_ADMIN_ID`

---

## 9. Supervisor (procesos en background)

Instalar supervisor para mantener el bot corriendo:

```bash
apt install supervisor   # Debian/Ubuntu
# o
yum install supervisor   # CentOS/RHEL
```

### Configuración del bot Telegram

Crear `/etc/supervisor/conf.d/farmanorbot.conf`:

```ini
[program:farmanorbot]
command=python3 /home/openclaw/whatsapp_agent/agentes/telegram_bot.py
directory=/home/openclaw/whatsapp_agent
autostart=true
autorestart=true
startretries=5
user=openclaw
stdout_logfile=/home/openclaw/whatsapp_agent/logs/supervisor-bot.log
stderr_logfile=/home/openclaw/whatsapp_agent/logs/supervisor-bot-err.log
environment=HOME="/home/openclaw"
```

### Configuración del servidor tracking

Crear `/etc/supervisor/conf.d/tracking.conf`:

```ini
[program:tracking-envios]
command=python3 /home/openclaw/whatsapp_agent/web_tracking/servidor_tracking.py
directory=/home/openclaw/whatsapp_agent/web_tracking
autostart=true
autorestart=true
startretries=5
user=openclaw
stdout_logfile=/home/openclaw/whatsapp_agent/logs/supervisor-tracking.log
stderr_logfile=/home/openclaw/whatsapp_agent/logs/supervisor-tracking-err.log
```

### Activar

```bash
supervisorctl reread
supervisorctl update
supervisorctl start farmanorbot
supervisorctl start tracking-envios
supervisorctl status
```

---

## 10. Permisos de acceso en SISCON

Las tres secciones nuevas tienen control de acceso por rol de CrudBooster.

### Rutas y quién puede acceder

| Ruta | Middleware | Acceso por defecto |
|------|-----------|-------------------|
| `/tracking` | `CBBackend` | Todos los usuarios autenticados |
| `/wsp/pedidos` | `CBBackend` + `wsp.pedidos` | Super Admin, Admin, Administrador General |
| `/pagar/{id}` | — (pública) | Cualquiera con el link |

### Dar acceso a un perfil nuevo en `/wsp/pedidos`

1. Ingresar al admin de SISCON como Super Administrator
2. Ir a **Privileges Roles** (`/admin/privileges_roles`)
3. Buscar el perfil deseado (ej: "Admin Bruno")
4. Marcar **Visible** en la fila **Pedidos WhatsApp**
5. Guardar — el cambio es inmediato en la próxima sesión del usuario

### Perfiles con acceso por defecto (vía migración)

| ID | Perfil |
|----|--------|
| 1  | Super Administrator |
| 3  | Admin |
| 50 | Administrador General |

---

## 11. Nginx / Web server

Configuración básica para el proyecto Laravel:

```nginx
server {
    listen 80;
    server_name sisconsalud.com www.sisconsalud.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name sisconsalud.com;

    root /var/www/siscon2_prod/public;
    index index.php;

    # SSL
    ssl_certificate     /etc/letsencrypt/live/sisconsalud.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/sisconsalud.com/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }

    # Proxy al servidor tracking Python (puerto 8088)
    location /tracking-envios/ {
        proxy_pass http://127.0.0.1:8088/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

> **Con Laravel Herd (desarrollo local):** Herd maneja el web server automáticamente, no se necesita configurar Nginx.

---

## 12. Verificación final

### Checklist post-deploy

```bash
# 1. PHP y dependencias
php artisan about

# 2. Conexiones a BD
php artisan tinker
>>> DB::connection('mysql')->getPdo();       // BD local
>>> DB::connection('drogueria')->getPdo();   // BD remota

# 3. Módulo CMS registrado
php artisan tinker
>>> DB::table('cms_moduls')->where('path','wsp_pedidos')->first();

# 4. Migración correcta
php artisan migrate:status | grep wsp

# 5. Importar pedidos históricos
php artisan wsp:importar-pedidos

# 6. Bot Telegram
supervisorctl status farmanorbot

# 7. Servidor tracking
curl http://localhost:8088/health
# Esperado: {"status":"ok","db":"ok"}
```

### URLs para verificar en el navegador

| URL | Qué verifica |
|-----|-------------|
| `https://sisconsalud.com/admin` | Login CrudBooster |
| `https://sisconsalud.com/tracking` | Tracking de envíos (requiere login) |
| `https://sisconsalud.com/wsp/pedidos` | Panel pedidos WhatsApp (requiere login + permiso) |
| `https://sisconsalud.com/pagar/TEST` | Gateway de pagos (404 esperado con ID inválido) |

---

## Resumen de componentes nuevos

| Archivo | Descripción |
|---------|-------------|
| `config/database.php` | Conexión `drogueria` agregada |
| `.env` | Variables `DROGUERIA_DB_*` y `WSP_PEDIDOS_PATH` |
| `app/Http/Controllers/TrackingController.php` | Tracking de envíos (BD remota) |
| `app/Http/Controllers/PagoController.php` | Gateway de pagos |
| `app/Http/Controllers/WspPedidosController.php` | Panel pedidos WhatsApp |
| `app/Http/Middleware/WspPedidosMiddleware.php` | Control de acceso por rol CMS |
| `app/Console/Commands/WspImportarPedidos.php` | Importador de JSONs a BD |
| `database/migrations/2026_04_24_000001_*` | Tabla `pagos` |
| `database/migrations/2026_04_24_000002_*` | Tabla `wsp_pedidos` |
| `database/migrations/2026_04_24_000003_*` | Módulo CMS + permisos |
| `resources/views/tracking/` | Vista de tracking |
| `resources/views/pagos/` | Vistas de pagos |
| `resources/views/wsp_pedidos/` | Panel de pedidos |
| `docs/whatsapp_agent_setup.sql` | Setup completo de BDs auxiliares |
