# 🚀 GUÍA RÁPIDA - Sistema Unión Personal

## ⚡ Acceso Inmediato

### 📍 **MÓDULOS ADMIN (Panel CRUDBooster)**

Acceder en: **http://localhost/admin**

Buscar en el menú lateral: **"Unión Personal"**

```
📂 Unión Personal
   │
   ├── 📊 Consumos UP
   │   └── URL: /admin/up_consumos
   │   └── 1,010 registros históricos (solo lectura)
   │
   ├── 📝 Solicitudes UP
   │   └── URL: /admin/up_solicitudes
   │   └── Crear solicitudes | ELG | AP | ATR
   │
   ├── 👤 Afiliados UP
   │   └── URL: /admin/afiliados_convenio_up
   │   └── Afiliados autocreados desde SOAP 🌟
   │
   └── 📡 Transacciones SOAP
       └── URL: /admin/up_transacciones_soap
       └── Log completo con XMLs
```

---

## 🔄 **FLUJO DE TRABAJO**

### **Opción 1: Desde el Admin**

```
1. Ir a: /admin/up_solicitudes
         ↓
2. Click: "Agregar Nueva Solicitud"
         ↓
3. Completar formulario:
   - Código afiliado: 54715500
   - TOKEN: 9999
   - Agregar prestaciones
         ↓
4. Guardar como "Borrador"
         ↓
5. Click: "Verificar Elegibilidad (ELG)"
   🌟 El afiliado se AUTOCREA automáticamente
         ↓
6. Si OK → Click: "Aprobar Prestación (AP)"
         ↓
7. ✅ Solicitud APROBADA
```

### **Opción 2: Desde la API**

```bash
# 1. Test de conexión
curl http://localhost/api/union-personal/test

# 2. Verificar elegibilidad (AUTOCREA AFILIADO 🌟)
curl -X POST http://localhost/api/union-personal/elegibilidad \
  -H "Content-Type: application/json" \
  -d '{
    "afiliado_codigo": "54715500",
    "token": "9999"
  }'

# 3. Verificar que se creó
mysql -u root -pA22f04gc* siscon -e \
  "SELECT * FROM afiliados_convenio_up WHERE codigo_afiliado='54715500'"

# 4. Aprobar prestación
curl -X POST http://localhost/api/union-personal/autorizar-prestacion \
  -H "Content-Type: application/json" \
  -d '{
    "afiliado_codigo": "54715500",
    "token": "9999",
    "contexto_tipo": "A",
    "prestaciones": [
      {"tipo": "P", "id": "1420107", "cant": 1}
    ]
  }'
```

---

## 🌟 **AUTOCREACIÓN DE AFILIADOS**

### **¿Dónde ocurre?**

Cuando ejecutas **ELG (Elegibilidad)** exitoso (STATUS=OK):

1. ✅ Datos extraídos del response SOAP
2. ✅ Registro creado/actualizado en `afiliados_convenio_up`
3. ✅ Información completa guardada automáticamente

### **Ver afiliados autocreados:**

**Admin:**
```
/admin/afiliados_convenio_up
```

**SQL:**
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

**API:**
```bash
curl http://localhost/api/union-personal/buscar-afiliado/54715500
```

---

## 📊 **DATOS DE PRUEBA**

### **Ambiente TEST (Configurado)**

```bash
# Endpoint
http://181.13.241.19:7002/cawsTest/Servicios

# Credenciales
User ID: 8888
Password: 7777
Prestador ID: 8888

# TOKEN desarrollo (no expira)
TOKEN: 9999

# Afiliados de prueba
Afiliado 1: 54715500 | Plan: 150 | VerCred: 45
Afiliado 2: 54715300 | Plan: 2   | VerCred: 31

# Prestaciones de prueba
1420107 - CONSULTA ESPECIALIZADA
1420101 - CONSULTA MEDICA
```

---

## 🧪 **TESTING RÁPIDO**

### **Comando Artisan:**

```bash
# Test completo
php artisan up:test

# Solo conexión
php artisan up:test test

# Solo elegibilidad (autocrea afiliado)
php artisan up:test elg

# Estadísticas
php artisan up:test
```

### **Ver Logs:**

```bash
# Logs de Laravel
tail -f storage/logs/laravel.log | grep -i soap

# Ver últimas transacciones
mysql -u root -pA22f04gc* siscon -e \
  "SELECT transaction_type, status, idtran, created_at
   FROM up_transacciones_soap
   ORDER BY created_at DESC
   LIMIT 10"
```

