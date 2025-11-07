# Flujo Integrado de Trabajo - Consumos Unión Personal

## 📋 Resumen

Se ha implementado un **flujo de trabajo integrado** en el módulo de **Consumos UP** que permite procesar consumos de afiliados desde la búsqueda hasta la validación de entrega, utilizando las transacciones SOAP de Unión Personal.

## 🔄 Flujo de Trabajo

```
┌─────────────────┐
│  1. Buscar      │
│  Consumo por    │ → Estado: PENDIENTE
│  Afiliado       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  2. Verificar   │
│  Elegibilidad   │ → ELG SOAP → Estado: ELEGIBILIDAD_OK / ELEGIBILIDAD_NO
│  (ELG)          │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  3. Aprobar     │
│  Prestación     │ → AP SOAP → Estado: APROBADO / RECHAZADO
│  (AP)           │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  4. Validar     │
│  Entrega        │ → Sin SOAP → Estado: ENTREGADO
│                 │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  5. Anular      │
│  (Opcional)     │ → ATR SOAP → Estado: ANULADO
│  (ATR)          │
└─────────────────┘
```

## 🎯 Estados del Flujo

| Estado | Descripción | Color Badge | Siguiente Acción Disponible |
|--------|-------------|-------------|------------------------------|
| **pendiente** | Consumo sin procesar | Gris | Verificar Elegibilidad (ELG) |
| **elegibilidad_ok** | ELG exitoso | Azul | Aprobar Prestación (AP) |
| **elegibilidad_no** | ELG rechazado | Rojo | Verificar Elegibilidad (ELG) nuevamente |
| **aprobado** | AP exitoso | Verde | Validar Entrega |
| **rechazado** | AP rechazado | Rojo | - |
| **entregado** | Entrega validada | Azul Oscuro | Anular (ATR) si es necesario |
| **anulado** | Transacción anulada | Amarillo | - |

## 🗄️ Cambios en la Base de Datos

### Nuevos Campos en `up_consumos`

```sql
-- Estado del flujo de trabajo
estado_flujo ENUM('pendiente', 'elegibilidad_ok', 'elegibilidad_no', 'aprobado', 'rechazado', 'entregado', 'anulado')

-- Referencias a transacciones SOAP
elegibilidad_id BIGINT (FK a up_elegibilidad)
autorizacion_id BIGINT (FK a up_autorizacion_previa)
anulacion_id BIGINT (FK a up_anulaciones)

-- IDs de transacciones SOAP
idtran_elegibilidad VARCHAR(50)
idtran_aprobacion VARCHAR(50)
idaut VARCHAR(50)

-- Control de fechas
fecha_elegibilidad DATETIME
fecha_aprobacion DATETIME
fecha_entrega DATETIME
fecha_anulacion DATETIME

-- Control de usuarios
usuario_elegibilidad VARCHAR(100)
usuario_aprobacion VARCHAR(100)
usuario_entrega VARCHAR(100)
usuario_anulacion VARCHAR(100)

-- Observaciones
observaciones_flujo TEXT
```

## 📝 Uso del Sistema

### Paso 1: Buscar Consumo

1. Ir a **Unión Personal → Consumos UP**
2. Usar los filtros para buscar por:
   - Número de afiliado
   - Fecha
   - Estado
3. Localizar el consumo a procesar

### Paso 2: Verificar Elegibilidad (ELG)

**Cuándo:** El consumo está en estado **PENDIENTE** o **ELEGIBILIDAD_NO**

**Acción:**
1. Click en el botón **"Verificar Elegibilidad (ELG)"** (azul)
2. El sistema ejecutará automáticamente:
   - Transacción SOAP ELG con Unión Personal
   - Verificación de datos del afiliado
   - Creación de registro en `up_elegibilidad`
   - Autocreación del afiliado si no existe
3. Si es exitoso → Estado cambia a **ELEGIBILIDAD_OK**
4. Si falla → Estado cambia a **ELEGIBILIDAD_NO**

