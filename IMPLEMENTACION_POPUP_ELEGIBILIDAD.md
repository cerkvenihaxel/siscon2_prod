# Implementación del Popup de Elegibilidad en Consumos UP V2

## Resumen de Cambios

Se ha implementado la funcionalidad solicitada para incluir el formulario de consulta de elegibilidad directamente en el popup de detalle de consumos, junto con un botón para aprobar directamente el consumo.

## Archivos Modificados

### 1. `/resources/views/consumos_up_v2/index.blade.php`

**Cambios realizados:**
- Agregado meta tag CSRF para las peticiones AJAX
- Modificada la función `verDetalle()` para incluir el panel de elegibilidad
- Agregada función `getFormularioElegibilidad()` que genera el HTML del formulario
- Agregada función `toggleElegibilidad()` para mostrar/ocultar el panel
- Agregada función `configurarFormularioElegibilidad()` para manejar el submit del formulario
- Modificada función `getBotonesAccion()` para incluir botones de "Consultar Elegibilidad" y "Aprobar Directo"
- Agregada función `aprobarDesdeElegibilidad()` para aprobar después de verificar elegibilidad
- Modificada función `ejecutarAccion()` para incluir la acción "aprobar_directo"

### 2. `/app/Http/Controllers/ConsumosUpV2Controller.php`

**Cambios realizados:**
- Agregado método `aprobarDirecto()` que permite aprobar un consumo sin verificar elegibilidad
- El método actualiza el estado a 'aprobado' y genera un IDAUTH con prefijo "AUTH_DIRECTO_"
- Incluye observaciones indicando que fue una aprobación directa

### 3. `/routes/web.php`

**Cambios realizados:**
- Agregada ruta POST `/consumos_up_v2/{id}/aprobar-directo` que apunta al método `aprobarDirecto`

## Funcionalidades Implementadas

### 1. Panel de Elegibilidad en el Popup
- Se agregó un panel colapsible en el modal de detalle del consumo
- El panel incluye el mismo formulario que está disponible en `/admin/up_elegibilidad`
- Campos incluidos:
  - Código de afiliado (prellenado y readonly)
  - TOKEN (valor por defecto: 9999)
  - Plan (valor por defecto: 150)
  - Versión Credencial (valor por defecto: 45)
- Botones de "Consultar Elegibilidad" y "Limpiar"

### 2. Consulta de Elegibilidad AJAX
- La consulta se realiza mediante AJAX al endpoint existente `/admin/up_elegibilidad/consultar-elegibilidad`
- Los resultados se muestran en el mismo modal sin necesidad de redirección
- Si la elegibilidad es exitosa (status = 'OK'), se muestra un botón adicional para aprobar el consumo

### 3. Botón de Aprobación Directa
- Para consumos en estado 'pendiente' o 'elegibilidad_no'
- Permite aprobar directamente sin verificar elegibilidad
- Genera un IDAUTH con prefijo "AUTH_DIRECTO_" para identificar estas aprobaciones
- Incluye observaciones automáticas indicando que fue una aprobación directa

### 4. Flujo de Trabajo Mejorado
- **Estado Pendiente/Elegibilidad No:**
  - Botón "Consultar Elegibilidad" (abre el panel)
  - Botón "Aprobar Directo" (aprueba sin verificar)
  
- **Después de Consultar Elegibilidad Exitosa:**
  - Botón "Aprobar Consumo" (aparece automáticamente)
  
- **Estado Elegibilidad OK:**
  - Botón "Aprobar Prestación" (flujo normal)
  
- **Estado Aprobado:**
  - Botón "Generar Validación" (flujo normal)

## Uso de la Funcionalidad

1. **Acceder al popup:** Hacer clic en cualquier fila de la tabla de consumos
2. **Consultar elegibilidad:** 
   - Hacer clic en "Consultar Elegibilidad" para expandir el panel
   - Ajustar los valores si es necesario (TOKEN, Plan, VerCred)
   - Hacer clic en "Consultar Elegibilidad"
   - Si es exitosa, aparecerá un botón "Aprobar Consumo"
3. **Aprobación directa:** Hacer clic en "Aprobar Directo" para aprobar sin verificar elegibilidad
4. **Seguir el flujo normal:** Una vez aprobado, usar "Generar Validación" para completar el proceso

## Ventajas de la Implementación

- **Integración completa:** El formulario de elegibilidad está integrado directamente en el popup
- **Sin redirecciones:** Todo el proceso se maneja en el mismo modal
- **Flexibilidad:** Permite tanto verificar elegibilidad como aprobar directamente
- **Trazabilidad:** Las aprobaciones directas se identifican claramente en la base de datos
- **Experiencia de usuario mejorada:** Menos clics y navegación más fluida

## Consideraciones Técnicas

- Se mantiene la compatibilidad con el sistema existente de elegibilidad
- Las consultas AJAX utilizan los mismos endpoints que el sistema principal
- Se incluyen validaciones de estado para evitar acciones incorrectas
- Los mensajes de confirmación y error se muestran usando SweetAlert
- El código JavaScript está organizado en funciones modulares para facilitar el mantenimiento
