# Sistema de Gestión de Oxigenoterapia

## Descripción General

Este sistema está diseñado para gestionar el flujo completo de préstamos de equipos de oxigenoterapia, desde la creación del pedido hasta la finalización del préstamo. El sistema incluye gestión de documentos, firmas digitales, renovaciones y seguimiento de equipos.

## Flujo del Sistema

### 1. Crear Pedido de Oxigenoterapia
- **Usuario**: Auditor Convenio / Oficina Convenio
- **Acción**: Crear nueva solicitud de oxigenoterapia
- **Datos requeridos**: Datos del paciente, prescripción médica, archivos adjuntos
- **Estado inicial**: PENDIENTE

### 2. Autorizar Pedido
- **Usuario**: Auditor Convenio
- **Acción**: Revisar y autorizar la solicitud
- **Estado**: AUTORIZADO

### 3. Realizar Préstamo
- **Usuario**: Oficina Convenio
- **Acción**: Crear préstamo con equipo disponible
- **Funcionalidades**:
  - Selección de equipo disponible
  - Definición de fechas de préstamo
  - Registro de dirección de entrega
  - Generación automática de documentos
- **Estado**: EN_PRESTAMO

### 4. Gestión de Documentos
- **Tipos de documentos**:
  - Términos y Condiciones
  - Contrato de Préstamo
  - Renovación (si aplica)
  - Acta de Finalización
- **Funcionalidades**:
  - Generación automática
  - Firma digital
  - Impresión
  - Seguimiento de estado

### 5. Renovación de Préstamo
- **Usuario**: Oficina Convenio
- **Acción**: Extender el período del préstamo
- **Funcionalidades**:
  - Crear nuevo préstamo
  - Generar documentos de renovación
  - Mantener historial
- **Estado**: RENOVADO

### 6. Finalización de Préstamo
- **Usuario**: Oficina Convenio
- **Acción**: Finalizar préstamo y registrar devolución
- **Funcionalidades**:
  - Registro de fecha de devolución
  - Evaluación del estado del equipo
  - Generación de acta de finalización
  - Liberación del equipo
- **Estado**: FINALIZADO

## Estructura de Base de Datos

### Tablas Principales

#### 1. `estado_oxigenoterapia`
- Estados del flujo: PENDIENTE, AUTORIZADO, EN_PRESTAMO, RENOVADO, FINALIZADO, RECHAZADO

#### 2. `pedido_oxigenoterapia`
- Información del pedido y paciente
- Campos principales: nro_solicitud, datos del paciente, prescripción médica

#### 3. `prestamo_oxigenoterapia`
- Información del préstamo activo
- Campos principales: fechas, dirección de entrega, equipo asignado

#### 4. `documentos_prestamo`
- Gestión de documentos generados
- Tipos: TERMINOS_CONDICIONES, CONTRATO, RENOVACION, FINALIZACION

#### 5. `equipos_oxigenoterapia`
- Inventario de equipos disponibles
- Estados: DISPONIBLE, EN_USO, MANTENIMIENTO, RETIRADO

## Roles de Usuario

### 1. Auditor Convenio
- Crear pedidos de oxigenoterapia
- Autorizar solicitudes
- Revisar documentación

### 2. Oficina Convenio
- Realizar préstamos
- Gestionar renovaciones
- Finalizar préstamos
- Registrar entregas/devoluciones

### 3. Farmacia
- Ver pedidos autorizados
- Preparar equipos para entrega

### 4. Cliente
- Ver estado de su pedido
- Firmar documentos digitalmente
- Recibir notificaciones

## Funcionalidades Principales

### Gestión de Pedidos
- Creación con auto-completado de datos
- Validación de información
- Carga de archivos (prescripción, estudios)
- Generación automática de números de solicitud

### Gestión de Préstamos
- Selección de equipos disponibles
- Validación de fechas
- Registro de dirección de entrega
- Generación automática de documentos

### Gestión de Documentos
- Generación automática de contenido
- Firma digital con timestamp
- Impresión en PDF
- Seguimiento de estado

### Gestión de Equipos
- Inventario completo
- Estados de disponibilidad
- Historial de mantenimiento
- Seguimiento de uso

### Notificaciones
- Alertas de vencimiento
- Recordatorios de renovación
- Notificaciones de entrega/devolución

## Estados del Sistema