**Resultado:**
- Se obtiene IDTRAN de elegibilidad
- Se valida que el afiliado existe y está activo
- Se registra la transacción completa

### Paso 3: Aprobar Prestación (AP)

**Cuándo:** El consumo está en estado **ELEGIBILIDAD_OK**

**Acción:**
1. Click en el botón **"Aprobar Prestación (AP)"** (verde)
2. El sistema ejecutará automáticamente:
   - Transacción SOAP AP con Unión Personal
   - Autorización de la prestación/medicamento
   - Creación de registro en `up_autorizacion_previa`
3. Si es exitoso → Estado cambia a **APROBADO**
4. Si falla → Estado cambia a **RECHAZADO**

**Resultado:**
- Se obtiene IDAUT (ID de Autorización)
- Se obtiene IDTRAN de aprobación
- Se registran los importes autorizados

### Paso 4: Validar Entrega

**Cuándo:** El consumo está en estado **APROBADO**

**Acción:**
1. Click en el botón **"Validar Entrega"** (azul oscuro)
2. El sistema marca el consumo como entregado
3. Estado cambia a **ENTREGADO**

**Nota:** Esta acción NO ejecuta transacción SOAP, solo marca internamente que la prestación fue entregada al afiliado.

### Paso 5: Anular Transacción (ATR) - Opcional

**Cuándo:** El consumo está en estado **APROBADO** o **ENTREGADO**

**Acción:**
1. Click en el botón **"Anular Transacción (ATR)"** (rojo)
2. El sistema ejecutará automáticamente:
   - Transacción SOAP ATR con Unión Personal
   - Anulación de la autorización previa
   - Creación de registro en `up_anulaciones`
3. Estado cambia a **ANULADO**

**Resultado:**
- Se obtiene IDTRAN de anulación
- La transacción queda anulada en Unión Personal

## 🎨 Interfaz Visual

### Indicadores de Color en las Filas

- **Verde:** Entregado (flujo completado)
- **Azul:** Aprobado (pendiente de entrega)
- **Rojo:** Rechazado o Elegibilidad negativa
- **Amarillo:** Anulado

### Botones de Acción Dinámicos

Los botones aparecen/desaparecen según el estado:

| Estado | Botones Visibles |
|--------|-----------------|
| pendiente | ✅ Verificar Elegibilidad |
| elegibilidad_ok | ✅ Aprobar Prestación |
| aprobado | ✅ Validar Entrega<br>⚠️ Anular Transacción |
| entregado | ⚠️ Anular Transacción |
| rechazado | - |
| anulado | - |

## 🔍 Trazabilidad

Cada acción del flujo registra:

### En la tabla `up_consumos`:
- Estado actual (`estado_flujo`)
- IDs de transacciones SOAP
- Fechas de cada operación
- Usuario que ejecutó cada acción
- IDs de relación con tablas específicas

### En tablas específicas:
- `up_elegibilidad`: Detalle completo de ELG
- `up_autorizacion_previa`: Detalle completo de AP
- `up_anulaciones`: Detalle completo de ATR

### En tabla de trazabilidad:
- `up_transacciones_soap`: Registro completo de todas las transacciones SOAP (request/response XML)

## 📊 Ejemplo de Flujo Completo

### Caso: Procesamiento de Consumo de Medicamento

**Datos del Consumo:**
- Afiliado: 54715500
- Medicamento: 1420107 (Consulta Especializada)
- Plan: 150
- Cantidad: 1

**Paso a Paso:**

1. **Buscar el consumo**
   - Filtrar por afiliado: `54715500`
   - Estado inicial: `pendiente`

2. **Click en "Verificar Elegibilidad (ELG)"**
   - Sistema envía SOAP ELG a UP
   - Respuesta: `STATUS=OK`, `IDTRAN=1114XXXXXX`
   - Estado cambia a: `elegibilidad_ok`
   - Registro creado en `up_elegibilidad` (id=1)
   - Campo `elegibilidad_id` = 1

