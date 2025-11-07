# UP Consumos API - Documentación

## Descripción General

La API de UP Consumos permite a sistemas externos (como farmacias) consultar, filtrar y actualizar información sobre consumos de medicamentos de Unión Personal. Esta API facilita el flujo de trabajo desde la generación del consumo hasta la entrega del medicamento.

## URL Base

```
http://tu-dominio.com/api/up-consumos
```

## Autenticación

**Acceso Libre**: Esta API no requiere autenticación para facilitar la integración con sistemas externos de farmacias.

## Endpoints Disponibles

### 1. Listar Consumos

**GET** `/api/up-consumos`

Obtiene una lista paginada de consumos con filtros opcionales.

#### Parámetros de Query

| Parámetro | Tipo | Requerido | Descripción | Ejemplo |
|-----------|------|-----------|-------------|---------|
| `fecha_desde` | string | No | Fecha desde (Y-m-d) | `2024-10-01` |
| `fecha_hasta` | string | No | Fecha hasta (Y-m-d) | `2024-10-31` |
| `estado_flujo` | string | No | Estado del flujo | `pendiente`, `aprobado`, `entregado` |
| `codigo_afiliado` | string | No | Código del afiliado | `54715500` |
| `per_page` | integer | No | Elementos por página (default: 50) | `20` |

#### Estados de Flujo Disponibles

- `pendiente` - Consumo generado, pendiente de verificación
- `elegibilidad_ok` - Elegibilidad verificada exitosamente
- `elegibilidad_no` - Elegibilidad rechazada
- `aprobado` - Prestación aprobada, listo para entrega
- `rechazado` - Prestación rechazada
- `entregado` - Medicamento entregado al afiliado
- `anulado` - Transacción anulada

#### Ejemplo de Solicitud

```bash
GET /api/up-consumos?fecha_desde=2024-10-01&estado_flujo=aprobado&per_page=20
```

#### Respuesta Exitosa (200)

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "desc": "PARACETAMOL 500MG COMP X 20",
            "afiliado": "54715500",
            "cant": 1,
            "estado_flujo": "aprobado",
            "observaciones": "Listo para entrega",
            "idaut": "82502981",
            "fecha_aprobacion": "2024-10-15T14:20:00.000000Z",
            "fecha_entrega": null,
            "created_at": "2024-10-15T10:30:00.000000Z",
            "updated_at": "2024-10-15T14:20:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 20,
        "total": 45
    },
    "filters_applied": {
        "fecha_desde": "2024-10-01",
        "fecha_hasta": null,
        "estado_flujo": "aprobado",
        "codigo_afiliado": null
    }
}
```

### 2. Ver Consumo Específico

**GET** `/api/up-consumos/{id}`

Obtiene detalles completos de un consumo específico.

#### Parámetros de Ruta

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del consumo |

#### Ejemplo de Solicitud

```bash
GET /api/up-consumos/1
```

#### Respuesta Exitosa (200)

```json
{
    "success": true,
    "data": {
        "id": 1,
        "desc": "PARACETAMOL 500MG COMP X 20",
        "afiliado": "54715500",
        "cant": 1,
        "estado_flujo": "aprobado",
        "observaciones": "Medicamento listo para entrega",
        "idaut": "82502981",
        "idtran_aprobacion": "326423927",
        "fecha_aprobacion": "2024-10-15T14:20:00.000000Z",
        "usuario_aprobacion": "admin@sistema.com",
        "created_at": "2024-10-15T10:30:00.000000Z",
        "elegibilidad": {
            "id": 1,
            "status": "OK",
            "idtran": "326423927"
        },
        "autorizacion": {
            "id": 1,
            "status": "OK",
            "idaut": "82502981"
        },
        "anulacion": null
    },
    "workflow_status": {
        "puede_elg": false,
        "puede_ap": false,
        "puede_entrega": true,
        "puede_anular": true
    }
}
```

### 3. Actualizar Observaciones

**PUT** `/api/up-consumos/{id}/observaciones`

Actualiza las observaciones de un consumo específico.

#### Parámetros de Ruta

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del consumo |

#### Cuerpo de la Solicitud

```json
{
    "observaciones": "Medicamento entregado al titular. DNI verificado. Entrega en farmacia central.",
    "usuario": "Farmacia Central"
}
```

#### Campos del Cuerpo

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `observaciones` | string | Sí | Texto de las observaciones |
| `usuario` | string | No | Identificación del usuario/sistema que actualiza |

#### Ejemplo de Solicitud

```bash
PUT /api/up-consumos/1/observaciones
Content-Type: application/json

