# Implementación Transacción AP (Consumo de prestaciones)

## 📋 Resumen
Se ha implementado una nueva pantalla "Transacción AP (Consumo de prestaciones)" basada en el flujo existente de Convenio UP, con validación de login y middleware de `consumos_up_v2`.

## 🏗️ Arquitectura Implementada

### 1. Controlador
**Archivo:** `app/Http/Controllers/TransaccionApController.php`

**Métodos principales:**
- `index()` - Vista principal
- `consultarElegibilidad()` - Consulta ELG en tiempo real
- `procesarTransaccionAp()` - Envío de XML y procesamiento de respuesta
- `buscarArticulos()` - Lazy loading de medicamentos desde articulosZafiro
- `guardarConsumo()` - Guardado en tabla up_consumos

### 2. Vista Principal
**Archivo:** `resources/views/transaccion_ap/index.blade.php`

**Secciones implementadas:**
1. **Consulta Rápida de Elegibilidad (ELG)**
   - Input para código de afiliado
   - Select para tipo de documento
   - Consulta en tiempo real con jQuery
   - Respuesta inmediata OK/NO

2. **Transacción AP**
   - Input para código de prestación
   - Input para cantidad
   - Envío de XML según especificación
   - Manejo de respuestas OK/NO

3. **Carga de Medicamentos** (solo visible si STATUS=OK)
   - Select2 con lazy loading
   - Búsqueda en tabla articulosZafiro
   - Filtro por nro_registro_alfabeta
   - Tabla dinámica de medicamentos agregados
   - Guardado en up_consumos

### 3. Rutas
**Archivo:** `routes/web.php`

```php
// Transacción AP (Consumo de prestaciones)
Route::middleware(['CBBackend'])->group(function () {
    Route::get('/admin/transaccion-ap', 'App\Http\Controllers\TransaccionApController@index');
    Route::post('/transaccion-ap/elegibilidad', 'App\Http\Controllers\TransaccionApController@consultarElegibilidad');
    Route::post('/transaccion-ap/procesar', 'App\Http\Controllers\TransaccionApController@procesarTransaccionAp');
    Route::get('/transaccion-ap/buscar-articulos', 'App\Http\Controllers\TransaccionApController@buscarArticulos');
    Route::post('/transaccion-ap/guardar-consumo', 'App\Http\Controllers\TransaccionApController@guardarConsumo');
});
```

### 4. Servicio SOAP
**Archivo:** `app/Services/UnionPersonalSoapService.php`

**Métodos agregados:**
- `consultarElegibilidad()` - Wrapper simplificado para ELG
- `enviarTransaccionAP()` - Wrapper simplificado para AP

## 📊 Flujo de Datos

### XML de Solicitud AP
```xml
<SOLICITUD>
    <EMISOR>
        <ID>hms-ca-web.osup.com.ar</ID>
        <PROT>CA_V20</PROT>
        <MSGID>000041709</MSGID>
        <TER>Web</TER>
        <APP>HMS_CAWEB</APP>
        <TIME>2025-11-6T17:04:35</TIME>
    </EMISOR>
    <SEGURIDAD>
        <TIPOAUT>U</TIPOAUT>
        <TIPOCON>PRES</TIPOCON>
        <USRID>149093</USRID>
        <USRPASS>DIAB</USRPASS>
    </SEGURIDAD>
    <OPER>
        <TIPO>AP</TIPO>
        <IDASEG>UP</IDASEG>
        <IDPRESTADOR>149093</IDPRESTADOR>
        <FECHA>2025-11-06</FECHA>
    </OPER>
    <PID>
        <TIPOID>CODIGO</TIPOID>
        <TOKEN>9999</TOKEN>
        <ID>54715500</ID>
        <VERIFID>AUTO</VERIFID>
    </PID>
    <CONTEXTO>
        <TIPO>A</TIPO>
    </CONTEXTO>
    <PR>
        <TIPO>M</TIPO>
        <CANT>1</CANT>
        <ID>4048304</ID>
    </PR>
</SOLICITUD>
```

