# UP Consumos API - Guía Rápida

## 🚀 Endpoints Principales

```bash
# Listar consumos con filtros
GET /api/up-consumos?fecha_desde=2024-10-01&estado_flujo=aprobado

# Ver consumo específico
GET /api/up-consumos/{id}

# Actualizar observaciones
PUT /api/up-consumos/{id}/observaciones

# Estadísticas
GET /api/up-consumos/stats
```

## 📋 Estados del Flujo

| Estado | Descripción | Siguiente Acción |
|--------|-------------|------------------|
| `pendiente` | Recién generado | Verificar elegibilidad |
| `elegibilidad_ok` | Elegible | Aprobar prestación |
| `aprobado` | Autorizado | **Entregar medicamento** |
| `entregado` | Completado | - |
| `anulado` | Cancelado | - |

## 🔍 Filtros Útiles

```bash
# Medicamentos listos para entrega
?estado_flujo=aprobado

# Por rango de fechas
?fecha_desde=2024-10-01&fecha_hasta=2024-10-31

# Por afiliado
?codigo_afiliado=54715500

# Combinado
?estado_flujo=aprobado&fecha_desde=2024-10-01&per_page=20
```

## 📝 Actualizar Observaciones

```json
PUT /api/up-consumos/123/observaciones
{
    "observaciones": "Entregado a titular. DNI verificado.",
    "usuario": "Farmacia Central"
}
```

## 📊 Respuesta Típica

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "desc": "PARACETAMOL 500MG",
            "afiliado": "54715500",
            "estado_flujo": "aprobado",
            "observaciones": "Listo para entrega",
            "idaut": "82502981"
        }
    ],
    "pagination": {
        "current_page": 1,
        "total": 45
    }
}
```

## ⚡ Casos de Uso Rápidos

### Farmacia - Consultar Pendientes
```bash
curl "http://tu-dominio.com/api/up-consumos?estado_flujo=aprobado"
```

### Marcar como Entregado
```bash
curl -X PUT "http://tu-dominio.com/api/up-consumos/123/observaciones" \
  -H "Content-Type: application/json" \
  -d '{"observaciones":"Entregado","usuario":"Farmacia"}'
```

### Dashboard - Estadísticas
```bash
curl "http://tu-dominio.com/api/up-consumos/stats?fecha_desde=2024-10-01"
```

## 🎯 Variables Postman

```json
{
    "base_url": "http://localhost:8000",
    "consumo_id": "1"
}
```
