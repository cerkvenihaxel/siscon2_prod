# Implementación Rápida - Flujo UP Reestructurado

## 🚀 Pasos de Implementación (30 minutos)

### 1. Base de Datos (5 minutos)
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeder de menús
php artisan db:seed --class=UpMenusReestructuradosSeeder
```

### 2. Actualizar Controlador (15 minutos)

**Archivo:** `app/Http/Controllers/AdminUpConsumosController.php`

**Reemplazar la sección de botones de acción:**
```php
// Buscar esta línea en cbInit():
$this->addaction = array();

// Reemplazar todo el bloque $this->addaction con:
$this->addaction = array();

$this->addaction[] = [
    'label' => 'Consultar Elegibilidad', 
    'url' => CRUDBooster::mainpath('consultar-elegibilidad/[id]'),
    'icon' => 'fa fa-user-check',
    'color' => 'info',
    'showIf' => "[estado_flujo] == 'pendiente' || [estado_flujo] == 'elegibilidad_no'",
    'confirmation' => true
];

$this->addaction[] = [
    'label' => 'Aprobar Prestación', 
    'url' => CRUDBooster::mainpath('aprobar-prestacion/[id]'),
    'icon' => 'fa fa-check-circle',
    'color' => 'success',
    'showIf' => "[estado_flujo] == 'elegibilidad_ok'",
    'confirmation' => true
];

$this->addaction[] = [
    'label' => 'Generar Validación de Entrega', 
    'url' => CRUDBooster::mainpath('generar-validacion-entrega/[id]'),
    'icon' => 'fa fa-clipboard-check',
    'color' => 'primary',
    'showIf' => "[estado_flujo] == 'aprobado'"
];

$this->addaction[] = [
    'label' => 'Ver Historial SOAP',
    'url' => CRUDBooster::mainpath('ver-historial-soap/[id]'),
    'icon' => 'fa fa-history',
    'color' => 'default'
];

$this->addaction[] = [
    'label' => 'Anular Transacción', 
    'url' => CRUDBooster::mainpath('anular-transaccion/[id]'),
    'icon' => 'fa fa-times-circle',
    'color' => 'danger',
    'showIf' => "[estado_flujo] == 'aprobado' || [estado_flujo] == 'entregado'",
    'confirmation' => true
];
```

**Agregar estos métodos al final del controlador:**
```php
/**
 * Consultar Elegibilidad (ELG)
 */
public function getConsultarElegibilidad($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if (!in_array($consumo->estado_flujo, ['pendiente', 'elegibilidad_no'])) {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Este consumo no puede consultar elegibilidad', 'warning');
    }

    $params = [
        'afiliado_codigo' => $consumo->afiliado,
        'plan' => $consumo->modelo_plan,
        'prestaciones' => [[
            'tipo' => $consumo->tipo_pres,
            'id' => $consumo->cod_prestacion,
            'cant' => $consumo->cant,
        ]],
    ];

    $resultado = $this->soapService->ejecutarELG($params);
    
    if ($resultado['success']) {
        $consumo->update([
            'estado_flujo' => 'elegibilidad_ok',
            'fecha_elegibilidad' => now(),
            'usuario_elegibilidad' => CRUDBooster::myEmail(),
        ]);
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad confirmada. Puede aprobar la prestación.', 'success');
    } else {
        $consumo->update(['estado_flujo' => 'elegibilidad_no']);
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad rechazada: ' . $resultado['message'], 'danger');
    }
}

/**
 * Generar Validación de Entrega
 */
public function getGenerarValidacionEntrega($id)
{
    $consumo = UpConsumo::findOrFail($id);
    
    if ($consumo->estado_flujo !== 'aprobado') {
        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Solo se pueden generar validaciones para consumos aprobados', 'warning');
    }

    // Crear validación de entrega
    $validacion = \App\Models\UpValidacionEntrega::create([
        'consumo_id' => $consumo->id,
        'afiliado_codigo' => $consumo->afiliado,
        'afiliado_nombre' => $consumo->nombres,
        'afiliado_apellido' => $consumo->apellidos,
        'medicamento_codigo' => $consumo->cod_prestacion,
        'medicamento_descripcion' => $consumo->desc,
        'cantidad' => $consumo->cant,
        'importe_autorizado' => $consumo->imptot,
        'idaut' => $consumo->idaut,
        'fecha_entrega' => now(),
        'usuario_entrega' => CRUDBooster::myEmail(),
        'entrega_completa' => true,
    ]);

    $consumo->update([
        'estado_flujo' => 'entregado',
        'validacion_entrega_id' => $validacion->id,
        'fecha_validacion_entrega' => now(),
        'usuario_validacion_entrega' => CRUDBooster::myEmail(),
    ]);

    CRUDBooster::redirect(CRUDBooster::mainpath(), 'Validación de entrega generada exitosamente', 'success');
}
```

### 3. Actualizar Manual (5 minutos)

**Archivo:** `resources/views/manual_flujo_up.blade.php`

Reemplazar todo el contenido con los archivos:
- `MANUAL_UP_ACTUALIZADO_PARTE1.blade.php`
- `MANUAL_UP_ACTUALIZADO_PARTE2.blade.php`

### 4. Limpiar Cache (2 minutos)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 5. Verificar Implementación (3 minutos)

1. **Acceder al admin:** http://localhost:8000/admin
2. **Verificar menú:** Debe aparecer "Convenio UP" reestructurado
3. **Probar flujo:**
   - Ir a "1. Consultar Consumos UP"
   - Buscar consumo con estado PENDIENTE
   - Probar botón "Consultar Elegibilidad"

## ✅ Checklist de Verificación

- [ ] Migraciones ejecutadas sin errores
- [ ] Seeder de menús ejecutado
- [ ] Controlador actualizado con nuevos métodos
- [ ] Botones de acción actualizados
- [ ] Manual de usuario actualizado
- [ ] Cache limpiado
- [ ] Menú "Convenio UP" visible en admin
- [ ] Botones aparecen según estado del consumo
- [ ] Flujo funciona con datos de testing

## 🧪 Datos de Testing

```bash
# Afiliado de prueba
Código: 54715500
Plan: 150
TOKEN: 9999

# Medicamento de prueba
Código: 1420107
Descripción: Consulta Especializada
```

## 🔧 Troubleshooting

### Error: "Class UpValidacionEntrega not found"
```bash
composer dump-autoload
```

### Error: "Table doesn't exist"
```bash
php artisan migrate:status
php artisan migrate
```

### Error: "Method not found"
Verificar que los métodos se agregaron correctamente al controlador.

### Menús no aparecen
```bash
php artisan db:seed --class=UpMenusReestructuradosSeeder --force
```

## 📊 Resultado Esperado

Después de la implementación tendrás:

1. **Nuevo flujo:** Consultar → Elegibilidad → Aprobar → Validar Entrega
2. **Botones contextuales:** Aparecen según el estado del consumo
3. **Menús reestructurados:** Orden lógico del proceso
4. **Validaciones completas:** Registro en base de datos
5. **Manual actualizado:** Documentación del nuevo flujo

## 🎯 Próximos Pasos Opcionales

Una vez implementado lo básico, puedes agregar:

1. **Dashboard de farmacia** (archivo ya creado)
2. **Búsqueda por afiliado** (archivo ya creado)
3. **Reportes de validaciones** (archivo ya creado)
4. **Formulario completo de validación** (archivo ya creado)

---

**Tiempo total estimado:** 30 minutos  
**Complejidad:** Baja  
**Riesgo:** Mínimo (cambios no destructivos)
