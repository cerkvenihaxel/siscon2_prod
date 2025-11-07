# UP Consumos - Guía de Resolución de Problemas

## ✅ Estado Actual del Sistema

### Base de Datos
- ✅ Tabla `up_consumos` existe con 1038 registros
- ✅ Campo `estado_flujo` configurado correctamente
- ✅ Campos `observaciones_flujo`, `usuario_observaciones`, `fecha_observaciones` agregados
- ✅ Estados disponibles: pendiente (1018), elegibilidad_ok (8), aprobado (8), entregado (2), anulado (2)

### API
- ✅ API funcionando en `/api/up-consumos`
- ✅ Filtros por fecha, estado y afiliado operativos
- ✅ Paginación funcionando
- ✅ Endpoints de estadísticas y observaciones disponibles

### Controlador CRUDBooster
- ✅ `AdminUpConsumosController` configurado
- ✅ Botones de acción definidos:
  - Ver Historial SOAP
  - Verificar Elegibilidad (ELG) - para estado `pendiente`
  - Aprobar Prestación (AP) - para estado `elegibilidad_ok`
  - Marcar Entregado - para estado `aprobado`
  - Observaciones - siempre disponible
  - Anular - para estados `aprobado` y `entregado`

## 🔧 Verificaciones para CRUDBooster

### 1. Verificar Ruta en CRUDBooster
```bash
# Verificar que la ruta esté registrada
php artisan route:list | grep up_consumos
```

### 2. Verificar Permisos de Usuario
- Asegúrese de que el usuario tenga permisos para el módulo `up_consumos`
- Verificar en Admin > Privileges que el rol tenga acceso

### 3. Limpiar Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 4. Verificar Configuración CRUDBooster
En `config/crudbooster.php` verificar:
```php
'ADMIN_PATH' => 'admin',
'ADMIN_URL' => env('APP_URL').'/admin',
```

## 🎯 URLs Esperadas

### Panel Admin
```
http://tu-dominio.com/admin/up_consumos
```

### API Endpoints
```
GET  /api/up-consumos
GET  /api/up-consumos/{id}
PUT  /api/up-consumos/{id}/observaciones
GET  /api/up-consumos/stats
```

### Acciones del Workflow
```
/admin/up_consumos/verificar-elegibilidad/{id}
/admin/up_consumos/aprobar-prestacion/{id}
/admin/up_consumos/marcar-entregado/{id}
/admin/up_consumos/observaciones/{id}
/admin/up_consumos/anular-transaccion/{id}
```

## 🐛 Problemas Comunes

### Los botones no aparecen
1. Verificar que `$this->addaction` esté definido en `cbInit()`
2. Verificar condiciones `showIf` en los botones
3. Limpiar cache de vistas

### Error 404 en acciones
1. Verificar que los métodos existan en el controlador:
   - `getVerificarElegibilidad($id)`
   - `getAprobarPrestacion($id)`
   - `getMarcarEntregado($id)`
   - `getObservaciones($id)`
   - `getAnularTransaccion($id)`

### Campos no se muestran
1. Verificar `$this->col` en `cbInit()`
2. Verificar nombres de campos en base de datos
3. Verificar que el modelo `UpConsumo` tenga los campos en `$fillable`

## 📊 Datos de Prueba Disponibles

```sql
-- Consumos por estado
SELECT estado_flujo, COUNT(*) FROM up_consumos GROUP BY estado_flujo;

-- Consumos recientes
SELECT id, desc, afiliado, estado_flujo, created_at 
FROM up_consumos 
ORDER BY created_at DESC 
LIMIT 10;
```

## 🔄 Flujo de Trabajo Esperado

1. **Pendiente** → Botón "Verificar Elegibilidad"
2. **Elegibilidad OK** → Botón "Aprobar Prestación"  
3. **Aprobado** → Botón "Marcar Entregado"
4. **Entregado** → Flujo completado
5. **Observaciones** → Disponible en cualquier momento

## 📞 Verificación Rápida

```bash
# Verificar API
curl "http://localhost:8000/api/up-consumos?per_page=1"

# Verificar base de datos
mysql -u root -p'A22f04gc*' -e "SELECT COUNT(*) FROM siscon.up_consumos;"

# Verificar archivos
ls -la app/Http/Controllers/AdminUpConsumosController.php
ls -la app/Models/UpConsumo.php
ls -la resources/views/up_consumos/
```
