# Implementación de Menús ELG UP, AP UP y ATR UP

## 📋 Resumen

Se han implementado 3 nuevos menús en el sistema para gestionar las transacciones SOAP de Unión Personal de forma independiente:

1. **ELG UP** - Consultas de Elegibilidad
2. **AP UP** - Autorizaciones Previas
3. **ATR UP** - Anulaciones de Transacciones

## 🎯 Características

### 1. ELG UP (Elegibilidad)
- Verificación de elegibilidad de afiliados
- Consulta mediante TOKEN o Plan/VerCred
- Autocreación de afiliados en la base de datos
- Registro completo de todas las consultas
- Visualización del historial de elegibilidades

**Tabla:** `up_elegibilidad`

### 2. AP UP (Autorización Previa)
- Autorización de prestaciones médicas
- Soporte para múltiples prestaciones en una transacción
- Cálculo automático de importes
- Registro de ID de autorización (IDAUT)
- Visualización de prestaciones autorizadas

**Tabla:** `up_autorizacion_previa`

### 3. ATR UP (Anulaciones)
- Anulación de transacciones previas
- Soporte para 3 tipos de anulación:
  - Por IDTRAN (ID de Transacción)
  - Por MSGID (ID de Mensaje)
  - Por IDAUT (ID de Autorización)
- Registro de motivos de anulación
- Historial completo de anulaciones

**Tabla:** `up_anulaciones`

## 🗄️ Estructura de Base de Datos

### Tablas Creadas

1. **up_elegibilidad**
   - Almacena consultas de elegibilidad
   - Guarda datos del afiliado retornados por SOAP
   - Tracking de tiempo de ejecución
   - Soporte para prestaciones consultadas

2. **up_autorizacion_previa**
   - Almacena autorizaciones de prestaciones
   - Detalles de cada prestación autorizada
   - Importes totales, a cargo de OS y afiliado
   - Contexto de autorización (Ambulatorio, Internado, etc.)

3. **up_anulaciones**
   - Registro de anulaciones
   - Tipo de ID utilizado para anular
   - Motivo de anulación
   - Resultado de la operación

Todas las tablas están relacionadas con `up_transacciones_soap` para trazabilidad completa.

## 🎨 Interfaz

### Menús en CRUDBooster

Los menús están organizados bajo **"Unión Personal"**:

```
📂 Unión Personal
  ├─ 📊 Consumos UP (históricos)
  ├─ ✅ ELG UP (nuevo)
  ├─ ☑️ AP UP (nuevo)
  ├─ ❌ ATR UP (nuevo)
  ├─ 📝 Solicitudes UP
  ├─ 👥 Afiliados UP
  └─ 🔄 Transacciones SOAP
```

### Características de la Interfaz

- **Formularios Intuitivos:** Campos pre-llenados con valores de testing
- **Validación:** Validación client-side y server-side
- **Feedback Visual:** Badges de colores según status (OK, NO, ERROR)
- **Row Colors:** Filas coloreadas según resultado
- **Exportación:** Todos los módulos permiten exportar a Excel
- **Filtros:** Filtros avanzados por todos los campos

## 🔧 Archivos Creados

### Migraciones
```
database/migrations/2025_10_31_000001_create_up_elegibilidad_table.php
database/migrations/2025_10_31_000002_create_up_autorizacion_previa_table.php
database/migrations/2025_10_31_000003_create_up_anulaciones_table.php
```

### Modelos
```
app/Models/UpElegibilidad.php
app/Models/UpAutorizacionPrevia.php
app/Models/UpAnulacion.php
```

### Controladores
```
app/Http/Controllers/AdminUpElegibilidadController.php
app/Http/Controllers/AdminUpAutorizacionPreviaController.php
app/Http/Controllers/AdminUpAnulacionesController.php
```

### Seeders
```
database/seeders/UpMenusSeeder.php (actualizado)
```

## 🚀 Instalación

Las tablas ya han sido creadas y los menús ya están disponibles en el sistema.

Si necesita ejecutar las migraciones manualmente:

```bash
php artisan migrate --path=/database/migrations/2025_10_31_000001_create_up_elegibilidad_table.php
php artisan migrate --path=/database/migrations/2025_10_31_000002_create_up_autorizacion_previa_table.php
php artisan migrate --path=/database/migrations/2025_10_31_000003_create_up_anulaciones_table.php
```

Para crear los menús:

```bash
php artisan db:seed --class=UpMenusSeeder
```

## 📝 Uso

### ELG UP - Consulta de Elegibilidad

1. Ir a **Unión Personal → ELG UP**
2. Click en **"Agregar Nuevo"**
3. Completar el formulario:
   - **Código Afiliado:** 54715500 (testing)
   - **TOKEN:** 9999 (o dejar vacío para usar Plan/VerCred)
   - **Plan:** 150 (solo si no usa TOKEN)
   - **Versión Credencial:** 45 (solo si no usa TOKEN)
