# Flujo Convenio UP - Reestructurado y Mejorado

## 📋 Resumen de Cambios Propuestos

El flujo actual de convenio UP necesita ser reestructurado para seguir el proceso correcto que las farmacias de UP requieren para validar y autorizar medicamentos a afiliados.

## 🔄 Flujo Actual vs Flujo Propuesto

### ❌ Flujo Actual (Problemático)
```
Consumo Generado → Verificar Elegibilidad → Aprobar → Marcar Entregado
```

### ✅ Flujo Propuesto (Correcto)
```
1. Consultar Consumos UP → 2. Consultar Elegibilidad → 3. Aprobación → 4. Validación de Entrega
```

## 🎯 Nuevo Flujo Paso a Paso

### **Paso 1: Consultar Consumos UP**
**Objetivo:** Las farmacias deben poder consultar si un afiliado tiene consumos pendientes

**Funcionalidad:**
- Buscar por número de afiliado
- Mostrar todos los consumos disponibles para ese afiliado
- Filtrar por fecha, tipo de prestación, estado
- Ver detalles del consumo (medicamento, cantidad, importe)

**Implementación:**
- Mejorar filtros en la vista principal
- Agregar búsqueda rápida por afiliado
- Mostrar información más clara de los consumos

### **Paso 2: Consultar Elegibilidad**
**Objetivo:** Verificar si el afiliado puede recibir el medicamento

**Botón:** "Consultar Elegibilidad"
**Funcionalidad:**
- Ejecutar transacción ELG con Unión Personal
- Validar estado del afiliado
- Verificar cobertura del plan
- Autocreación del afiliado si no existe

**Estados resultantes:**
- `elegibilidad_ok` → Puede continuar al paso 3
- `elegibilidad_no` → Mostrar motivo del rechazo

### **Paso 3: Aprobación**
**Objetivo:** Obtener autorización formal para dispensar el medicamento

**Botón:** "Aprobar Prestación"
**Funcionalidad:**
- Ejecutar transacción AP con Unión Personal
- Obtener IDAUT (número de autorización)
- Registrar importes autorizados

**Estados resultantes:**
- `aprobado` → Puede continuar al paso 4
- `rechazado` → Fin del proceso

### **Paso 4: Validación de Entrega**
**Objetivo:** Registrar que el medicamento fue entregado al afiliado

**Botón:** "Generar Validación de Entrega"
**Funcionalidad:**
- Confirmar entrega física del medicamento
- Registrar fecha, hora y usuario que entrega
- Generar registro de validación final
- Guardar en base de datos para auditoría

**Estado resultante:**
- `entregado` → Proceso completado

## 🗄️ Mejoras en Base de Datos

### Nueva Tabla: `up_validaciones_entrega`
```sql
CREATE TABLE up_validaciones_entrega (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    consumo_id BIGINT UNSIGNED NOT NULL,
    afiliado_codigo VARCHAR(20) NOT NULL,
    medicamento_codigo VARCHAR(50) NOT NULL,
    medicamento_descripcion TEXT,
    cantidad INTEGER NOT NULL,
    fecha_entrega DATETIME NOT NULL,
    usuario_entrega VARCHAR(100) NOT NULL,
    farmacia_codigo VARCHAR(20),
    farmacia_nombre VARCHAR(255),
    observaciones TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (consumo_id) REFERENCES up_consumos(id),
    INDEX idx_afiliado (afiliado_codigo),
    INDEX idx_fecha_entrega (fecha_entrega),
    INDEX idx_medicamento (medicamento_codigo)
);
```

### Campos Adicionales en `up_consumos`
```sql
ALTER TABLE up_consumos ADD COLUMN validacion_entrega_id BIGINT UNSIGNED NULL;
ALTER TABLE up_consumos ADD COLUMN fecha_validacion_entrega DATETIME NULL;
ALTER TABLE up_consumos ADD COLUMN usuario_validacion_entrega VARCHAR(100) NULL;
ALTER TABLE up_consumos ADD FOREIGN KEY (validacion_entrega_id) REFERENCES up_validaciones_entrega(id);
```

