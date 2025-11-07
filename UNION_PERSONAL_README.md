# Sistema de Integración SOAP - Unión Personal

## 🎯 Descripción

Sistema completo de integración con los servicios SOAP de Unión Personal (Protocolo CA_V20) que permite:

- ✅ **ELG (Elegibilidad):** Verificar si afiliado y prestaciones son elegibles
- ✅ **AP (Autorizar Prestación):** Autorizar medicamentos y prestaciones online
- ✅ **ATR (Anulación):** Anular transacciones previamente autorizadas
- 🌟 **Autocreación de Afiliados:** Los afiliados se crean automáticamente en `afiliados_convenio_up` al ejecutar ELG

---

## 📋 Características Principales

### 🌟 Autocreación de Afiliados

Cuando se ejecuta una operación **ELG (Elegibilidad)** exitosa, el sistema automáticamente:
1. Extrae los datos del afiliado de la respuesta SOAP
2. Crea o actualiza el registro en la tabla `afiliados_convenio_up`
3. Guarda información completa: nombre, plan, ubicación, credencial, etc.

**Esto significa que no necesitas crear afiliados manualmente** - se autocompletan desde Unión Personal.

### 📊 Tablas Creadas

1. **afiliados_convenio_up** - Afiliados autocreados desde SOAP
2. **up_consumos** - Histórico de consumos (importado desde CSV)
3. **up_solicitudes** - Nuevas solicitudes de autorización
4. **up_solicitud_items** - Prestaciones de cada solicitud
5. **up_transacciones_soap** - Log completo de todas las transacciones SOAP

---

## 🚀 Instalación

### 1. Configurar Variables de Entorno

Agregar al archivo `.env`:

```bash
# SOAP - Unión Personal (TEST)
UP_AMBIENTE=test
SOAP_UP_ENDPOINT=http://181.13.241.19:7002/cawsTest/Servicios
SOAP_UP_WSDL=http://181.13.241.19:7002/cawsTest/Servicios?wsdl
SOAP_UP_EMISOR_ID=INTEGRACION-UP
SOAP_UP_TERMINAL=01
SOAP_UP_APP_NAME=Global
SOAP_UP_PRESTADOR_ID=8888
SOAP_UP_USER_ID=8888
SOAP_UP_USER_PASS=7777
SOAP_UP_LOG_ENABLED=true
SOAP_UP_DB_LOG_ENABLED=true
UP_AUTOCREAR_AFILIADOS=true

# Para PRODUCCIÓN cambiar:
# UP_AMBIENTE=produccion
# SOAP_UP_ENDPOINT=https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios
# SOAP_UP_WSDL=https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios?wsdl
```

### 2. Ejecutar Migraciones

```bash
php artisan migrate
```

Esto creará las 5 tablas necesarias.

### 3. Importar Consumos Históricos (Opcional)

```bash
php artisan db:seed --class=UpConsumosSeeder
```

Esto importará los datos del CSV `documentacion up/consumos up.csv` a la tabla `up_consumos`.

### 4. Limpiar Caché

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📖 Uso del Sistema

### Desde la Interfaz Admin

#### 1. Ver Consumos Históricos
- URL: `/admin/up_consumos`
- Vista de solo lectura del histórico importado del CSV

#### 2. Gestionar Solicitudes
- URL: `/admin/up_solicitudes`
- Crear nuevas solicitudes
- Flujo: **Borrador → ELG → AP → ATR**

**Pasos:**
1. Click en **"Agregar Nueva Solicitud"**
2. Ingresar código de afiliado (ej: `54715500`)
3. Ingresar TOKEN (`9999` para test) o Plan/VerCred
4. Agregar prestaciones (tipo, código, cantidad)
5. Guardar como borrador
6. Click en **"Verificar Elegibilidad (ELG)"** 🌟 *Autocrea el afiliado*
7. Si OK, click en **"Aprobar Prestación (AP)"**
8. Si necesario, click en **"Anular Transacción (ATR)"**

---

### Desde la API REST

Base URL: `/api/union-personal/`

#### Test de Conexión

```bash
GET /api/union-personal/test
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Bienvenido SISCON2_TEST",
  "data": { ... }
}
```

---

#### Verificar Elegibilidad (ELG) 🌟

