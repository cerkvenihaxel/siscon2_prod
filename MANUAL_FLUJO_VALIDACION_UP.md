# Manual de Usuario - Flujo de Validación Unión Personal

## 📋 Índice

1. [Introducción al Flujo](#introducción-al-flujo)
2. [Paso 1: Buscar Consumo](#paso-1-buscar-consumo)
3. [Paso 2: Verificar Elegibilidad (ELG)](#paso-2-verificar-elegibilidad-elg)
4. [Paso 3: Aprobar Prestación (AP)](#paso-3-aprobar-prestación-ap)
5. [Paso 4: Validar Entrega](#paso-4-validar-entrega)
6. [Paso 5: Anular (si es necesario)](#paso-5-anular-si-es-necesario)
7. [Estados del Sistema](#estados-del-sistema)
8. [Resolución de Errores](#resolución-de-errores)
9. [Datos de Prueba](#datos-de-prueba)

---

## 🔄 Introducción al Flujo

El sistema de validación con Unión Personal permite procesar consumos de afiliados a través de un flujo controlado de 4 pasos principales:

```
📋 Buscar Consumo → 🔍 Elegibilidad (ELG) → ✅ Aprobación (AP) → 🚚 Validar Entrega
```

### Características del Flujo:
- **Secuencial**: Cada paso debe completarse antes del siguiente
- **Trazable**: Todas las transacciones quedan registradas
- **Reversible**: Se puede anular si es necesario
- **Automático**: Comunicación directa con Unión Personal

---

## 📋 Paso 1: Buscar Consumo

### Objetivo
Localizar el consumo específico que necesita ser validado.

### Procedimiento
1. **Acceder al módulo**: Vaya a `Consumos UP` desde el menú principal
2. **Aplicar filtros**:
   - **Por afiliado**: Ingrese el número de afiliado
   - **Por fecha**: Seleccione rango de fechas
   - **Por estado**: Filtre por estado del flujo
3. **Identificar consumo**: Busque consumos con estado `PENDIENTE`
4. **Verificar datos**:
   - ✅ Número de afiliado correcto
   - ✅ Prestación solicitada
   - ✅ Cantidad
   - ✅ Importe

### Estados Iniciales Válidos
- `PENDIENTE`: Consumo sin procesar
- `ELEGIBILIDAD NO`: Para reintentar ELG

---

## 🔍 Paso 2: Verificar Elegibilidad (ELG)

### ¿Qué hace?
Verifica si el afiliado tiene derecho a recibir la prestación según:
- Estado de afiliación
- Plan contratado
- Cobertura de la prestación
- Límites y restricciones

### Procedimiento
1. **Ejecutar ELG**: Clic en botón `Verificar Elegibilidad (ELG)`
2. **Proceso automático**:
   - Consulta a Unión Personal vía SOAP
   - Validación de datos del afiliado
   - Verificación de cobertura
   - Actualización de estado

### Resultados Posibles

#### ✅ Elegibilidad Exitosa
- **Estado**: `ELEGIBILIDAD OK`
- **Se genera**: IDTRAN de elegibilidad
- **Siguiente paso**: Aprobar Prestación (AP)
- **Datos obtenidos**: Información completa del afiliado

#### ❌ Elegibilidad Rechazada
- **Estado**: `ELEGIBILIDAD NO`
- **Motivos comunes**:
  - Afiliado no encontrado
  - Plan no cubre la prestación
  - Afiliado dado de baja
  - Límites agotados
- **Acción**: Revisar datos y reintentar

---

## ✅ Paso 3: Aprobar Prestación (AP)

### ¿Qué hace?
Autoriza específicamente la prestación y genera el número de autorización (IDAUT) necesario para facturación.

### Prerrequisitos
- ⚠️ **Obligatorio**: Elegibilidad debe ser exitosa
- Estado del consumo: `ELEGIBILIDAD OK`

### Procedimiento
1. **Ejecutar AP**: Clic en botón `Aprobar Prestación (AP)`
2. **Proceso automático**:
   - Solicitud de autorización a UP
   - Cálculo de importes y copagos
   - Generación de IDAUT
   - Actualización de estado

### Resultados Posibles

#### ✅ Aprobación Exitosa
- **Estado**: `APROBADO`
- **Se genera**: 
  - IDTRAN de aprobación
  - IDAUT (número de autorización)
- **Siguiente paso**: Validar Entrega
- **Opciones**: También puede Anular si es necesario

#### ❌ Aprobación Rechazada
- **Estado**: `RECHAZADO`
- **Motivos comunes**:
  - Prestación no autorizada por UP
  - Límites específicos agotados
  - Requiere autorización especial
- **Acción**: Fin del flujo, no se puede continuar

---

## 🚚 Paso 4: Validar Entrega

### ¿Qué hace?
Confirma que la prestación fue efectivamente entregada al paciente. Es un control interno que no requiere comunicación con UP.

### Prerrequisitos
- ⚠️ **Obligatorio**: Aprobación debe ser exitosa
- Estado del consumo: `APROBADO`

### Procedimiento
1. **Verificar entrega real**: Confirmar que se entregó al paciente
2. **Ejecutar validación**: Clic en botón `Validar Entrega`
3. **Proceso interno**:
   - Marca como entregado
   - Registra fecha y usuario
   - Completa el flujo

### Resultado
- **Estado**: `ENTREGADO`
- **Flujo**: Completado exitosamente
- **Opciones**: Aún puede Anular si es necesario

---

## ❌ Paso 5: Anular (si es necesario)

### ⚠️ IMPORTANTE
**La anulación es IRREVERSIBLE**. Use solo en casos excepcionales.

### Casos Válidos para Anular
- ❌ Error en la prestación autorizada
- ❌ Paciente no retiró la prestación
- ❌ Error administrativo
- ❌ Solicitud expresa del paciente

### Prerrequisitos
Estados válidos para anular:
- `APROBADO`: Autorizado pero no entregado
- `ENTREGADO`: Ya entregado al paciente

### Procedimiento
1. **Confirmar necesidad**: Verificar que realmente se debe anular
2. **Ejecutar ATR**: Clic en botón `Anular Transacción (ATR)`
3. **Confirmar acción**: Aparece advertencia de irreversibilidad
4. **Proceso automático**:
   - Solicitud de anulación a UP
   - Anulación del IDAUT
   - Registro del motivo
   - Actualización de estado

### Resultado
- **Estado**: `ANULADO`
- **Se genera**: IDTRAN de anulación
- **Efecto**: Autorización queda sin efecto
- **Importante**: No se puede revertir

---

## 🎯 Estados del Sistema

| Estado | Badge | Descripción | Acciones Disponibles | Siguiente Paso |
|--------|-------|-------------|---------------------|----------------|
| `PENDIENTE` | ![Pendiente](https://img.shields.io/badge/-PENDIENTE-lightgrey) | Consumo inicial sin procesar | Verificar Elegibilidad | ELG |
| `ELEGIBILIDAD OK` | ![ELG OK](https://img.shields.io/badge/-ELEGIBILIDAD%20OK-blue) | Afiliado elegible | Aprobar Prestación | AP |
| `ELEGIBILIDAD NO` | ![ELG NO](https://img.shields.io/badge/-ELEGIBILIDAD%20NO-red) | Afiliado no elegible | Verificar Elegibilidad | Revisar datos |
| `APROBADO` | ![Aprobado](https://img.shields.io/badge/-APROBADO-green) | Prestación autorizada | Validar Entrega, Anular | Entrega |
| `RECHAZADO` | ![Rechazado](https://img.shields.io/badge/-RECHAZADO-red) | Prestación no autorizada | Ninguna | Fin |
| `ENTREGADO` | ![Entregado](https://img.shields.io/badge/-ENTREGADO-blue) | Prestación entregada | Anular | Completado |
| `ANULADO` | ![Anulado](https://img.shields.io/badge/-ANULADO-orange) | Transacción anulada | Ninguna | Fin |

---

## 🔧 Resolución de Errores

### Error: "Afiliado no encontrado"

**Causas posibles:**
- Número de afiliado incorrecto
- Afiliado dado de baja
- Error de tipeo

**Solución:**
1. Verificar el número de afiliado con el paciente
2. Consultar credencial física
3. Verificar en sistema de UP directamente

### Error: "Prestación no autorizada"

**Causas posibles:**
- Prestación no incluida en el plan
- Límites de cobertura agotados
- Requiere autorización previa especial

**Solución:**
1. Revisar cobertura del plan del afiliado
2. Contactar a UP para autorización especial
3. Considerar prestación alternativa cubierta

### Error: "Error de conexión SOAP"

**Causas posibles:**
- Problemas de conectividad a internet
- Servicio de UP temporalmente no disponible
- Credenciales SOAP incorrectas

**Solución:**
1. Verificar conexión a internet
2. Reintentar en unos minutos
3. Contactar soporte técnico si persiste

### Error: "TOKEN expirado"

**Causas posibles:**
- TOKEN de credencial digital vencido (5 minutos)
- TOKEN incorrecto

**Solución:**
1. Solicitar nuevo TOKEN al paciente
2. Usar Plan + VerCred como alternativa
3. Para testing, usar TOKEN `9999` (no expira)

---

## 🧪 Datos de Prueba

### ⚠️ Solo para Ambiente de Testing

Los siguientes datos funcionan únicamente en el ambiente de pruebas de Unión Personal:

### Afiliados de Prueba

| Código Afiliado | Plan | Versión Credencial | Nombre | Resultado |
|-----------------|------|-------------------|---------|-----------|
| `54715500` | 150 (Accord) | 45 | AFILIADO PRUEBA | ✅ Siempre OK |
| `54715300` | 2 (Básico) | 31 | PRUEBA1 | ✅ Siempre OK |

### Prestaciones de Prueba

| Código | Descripción | Tipo | Resultado |
|--------|-------------|------|-----------|
| `1420107` | Consulta Especializada | P | ✅ Siempre OK |
| `1420101` | Consulta Médica | P | ✅ Siempre OK |

### TOKEN de Desarrollo

- **TOKEN**: `9999`
- **Características**: 
  - No expira
  - Siempre devuelve resultados exitosos
  - Solo para ambiente de testing

### Credenciales SOAP (Testing)

- **Usuario**: `8888`
- **Password**: `7777`
- **Prestador ID**: `8888`
- **Aplicación**: `HMS_CAWEB`

---

## 📞 Soporte

### Contacto Técnico
- **Sistema**: SISCON2
- **Módulo**: Unión Personal
- **Documentación**: Este manual + documentación técnica en `/documentacion up/`

### Logs del Sistema
- **Ubicación**: `storage/logs/laravel.log`
- **Transacciones SOAP**: Tabla `up_transacciones_soap`
- **Historial**: Botón "Ver Historial SOAP" en cada consumo

---

## 📝 Notas Importantes

1. **Secuencia obligatoria**: No se puede saltar pasos en el flujo
2. **Trazabilidad completa**: Todas las acciones quedan registradas
3. **Anulaciones irreversibles**: Usar con extrema precaución
4. **Ambiente de testing**: Usar datos de prueba proporcionados
5. **Comunicación SOAP**: Todas las transacciones ELG, AP y ATR se comunican con UP
6. **Validación de entrega**: Es interna del sistema, no requiere comunicación con UP

---

**Versión del Manual**: 1.0  
**Fecha**: Octubre 2025  
**Sistema**: SISCON2 - Módulo Unión Personal