## 🎨 Mejoras en la Interfaz

### 1. Panel de Búsqueda Mejorado
```html
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3><i class="fa fa-search"></i> Consultar Consumos UP</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <label>Número de Afiliado</label>
                <input type="text" class="form-control" placeholder="Ej: 54715500">
                <button class="btn btn-primary btn-block mt-2">
                    <i class="fa fa-search"></i> Buscar Consumos
                </button>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <label>Fecha Desde</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Fecha Hasta</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 2. Botones de Acción Reestructurados
```php
// Paso 1: Siempre visible para consumos pendientes
$this->addaction[] = [
    'label' => 'Consultar Elegibilidad',
    'url' => CRUDBooster::mainpath('consultar-elegibilidad/[id]'),
    'icon' => 'fa fa-user-check',
    'color' => 'info',
    'showIf' => "[estado_flujo] == 'pendiente' || [estado_flujo] == 'elegibilidad_no'",
];

// Paso 2: Solo si elegibilidad es OK
$this->addaction[] = [
    'label' => 'Aprobar Prestación',
    'url' => CRUDBooster::mainpath('aprobar-prestacion/[id]'),
    'icon' => 'fa fa-check-circle',
    'color' => 'success',
    'showIf' => "[estado_flujo] == 'elegibilidad_ok'",
];

// Paso 3: Solo si está aprobado
$this->addaction[] = [
    'label' => 'Generar Validación de Entrega',
    'url' => CRUDBooster::mainpath('generar-validacion-entrega/[id]'),
    'icon' => 'fa fa-clipboard-check',
    'color' => 'primary',
    'showIf' => "[estado_flujo] == 'aprobado'",
];
```

### 3. Estados Visuales Mejorados
```php
$this->table_row_color = [
    ['condition' => "[estado_flujo] == 'entregado'", 'color' => 'success'],
    ['condition' => "[estado_flujo] == 'aprobado'", 'color' => 'info'],
    ['condition' => "[estado_flujo] == 'elegibilidad_ok'", 'color' => 'warning'],
    ['condition' => "[estado_flujo] == 'pendiente'", 'color' => 'default'],
    ['condition' => "[estado_flujo] == 'elegibilidad_no' || [estado_flujo] == 'rechazado'", 'color' => 'danger'],
    ['condition' => "[estado_flujo] == 'anulado'", 'color' => 'active'],
];
```

## 🔧 Nuevos Métodos del Controlador

### 1. Método: Consultar Elegibilidad
```php
public function getConsultarElegibilidad($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if (!$consumo->puedeConsultarElegibilidad()) {
        return redirect()->back()->with('warning', 'Este consumo no puede consultar elegibilidad en su estado actual');
    }
    
    // Ejecutar ELG
    $resultado = $this->soapService->ejecutarELG([
        'afiliado_codigo' => $consumo->afiliado,
        'plan' => $consumo->modelo_plan,
        'prestaciones' => [[
            'tipo' => $consumo->tipo_pres,
            'id' => $consumo->cod_prestacion,
            'cant' => $consumo->cant,
        ]],
    ]);
    
    if ($resultado['success']) {
        $consumo->update([
            'estado_flujo' => 'elegibilidad_ok',
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => auth()->user()->email,
        ]);
        
        return redirect()->back()->with('success', 'Elegibilidad confirmada. Puede proceder a aprobar la prestación.');
    } else {
        $consumo->update([
            'estado_flujo' => 'elegibilidad_no',
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => auth()->user()->email,
        ]);
        
        return redirect()->back()->with('error', 'Elegibilidad rechazada: ' . $resultado['message']);
    }
}
```

### 2. Método: Generar Validación de Entrega
```php
public function getGenerarValidacionEntrega($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if (!$consumo->puedeGenerarValidacionEntrega()) {
        return redirect()->back()->with('warning', 'Este consumo debe estar aprobado para generar validación de entrega');
    }
    
    return view('up_consumos.generar_validacion_entrega', compact('consumo'));
}