```bash
POST /api/union-personal/elegibilidad
Content-Type: application/json

{
  "afiliado_codigo": "54715500",
  "token": "9999",
  "prestaciones": [
    {
      "tipo": "P",
      "id": "1420107",
      "cant": 1
    }
  ]
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "AFILIACION VALIDA**SOLO VERIFICACION - NO VALIDO PARA FACTURAR**",
  "data": {
    "IDTRAN": "1114XXXXXX",
    "STATUS": "OK",
    "AFICODIGO": "54715500",
    "AFIAPE": "Prueba",
    "AFINOM": "Afiliado Prueba",
    "AFIPLAN": "150",
    "AFIPLANNOM": "Accord"
  },
  "idtran": "1114XXXXXX",
  "afiliado_creado": true,
  "afiliado_nombre": "Prueba, Afiliado Prueba"
}
```

🌟 **El afiliado se autocreó en `afiliados_convenio_up`**

---

#### Autorizar Prestación (AP)

```bash
POST /api/union-personal/autorizar-prestacion
Content-Type: application/json

{
  "afiliado_codigo": "54715500",
  "token": "9999",
  "contexto_tipo": "A",
  "prestaciones": [
    {
      "tipo": "P",
      "id": "1420107",
      "cant": 1
    }
  ]
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "AP APROBADA",
  "data": {
    "IDTRAN": "1114YYYYYY",
    "IDAUT": "117ZZZZZZ",
    "STATUS": "OK",
    "PR": [
      {
        "TIPO": "P",
        "ID": "1420107",
        "DESCRIPCION": "CONSULTA ESPECIALIZADA",
        "STATUS": "OK",
        "IMPTOTAL": "703.99"
      }
    ]
  },
  "idtran": "1114YYYYYY",
  "idaut": "117ZZZZZZ"
}
```

---

#### Anular Transacción (ATR)

```bash
POST /api/union-personal/anular-transaccion
Content-Type: application/json

{
  "afiliado_codigo": "54715500",
  "plan": "150",
  "vercred": "45",
  "tipoidanul": "IDTRAN",
  "idanul": "1114YYYYYY",
  "motivo": "Anulación de prueba"
}
```

---

#### Flujo Completo (ELG + AP)

```bash
POST /api/union-personal/flujo-completo
Content-Type: application/json

{
  "afiliado_codigo": "54715500",
  "prestador_id": "8888",
  "token": "9999",
  "prestaciones": [
    {
      "tipo": "M",
      "id": "43472",
      "cant": 1
    }
  ]
}
```

Este endpoint ejecuta automáticamente:
1. ✅ Crea solicitud en `up_solicitudes`
2. ✅ Ejecuta ELG (autocrea afiliado 🌟)
3. ✅ Ejecuta AP si ELG fue exitoso
4. ✅ Actualiza todos los estados

---

## 📁 Estructura de Archivos

```
app/
├── Models/
│   ├── AfiliadoConvenioUp.php       🌟 Modelo con método createOrUpdateFromSoap()
│   ├── UpConsumo.php
│   ├── UpSolicitud.php
│   ├── UpSolicitudItem.php
│   └── UpTransaccionSoap.php
├── Services/
│   └── UnionPersonalSoapService.php  🌟 Servicio SOAP con autocreación
├── Http/Controllers/
│   ├── AdminUpConsumosController.php
│   ├── AdminUpSolicitudesController.php
│   └── API/
│       └── UnionPersonalAPIController.php

config/
└── union_personal.php                 Configuración del sistema

database/
├── migrations/
│   ├── 2025_10_28_000001_create_afiliados_convenio_up_table.php
│   ├── 2025_10_28_000002_create_up_consumos_table.php
│   ├── 2025_10_28_000003_create_up_solicitudes_table.php
│   ├── 2025_10_28_000004_create_up_solicitud_items_table.php
│   └── 2025_10_28_000005_create_up_transacciones_soap_table.php
└── seeders/
    └── UpConsumosSeeder.php

routes/
└── api.php                            Rutas API agregadas
```

---

## 🔍 Consultas SQL Útiles

### Ver afiliados autocreados

```sql
SELECT
    codigo_afiliado,
    apellido,
    nombre,
    plan,
    plan_nombre,
    ultima_verificacion_soap
FROM afiliados_convenio_up
ORDER BY ultima_verificacion_soap DESC;
```

### Ver transacciones SOAP recientes

```sql
SELECT
    transaction_type,
    afiliado_codigo,
    status,
    idtran,
    idaut,
    response_message,
    created_at
FROM up_transacciones_soap
ORDER BY created_at DESC
LIMIT 20;
```