### Estados de Pedido
1. **PENDIENTE**: Solicitud creada, pendiente de autorización
2. **AUTORIZADO**: Solicitud aprobada, pendiente de préstamo
3. **EN_PRESTAMO**: Equipo entregado en préstamo
4. **RENOVADO**: Préstamo renovado
5. **FINALIZADO**: Préstamo finalizado
6. **RECHAZADO**: Solicitud rechazada

### Estados de Préstamo
- **ACTIVO**: Préstamo vigente
- **RENOVADO**: Préstamo renovado (histórico)
- **FINALIZADO**: Préstamo finalizado

### Estados de Documento
- **GENERADO**: Documento creado
- **FIRMADO**: Documento firmado
- **VENCIDO**: Documento vencido

### Estados de Equipo
- **DISPONIBLE**: Equipo disponible para préstamo
- **EN_USO**: Equipo en préstamo activo
- **MANTENIMIENTO**: Equipo en mantenimiento
- **RETIRADO**: Equipo retirado del servicio

## Instalación y Configuración

### 1. Migraciones
```bash
php artisan migrate
```

### 2. Seeders
```bash
php artisan db:seed --class=EstadoOxigenoterapiaSeeder
php artisan db:seed --class=EquiposOxigenoterapiaSeeder
```

### 3. Configuración de CRUDBooster
- Agregar módulos en el panel de administración
- Configurar permisos por rol
- Personalizar vistas según necesidades

## Archivos Principales

### Controladores
- `AdminOxigenoterapiaController.php`: Gestión principal de pedidos
- `PrestamoOxigenoterapiaController.php`: Gestión de préstamos
- `AdminEquiposOxigenoterapiaController.php`: Gestión de equipos

### Modelos
- `PedidoOxigenoterapia.php`: Modelo de pedidos
- `PrestamoOxigenoterapia.php`: Modelo de préstamos
- `DocumentosPrestamo.php`: Modelo de documentos
- `EquiposOxigenoterapia.php`: Modelo de equipos
- `EstadoOxigenoterapia.php`: Modelo de estados

### Vistas
- `oxigenoterapia/prestamo.blade.php`: Formulario de préstamo
- `oxigenoterapia/ver-prestamo.blade.php`: Vista de detalles
- `equipos/prestamos.blade.php`: Historial de préstamos por equipo

### Migraciones
- `create_estado_oxigenoterapia_table.php`
- `create_pedido_oxigenoterapia_table.php`
- `create_prestamo_oxigenoterapia_table.php`
- `create_documentos_prestamo_table.php`
- `create_equipos_oxigenoterapia_table.php`

## Mejoras Implementadas

### 1. Flujo Optimizado
- Estados claros y definidos
- Transiciones automáticas
- Validaciones en cada paso

### 2. Gestión de Documentos
- Generación automática
- Firmas digitales
- Seguimiento de estado
- Impresión en PDF

### 3. Gestión de Equipos
- Inventario completo
- Estados de disponibilidad
- Historial de uso
- Mantenimiento programado

### 4. Interfaz de Usuario
- Diseño responsive
- Filtros rápidos
- Estadísticas en tiempo real
- Notificaciones visuales

### 5. Seguridad
- Validaciones de entrada
- Transacciones de base de datos
- Control de acceso por roles
- Auditoría de cambios

## Uso del Sistema

### Para Auditor Convenio
1. Acceder a "Oxigenoterapia" en el menú
2. Crear nuevo pedido con datos del paciente
3. Revisar y autorizar solicitudes pendientes
4. Monitorear estadísticas generales

### Para Oficina Convenio
1. Ver pedidos autorizados
2. Realizar préstamos seleccionando equipos
3. Gestionar renovaciones cuando sea necesario
4. Finalizar préstamos y registrar devoluciones

### Para Farmacia
1. Ver pedidos autorizados
2. Preparar equipos para entrega
3. Registrar entregas realizadas

### Para Cliente
1. Acceder a portal de cliente
2. Ver estado de su pedido
3. Firmar documentos digitalmente
4. Recibir notificaciones de estado

## Mantenimiento

### Tareas Programadas
- Verificación de préstamos vencidos
- Notificaciones automáticas
- Limpieza de documentos antiguos
- Backup de datos

### Monitoreo
- Logs de actividad
- Estadísticas de uso
- Alertas de sistema
- Reportes de rendimiento

## Soporte Técnico

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo.

---

**Versión**: 1.0  
**Fecha**: Enero 2024  
**Desarrollado por**: Equipo de Desarrollo 