public function postGenerarValidacionEntrega(Request $request)
{
    $consumo = UpConsumo::findOrFail($request->consumo_id);
    
    // Crear registro de validación de entrega
    $validacion = UpValidacionEntrega::create([
        'consumo_id' => $consumo->id,
        'afiliado_codigo' => $consumo->afiliado,
        'medicamento_codigo' => $consumo->cod_prestacion,
        'medicamento_descripcion' => $consumo->desc,
        'cantidad' => $consumo->cant,
        'fecha_entrega' => now(),
        'usuario_entrega' => auth()->user()->email,
        'farmacia_codigo' => $request->farmacia_codigo,
        'farmacia_nombre' => $request->farmacia_nombre,
        'observaciones' => $request->observaciones,
    ]);
    
    // Actualizar consumo
    $consumo->update([
        'estado_flujo' => 'entregado',
        'validacion_entrega_id' => $validacion->id,
        'fecha_validacion_entrega' => now(),
        'usuario_validacion_entrega' => auth()->user()->email,
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Validación de entrega generada exitosamente',
        'validacion_id' => $validacion->id,
    ]);
}
```

## 📊 Nuevo Modelo: UpValidacionEntrega

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpValidacionEntrega extends Model
{
    protected $table = 'up_validaciones_entrega';
    
    protected $fillable = [
        'consumo_id',
        'afiliado_codigo',
        'medicamento_codigo',
        'medicamento_descripcion',
        'cantidad',
        'fecha_entrega',
        'usuario_entrega',
        'farmacia_codigo',
        'farmacia_nombre',
        'observaciones',
    ];
    
    protected $casts = [
        'fecha_entrega' => 'datetime',
        'cantidad' => 'integer',
    ];
    
    public function consumo()
    {
        return $this->belongsTo(UpConsumo::class, 'consumo_id');
    }
    
    public function afiliado()
    {
        return $this->belongsTo(AfiliadoConvenioUp::class, 'afiliado_codigo', 'codigo_afiliado');
    }
}
```

## 🎯 Mejoras Adicionales Propuestas

### 1. Dashboard de Farmacia
- Panel específico para farmacias con métricas
- Consumos pendientes por procesar
- Estadísticas de entregas del día/mes
- Alertas de consumos próximos a vencer

### 2. Búsqueda Inteligente
- Autocompletado de números de afiliado
- Búsqueda por nombre y apellido
- Filtros avanzados por tipo de medicamento
- Historial de consumos del afiliado

### 3. Validaciones Mejoradas
- Verificación de stock antes de aprobar
- Alertas de interacciones medicamentosas
- Validación de dosis máximas
- Control de duplicados

### 4. Reportes y Auditoría
- Reporte de entregas por farmacia
- Auditoría completa del flujo
- Estadísticas de aprobaciones/rechazos
- Exportación de datos para facturación

### 5. Notificaciones
- Email al aprobar prestación
- SMS al afiliado cuando esté listo
- Alertas de vencimiento de autorizaciones
- Notificaciones push para farmacias

## 🔄 Orden de Menús Reestructurado

### Menú Principal: "Convenio UP"
```
📂 Convenio UP
   │
   ├── 🔍 1. Consultar Consumos UP
   │   └── Buscar y procesar consumos de afiliados
   │
   ├── ✅ 2. Elegibilidad (ELG)
   │   └── Historial de consultas de elegibilidad
   │
   ├── 🎯 3. Aprobaciones (AP)
   │   └── Historial de aprobaciones de prestaciones
   │
   ├── 📋 4. Validaciones de Entrega
   │   └── Registro de entregas realizadas
   │
   ├── ❌ 5. Anulaciones (ATR)
   │   └── Gestión de anulaciones
   │
   ├── 👥 6. Afiliados UP
   │   └── Base de datos de afiliados
   │
   ├── 📊 7. Dashboard Farmacia
   │   └── Panel de control para farmacias
   │
   └── ⚙️ 8. Configuración
       └── Ambiente y parámetros SOAP
```