4. Click en **"Guardar"**

El sistema ejecutará la transacción SOAP y mostrará el resultado.

### AP UP - Autorización Previa

1. Ir a **Unión Personal → AP UP**
2. Click en **"Agregar Nuevo"**
3. Completar el formulario:
   - **Código Afiliado:** 54715500
   - **TOKEN:** 9999
   - **Contexto:** A (Ambulatorio)
   - **Prestaciones (JSON):**
   ```json
   [
     {"tipo": "P", "id": "1420107", "cant": 1},
     {"tipo": "P", "id": "1420101", "cant": 1}
   ]
   ```
4. Click en **"Guardar"**

El sistema autorizará las prestaciones y retornará IDAUT e IDTRAN.

### ATR UP - Anulación

1. Ir a **Unión Personal → ATR UP**
2. Click en **"Agregar Nuevo"**
3. Completar el formulario:
   - **Código Afiliado:** 54715500
   - **Tipo de ID:** IDTRAN (o MSGID, IDAUT)
   - **ID a Anular:** (copiar desde una autorización previa)
   - **Motivo:** "Prueba de anulación desde SISCON2"
4. Click en **"Guardar"**

El sistema anulará la transacción especificada.

## 📊 Datos de Testing

### Ambiente TEST (configurado en .env)

```bash
UP_AMBIENTE=test
SOAP_UP_ENDPOINT=http://181.13.241.19:7002/cawsTest/Servicios
SOAP_UP_WSDL=http://181.13.241.19:7002/cawsTest/Servicios?wsdl
SOAP_UP_PRESTADOR_ID=8888
SOAP_UP_USER_ID=8888
SOAP_UP_USER_PASS=7777
```

### Afiliados de Testing

**Afiliado 1:**
- Código: `54715500`
- Plan: `150`
- Versión: `45`
- TOKEN: `9999`

**Afiliado 2:**
- Código: `54715300`
- Plan: `2`
- Versión: `31`
- TOKEN: `9999`

### Prestaciones de Testing

- `1420107` - Consulta Especializada
- `1420101` - Consulta Médica

### Credenciales SOAP

- Usuario: `8888`
- Password: `7777`
- ID Prestador: `8888`

## 🔍 Trazabilidad

Todas las transacciones SOAP se registran en:

1. **Tabla específica** (up_elegibilidad, up_autorizacion_previa, up_anulaciones)
2. **Tabla de trazabilidad** (up_transacciones_soap)
3. **Logs de Laravel** (storage/logs/laravel.log)

Cada registro incluye:
- Request XML completo
- Response XML completo
- Tiempo de ejecución en milisegundos
- Status de la operación
- Datos parseados del afiliado
- Usuario que ejecutó la transacción

## ⚠️ Notas Importantes

1. **TOKEN 9999:** Es un TOKEN especial de desarrollo que no expira. Solo para testing.

2. **Autocrear Afiliados:** La configuración `UP_AUTOCREAR_AFILIADOS=true` hace que los afiliados se creen automáticamente al verificar elegibilidad.

3. **Validación de Prestaciones (AP):** El formato JSON debe ser válido. Ejemplo:
   ```json
   [{"tipo":"P","id":"1420107","cant":1}]
   ```

4. **Anulaciones:** Solo se pueden anular transacciones que existen en el sistema de Unión Personal. Usar IDTRAN obtenido de una autorización previa exitosa.

5. **Ambiente:** Asegurarse de tener `UP_AMBIENTE=test` en el `.env` para usar los datos de testing.

## 🎯 Próximos Pasos Sugeridos

1. **Pruebas Exhaustivas:**
   - Probar ELG con diferentes afiliados
   - Probar AP con múltiples prestaciones
   - Probar ATR con los 3 tipos de ID

2. **Integración:**
   - Vincular con módulos existentes si es necesario
   - Agregar búsqueda de IDTRAN/IDAUT desde otros módulos

3. **Producción:**
   - Cambiar ambiente a `production` en `.env`
   - Actualizar endpoints a URLs de producción
   - Actualizar credenciales reales

4. **Mejoras Futuras:**
   - Agregar dashboard con estadísticas
   - Implementar notificaciones por email
   - Agregar validaciones adicionales

## 📚 Documentación Relacionada

- `manual up.md` - Manual completo de testing
- `UNION_PERSONAL_README.md` - Documentación de la integración UP
- `IMPLEMENTACION_COMPLETADA.md` - Implementación original

## 🆘 Soporte

Para problemas o preguntas:
1. Revisar logs en `storage/logs/laravel.log`
2. Verificar tabla `up_transacciones_soap` para ver detalles de errores
3. Consultar documentación oficial de UP en `documentacion up/`

---

**Fecha de Implementación:** 31 de Octubre 2025
**Versión:** 1.0
**Proyecto:** SISCON2_PROD