---

## 📋 **CONSULTAS SQL ÚTILES**

```sql
-- Total de registros por tabla
SELECT 'Consumos' as Tabla, COUNT(*) as Total FROM up_consumos
UNION ALL
SELECT 'Afiliados autocreados', COUNT(*) FROM afiliados_convenio_up
UNION ALL
SELECT 'Solicitudes', COUNT(*) FROM up_solicitudes
UNION ALL
SELECT 'Transacciones SOAP', COUNT(*) FROM up_transacciones_soap;

-- Últimos afiliados autocreados
SELECT
    codigo_afiliado,
    CONCAT(apellido, ', ', nombre) as nombre_completo,
    plan_nombre,
    DATE_FORMAT(ultima_verificacion_soap, '%d/%m/%Y %H:%i') as ultima_verificacion
FROM afiliados_convenio_up
ORDER BY ultima_verificacion_soap DESC
LIMIT 10;

-- Solicitudes por estado
SELECT
    estado,
    COUNT(*) as cantidad
FROM up_solicitudes
GROUP BY estado;

-- Transacciones SOAP por tipo y status
SELECT
    transaction_type,
    status,
    COUNT(*) as cantidad,
    AVG(execution_time_ms) as tiempo_promedio_ms
FROM up_transacciones_soap
GROUP BY transaction_type, status;
```

---

## 🔧 **TROUBLESHOOTING**

### **Problema: No veo los módulos en el menú**

**Solución:**
```bash
# 1. Limpiar cache
php artisan cache:clear
php artisan config:clear

# 2. Verificar que están registrados
mysql -u root -pA22f04gc* siscon -e \
  "SELECT id, name, path FROM cms_menus WHERE name LIKE '%UP%' OR name LIKE '%Unión%'"

# 3. Si no están, ejecutar seeder
php artisan db:seed --class=UpMenusSeeder
```

### **Problema: Error SOAP "extension not loaded"**

**Solución:**
```bash
# Verificar si está instalada
php -m | grep soap

# Si no aparece, habilitar en php.ini
# Agregar: extension=soap
# Luego: sudo service php-fpm restart
```

### **Problema: Afiliado no se autocrea**

**Verificar:**
```bash
# 1. Configuración
grep UP_AUTOCREAR_AFILIADOS .env
# Debe ser: UP_AUTOCREAR_AFILIADOS=true

# 2. Ver logs
tail -50 storage/logs/laravel.log | grep -i "autocrear"

# 3. Verificar respuesta SOAP
mysql -u root -pA22f04gc* siscon -e \
  "SELECT status, response_message, response_data
   FROM up_transacciones_soap
   WHERE transaction_type='ELG'
   ORDER BY created_at DESC
   LIMIT 1"
```

---

## 📚 **DOCUMENTACIÓN COMPLETA**

```
📄 UNION_PERSONAL_README.md           - Manual técnico completo
📄 IMPLEMENTACION_COMPLETADA.md       - Resumen de implementación
📄 GUIA_RAPIDA_UP.md                  - Esta guía (acceso rápido)
📄 documentacion up/preguntas up.md   - Preguntas y respuestas
📄 documentacion up/manual up.md      - Manual de pruebas
```

---

## 🎯 **CHECKLIST RÁPIDO**

**Antes de usar el sistema:**
- [x] Extensión SOAP de PHP habilitada
- [x] Variables .env configuradas
- [x] Migraciones ejecutadas (5 tablas)
- [x] Consumos importados (1,010 registros)
- [x] Módulos registrados en CRUDBooster (4 módulos)
- [x] Cache limpiado

**Tu primer solicitud:**
- [ ] Acceder a /admin/up_solicitudes
- [ ] Crear nueva solicitud
- [ ] Verificar elegibilidad (ELG) 🌟 Autocrea afiliado
- [ ] Aprobar prestación (AP)
- [ ] Ver en /admin/afiliados_convenio_up que se creó el afiliado

---

## 📞 **SOPORTE**

**Archivos importantes:**
- Configuración: `config/union_personal.php`
- Servicio SOAP: `app/Services/UnionPersonalSoapService.php`
- Variables .env: Ver sección "Datos de Prueba" arriba

**Logs:**
- Laravel: `storage/logs/laravel.log`
- Base de datos: Tabla `up_transacciones_soap`

---

✨ **Sistema 100% funcional y listo para usar**

**Implementado:** 28/10/2025
**Versión:** 1.0.0
**Estado:** ✅ PRODUCCIÓN READY