## 📝 Documentación de Usuario Actualizada

### Manual para Farmacias

#### **Paso 1: Buscar Consumos del Afiliado**
1. Acceder a "Convenio UP → Consultar Consumos UP"
2. Ingresar número de afiliado en el buscador
3. Revisar la lista de consumos disponibles
4. Verificar datos del medicamento y cantidad

#### **Paso 2: Consultar Elegibilidad**
1. Hacer clic en "Consultar Elegibilidad" en el consumo deseado
2. El sistema verificará automáticamente con Unión Personal
3. Si es exitoso, el estado cambiará a "Elegibilidad OK"
4. Si falla, revisar el motivo del rechazo

#### **Paso 3: Aprobar Prestación**
1. Con elegibilidad confirmada, hacer clic en "Aprobar Prestación"
2. El sistema obtendrá la autorización de Unión Personal
3. Se generará el número IDAUT para facturación
4. El estado cambiará a "Aprobado"

#### **Paso 4: Generar Validación de Entrega**
1. Al entregar el medicamento al paciente, hacer clic en "Generar Validación de Entrega"
2. Completar los datos de la farmacia
3. Agregar observaciones si es necesario
4. Confirmar la entrega
5. Se generará el registro final para auditoría

## 🚀 Plan de Implementación

### Fase 1: Base de Datos (1 día)
- [ ] Crear migración para tabla `up_validaciones_entrega`
- [ ] Agregar campos adicionales a `up_consumos`
- [ ] Crear modelo `UpValidacionEntrega`
- [ ] Actualizar relaciones en modelos existentes

### Fase 2: Controlador y Lógica (2 días)
- [ ] Actualizar `AdminUpConsumosController`
- [ ] Implementar método `getConsultarElegibilidad`
- [ ] Implementar método `getGenerarValidacionEntrega`
- [ ] Actualizar métodos de validación en modelo `UpConsumo`

### Fase 3: Vistas y UI (2 días)
- [ ] Crear vista `generar_validacion_entrega.blade.php`
- [ ] Mejorar panel de búsqueda principal
- [ ] Actualizar botones de acción
- [ ] Mejorar indicadores visuales de estado

### Fase 4: Menús y Navegación (1 día)
- [ ] Reestructurar menús en CRUDBooster
- [ ] Crear dashboard específico para farmacias
- [ ] Actualizar seeder de menús
- [ ] Configurar permisos por rol

### Fase 5: Testing y Documentación (1 día)
- [ ] Probar flujo completo con datos de testing
- [ ] Actualizar documentación de usuario
- [ ] Crear manual específico para farmacias
- [ ] Validar con usuarios finales

## 🎯 Beneficios del Nuevo Flujo

### Para Farmacias
- ✅ Proceso más claro y lógico
- ✅ Mejor control de entregas
- ✅ Auditoría completa del proceso
- ✅ Interfaz más intuitiva

### Para Administradores
- ✅ Trazabilidad completa
- ✅ Reportes más detallados
- ✅ Mejor control de stock
- ✅ Auditoría mejorada

### Para el Sistema
- ✅ Base de datos más organizada
- ✅ Flujo de trabajo estandarizado
- ✅ Mejor integración con UP
- ✅ Escalabilidad mejorada

---

**Fecha de Propuesta:** 4 de Noviembre 2025  
**Versión:** 1.0 - Flujo Reestructurado  
**Proyecto:** SISCON2_PROD - Convenio UP  
**Estado:** 📋 Propuesta para Implementación