3. **Click en "Aprobar Prestación (AP)"**
   - Sistema envía SOAP AP a UP
   - Respuesta: `STATUS=OK`, `IDAUT=117XXXXXX`, `IDTRAN=1114YYYYYY`
   - Estado cambia a: `aprobado`
   - Registro creado en `up_autorizacion_previa` (id=1)
   - Campo `autorizacion_id` = 1
   - Campo `idaut` = 117XXXXXX

4. **Click en "Validar Entrega"**
   - Estado cambia a: `entregado`
   - No hay transacción SOAP
   - Se registra fecha y usuario de entrega

5. **Si es necesario anular:**
   - Click en "Anular Transacción (ATR)"
   - Sistema envía SOAP ATR a UP usando IDTRAN de aprobación
   - Respuesta: `STATUS=OK`, `IDTRAN=1114ZZZZZZ`
   - Estado cambia a: `anulado`
   - Registro creado en `up_anulaciones` (id=1)

## 💡 Ventajas del Flujo Integrado

1. **Centralizado:** Todo el flujo desde un solo módulo
2. **Trazable:** Registro completo de cada paso
3. **Visual:** Estados con colores y botones dinámicos
4. **Seguro:** Validaciones de estado antes de cada acción
5. **Auditable:** Usuario y fecha de cada operación
6. **Relacional:** Vínculos con tablas específicas de cada transacción

## 🔧 Configuración

### Datos de Testing (.env)

```bash
UP_AMBIENTE=test
SOAP_UP_ENDPOINT=http://181.13.241.19:7002/cawsTest/Servicios
SOAP_UP_WSDL=http://181.13.241.19:7002/cawsTest/Servicios?wsdl
SOAP_UP_PRESTADOR_ID=8888
SOAP_UP_USER_ID=8888
SOAP_UP_USER_PASS=7777
UP_AUTOCREAR_AFILIADOS=true
```

### Afiliados de Testing

- Código: `54715500`
- Plan: `150`
- TOKEN: `9999` (opcional, no usado en flujo automático)

## ⚠️ Consideraciones Importantes

1. **No se puede saltar pasos:** El flujo debe seguirse en orden (ELG → AP → Entrega)

2. **Validaciones automáticas:** El sistema valida el estado antes de permitir cada acción

3. **Reintentos de ELG:** Si ELG falla, se puede reintentar haciendo click nuevamente

4. **Datos del CSV:** Los consumos se importan desde CSV con estado `pendiente` por defecto

5. **Plan y VerCred:** Se toman del consumo. Si no están disponibles, usar datos de testing

6. **Anulación irreversible:** Una vez anulado, no se puede revertir

## 🆘 Troubleshooting

### "Este consumo no puede ejecutar ELG en su estado actual"
- **Causa:** El consumo no está en estado `pendiente` o `elegibilidad_no`
- **Solución:** Verificar el estado actual del consumo

### "Debe verificar elegibilidad antes de aprobar"
- **Causa:** Intento de ejecutar AP sin ELG exitoso
- **Solución:** Ejecutar primero ELG hasta obtener estado `elegibilidad_ok`

### "No hay transacción aprobada para anular"
- **Causa:** El consumo no tiene IDTRAN de aprobación
- **Solución:** Ejecutar primero AP exitosamente

### Error SOAP
- **Causa:** Problemas de conectividad o credenciales
- **Solución:** Verificar configuración en `.env` y logs en `storage/logs/laravel.log`

## 📚 Documentación Relacionada

- `IMPLEMENTACION_ELG_AP_ATR.md` - Módulos independientes ELG, AP, ATR
- `manual up.md` - Manual completo de testing SOAP
- `UNION_PERSONAL_README.md` - Documentación general de la integración

---

**Fecha de Implementación:** 31 de Octubre 2025
**Versión:** 2.0 - Flujo Integrado
**Proyecto:** SISCON2_PROD