{
    "observaciones": "Medicamento entregado al titular. DNI 12345678 verificado.",
    "usuario": "Farmacia Central"
}
```

#### Respuesta Exitosa (200)

```json
{
    "success": true,
    "message": "Observaciones actualizadas correctamente",
    "data": {
        "id": 1,
        "desc": "PARACETAMOL 500MG COMP X 20",
        "observaciones": "Medicamento entregado al titular. DNI 12345678 verificado.",
        "usuario_observaciones": "Farmacia Central",
        "fecha_observaciones": "2024-10-15T16:45:00.000000Z",
        "updated_at": "2024-10-15T16:45:00.000000Z"
    }
}
```

### 4. Estadísticas

**GET** `/api/up-consumos/stats`

Obtiene estadísticas del workflow de consumos.

#### Parámetros de Query

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `fecha_desde` | string | No | Fecha desde para estadísticas |
| `fecha_hasta` | string | No | Fecha hasta para estadísticas |

#### Ejemplo de Solicitud

```bash
GET /api/up-consumos/stats?fecha_desde=2024-10-01&fecha_hasta=2024-10-31
```

#### Respuesta Exitosa (200)

```json
{
    "success": true,
    "data": {
        "total": 150,
        "por_estado": {
            "pendiente": 45,
            "elegibilidad_ok": 20,
            "aprobado": 35,
            "entregado": 40,
            "anulado": 10
        },
        "entregados_hoy": 8,
        "pendientes": 45
    }
}
```

## Códigos de Respuesta HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Solicitud exitosa |
| 400 | Error en los parámetros de la solicitud |
| 404 | Recurso no encontrado |
| 500 | Error interno del servidor |

## Estructura de Errores

```json
{
    "success": false,
    "message": "Descripción del error"
}
```

## Ejemplos de Uso Prácticos

### Caso 1: Farmacia consulta medicamentos pendientes de entrega

```bash
# Obtener medicamentos aprobados para entrega
GET /api/up-consumos?estado_flujo=aprobado&per_page=50

# Ver detalles de un medicamento específico
GET /api/up-consumos/123

# Marcar observaciones de entrega
PUT /api/up-consumos/123/observaciones
{
    "observaciones": "Entregado a titular con DNI 12345678. Farmacia Central - Turno mañana.",
    "usuario": "Farmacia Central"
}
```

### Caso 2: Sistema de reportes consulta estadísticas

```bash
# Estadísticas del mes actual
GET /api/up-consumos/stats?fecha_desde=2024-10-01&fecha_hasta=2024-10-31

# Consumos de un afiliado específico
GET /api/up-consumos?codigo_afiliado=54715500&fecha_desde=2024-10-01
```

### Caso 3: Integración con sistema de farmacia

```javascript
// Ejemplo en JavaScript
async function obtenerMedicamentosPendientes() {
    const response = await fetch('/api/up-consumos?estado_flujo=aprobado');
    const data = await response.json();
    
    if (data.success) {
        return data.data; // Array de medicamentos
    }
    throw new Error(data.message);
}

async function marcarEntregado(consumoId, observaciones) {
    const response = await fetch(`/api/up-consumos/${consumoId}/observaciones`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            observaciones: observaciones,
            usuario: 'Sistema Farmacia'
        })
    });
    
    return await response.json();
}
```

## Notas Importantes

1. **Sin Autenticación**: La API está diseñada para acceso libre para facilitar la integración.
2. **Paginación**: Todas las listas están paginadas. Use `per_page` para controlar el tamaño.
3. **Filtros de Fecha**: Use formato `Y-m-d` (ej: `2024-10-15`) para filtros de fecha.
4. **Estados del Workflow**: Respete los estados del flujo para un correcto funcionamiento.
5. **Observaciones**: Las observaciones son acumulativas y se pueden actualizar múltiples veces.

## Soporte

Para soporte técnico o consultas sobre la API, contacte al equipo de desarrollo de SISCON2.

---

**Versión**: 1.0  
**Última actualización**: Octubre 2024