### Respuesta XML (Ejemplo ERROR)
```xml
<RESPUESTA>
    <IDTRAN>1343425592</IDTRAN>
    <OPER>AP</OPER>
    <FECHAOPER>2025-11-06</FECHAOPER>
    <STATUS>NO</STATUS>
    <RSPCODG>MSGXML_1052</RSPCODG>
    <RSPMSGG>AP NO APROBADA</RSPMSGG>
    <VERIFID>AUTO</VERIFID>
    <AFICODIGO>54715500</AFICODIGO>
    <AFIPLAN>3</AFIPLAN>
    <AFIPLANNOM>ACCORD DORADO</AFIPLANNOM>
    <AFIAPE>PRUEBAS</AFIAPE>
    <AFINOM>TITULAR</AFINOM>
    <AFIAFIL>OBL</AFIAFIL>
    <AFIDOM>CHIVILCOY BUENOS AIRES</AFIDOM>
    <IDAUT>128375186</IDAUT>
    <SEXO>F</SEXO>
    <AFIFECNAC>01/04/1962</AFIFECNAC>
    <AFIAPL>ACCORD</AFIAPL>
    <AFITIPODOC>2</AFITIPODOC>
    <AFINRODOC>6915095</AFINRODOC>
    <PR>
        <TIPO>P</TIPO>
        <ID>4048304</ID>
        <DESCRIPCION>M660G STEEL MONO</DESCRIPCION>
        <STATUS>NO</STATUS>
        <RSPCODP>CAUD 325</RSPCODP>
        <RSPMSGP>CODIGO NO CONVENIDO</RSPMSGP>
        <RSPMSGPADIC>La prestación o grupo de prestaciones 04048304 no esta pactada en el convenio 149027 para el afiliado 54715500 al 06/11/2025</RSPMSGPADIC>
        <CANT>1</CANT>
    </PR>
</RESPUESTA>
```

## 🔄 Manejo de Estados

### STATUS = NO
- Muestra popup modal con error
- Incluye código, mensaje y detalles adicionales
- No permite continuar con carga de medicamentos

### STATUS = OK
- Muestra mensaje de éxito con IDTRAN e IDAUT
- Habilita sección de carga de medicamentos
- Permite búsqueda y selección de artículos

## 🗄️ Base de Datos

### Tabla: articulosZafiro
**Campos utilizados:**
- `nro_registro_alfabeta` - Código de prestación
- `descripcion` - Descripción del medicamento
- `precio_venta` - Precio del artículo

### Tabla: up_consumos
**Campos agregados:**
- `precio` - Precio del medicamento
- `idtran` - ID de transacción
- `usuario_carga` - Usuario que cargó el consumo

## 🎯 Funcionalidades Clave

### 1. Lazy Loading de Medicamentos
- Select2 con búsqueda dinámica
- Filtro por código de prestación
- Mínimo 2 caracteres para búsqueda
- Cache habilitado

### 2. Validación de Flujo
- Elegibilidad obligatoria antes de transacción
- Validación de campos requeridos
- Manejo de errores con mensajes descriptivos

### 3. Interfaz Responsiva
- Bootstrap 4
- Modales para errores
- Tablas dinámicas
- Feedback visual inmediato

## 🚀 Acceso

**URL Principal:** `/admin/transaccion-ap`

**Middleware:** `CBBackend` (mismo que consumos_up_v2)

## 📝 Notas Técnicas

1. **Compatibilidad:** Reutiliza la infraestructura existente de UP
2. **Seguridad:** Middleware de autenticación obligatorio
3. **Performance:** Lazy loading para optimizar consultas
4. **Logging:** Integrado con sistema de logs SOAP existente
5. **Escalabilidad:** Estructura modular para futuras extensiones

## ✅ Testing

Para probar la implementación:
1. Acceder a `/test_transaccion_ap.php` para ver resumen
2. Acceder a `/admin/transaccion-ap` para usar la funcionalidad
3. Verificar logs en tabla `up_transacciones_soap`

## 🔧 Mantenimiento

- Logs automáticos de todas las transacciones
- Manejo de errores con rollback
- Validaciones en frontend y backend
- Estructura preparada para auditoría