### Ver solicitudes aprobadas

```sql
SELECT
    s.nro_solicitud,
    s.codigo_afiliado,
    a.apellido,
    a.nombre,
    s.estado,
    s.idaut,
    s.fecha_solicitud
FROM up_solicitudes s
LEFT JOIN afiliados_convenio_up a ON s.codigo_afiliado = a.codigo_afiliado
WHERE s.estado = 'aprobada'
ORDER BY s.fecha_solicitud DESC;
```

---

## 🧪 Testing

### Datos de Prueba (Ambiente TEST)

**Afiliado 1:**
- Código: `54715500`
- Plan: `150`
- VerCred: `45`
- TOKEN: `9999`

**Prestaciones de prueba:**
- `1420107` - CONSULTA ESPECIALIZADA
- `1420101` - CONSULTA MEDICA

### Secuencia de Prueba

1. **Test de conexión:**
   ```bash
   curl http://localhost/api/union-personal/test
   ```

2. **Elegibilidad (autocrea afiliado):**
   ```bash
   curl -X POST http://localhost/api/union-personal/elegibilidad \
     -H "Content-Type: application/json" \
     -d '{"afiliado_codigo":"54715500","token":"9999"}'
   ```

3. **Verificar que se creó el afiliado:**
   ```sql
   SELECT * FROM afiliados_convenio_up WHERE codigo_afiliado = '54715500';
   ```

4. **Aprobar prestación:**
   ```bash
   curl -X POST http://localhost/api/union-personal/autorizar-prestacion \
     -H "Content-Type: application/json" \
     -d '{
       "afiliado_codigo":"54715500",
       "token":"9999",
       "contexto_tipo":"A",
       "prestaciones":[{"tipo":"P","id":"1420107","cant":1}]
     }'
   ```

---

## 📝 Notas Importantes

1. 🌟 **Autocreación de afiliados:** Habilitada por defecto (`UP_AUTOCREAR_AFILIADOS=true`). Para deshabilitarla, cambiar a `false`.

2. **TOKEN de desarrollo:** El TOKEN `9999` es especial para desarrollo y **no expira** (solo válido en ambiente TEST).

3. **Logging:** Todas las transacciones se guardan en `up_transacciones_soap` con request/response completos.

4. **Estados de solicitud:**
   - `borrador` → puede editarse
   - `elegibilidad_ok` → puede aprobarse
   - `aprobada` → puede anularse
   - `anulada` → final

5. **Credenciales de TEST:**
   - User ID: `8888`
   - Password: `7777`
   - Prestador ID: `8888`

---

## 🐛 Troubleshooting

### Error: "SOAP extension not enabled"

```bash
# Verificar si está instalada
php -m | grep soap

# Si no aparece, habilitar en php.ini:
extension=soap
```

### Error: "No se pudo conectar al servicio"

1. Verificar endpoint en `.env`
2. Verificar conectividad:
   ```bash
   curl -I http://181.13.241.19:7002/cawsTest/Servicios?wsdl
   ```
3. Limpiar cache:
   ```bash
   php artisan config:clear
   ```

### El afiliado no se autocrea

1. Verificar `UP_AUTOCREAR_AFILIADOS=true` en `.env`
2. Verificar que ELG retorna `STATUS=OK`
3. Revisar logs: `storage/logs/laravel.log`

---

## 📚 Referencias

- **Documentación:** `documentacion up/preguntas up.md`
- **Manual de pruebas:** `documentacion up/manual up.md`
- **Protocolo:** CA_V20
- **Endpoints:**
  - TEST: http://181.13.241.19:7002/cawsTest/Servicios
  - PROD: https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios

---

## ✅ Checklist de Implementación

- [x] Migraciones creadas (5 tablas)
- [x] Modelos Eloquent con relaciones
- [x] Servicio SOAP con autocreación de afiliados 🌟
- [x] API Controller (6 endpoints)
- [x] Admin Controllers (CRUDBooster)
- [x] Seeder para CSV
- [x] Rutas API configuradas
- [x] Configuración completa
- [ ] Migraciones ejecutadas (`php artisan migrate`)
- [ ] CSV importado (`php artisan db:seed --class=UpConsumosSeeder`)
- [ ] Testing en ambiente TEST

---

**Versión:** 1.0
**Fecha:** Octubre 2025
**Proyecto:** SISCON2_PROD
**Desarrollado con:** ❤️ y Claude Code
