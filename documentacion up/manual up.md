
# Manual de Pruebas - Integración SOAP Unión Personal

  

## Índice

  

1. [Introducción](#introducción)

2. [Datos de Prueba](#datos-de-prueba)

3. [Configuración de Entornos](#configuración-de-entornos)

4. [Casos de Prueba](#casos-de-prueba)

5. [Pruebas por Tipo de Transacción](#pruebas-por-tipo-de-transacción)

6. [Validación de Resultados](#validación-de-resultados)

7. [Troubleshooting](#troubleshooting)

  

---

  

## Introducción

  

Este manual describe los procedimientos de prueba para la integración SOAP con el Centro Autorizador de Unión Personal utilizando el protocolo CA_V20.

  

### Objetivos de las Pruebas

  

- ✅ Verificar conectividad con servicios SOAP de Unión Personal

- ✅ Validar transacciones ELG (Elegibilidad)

- ✅ Validar transacciones AP (Autorización de Prestaciones)

- ✅ Validar transacciones ATR (Anulación)

- ✅ Probar funcionalidad de TOKEN de credencial digital

- ✅ Verificar logging y trazabilidad

  

---

  

## Datos de Prueba

  

### 🔵 Ambiente de PRODUCCIÓN

  

#### Afiliado 1 - Titular Plan Accord

```

Código Afiliado: 54715500

Apellido y Nombre: Pruebas, Titular

Plan: 3C (Accord)

Versión Credencial: 71

```

  

#### Afiliado 2 - Titular Plan 2

```

Código Afiliado: 54715300

Apellido y Nombre: PRUEBA UP, Titular

Plan: 2

Versión Credencial: 65

```

  

---

  

### 🟢 Ambiente de TEST

  

#### Afiliado 1 - Afiliado Plan Accord

```

Código Afiliado: 54715500

Apellido y Nombre: Prueba, Afiliado Prueba

Plan: 150 (Accord)

Versión Credencial: 45

```

  

#### Afiliado 2 - Afiliado Plan 2

```

Código Afiliado: 54715300

Apellido y Nombre: PRUEBA1, Afiliado Prueba

Plan: 2

Versión Credencial: 31

```

  

---

  

### 🔑 Credenciales y Configuración

  

#### TOKEN de Desarrollo (Prod y Test)

```

TOKEN: 9999

Nota: Solo para desarrollo, NO válido para facturar

Vigencia: No expira (solo en desarrollo)

```

  

#### Prestador de Prueba (Prod y Test)

```

User ID: 8888

Password: 7777

ID Prestador: 8888

```

  

#### Configuración de Aplicación

```

Nombre de APP: Global

Tag XML: <APP>Global</APP>

```

  

#### Prestaciones de Prueba

```

Prestación 1: 1420107 (Consulta Especializada)

Prestación 2: 1420101 (Consulta Médica)

```

  

---

  

## Configuración de Entornos

  

### Paso 1: Configurar Variables de Entorno

  

#### Para Ambiente de TEST

  

Editar `.env`:

  

```bash

# SOAP - Unión Personal TEST

SOAP_UP_ENDPOINT=http://181.13.241.19:7002/cawsTest/Servicios

SOAP_UP_WSDL=http://181.13.241.19:7002/cawsTest/Servicios?wsdl

SOAP_UP_EMISOR_ID=INTEGRACION-UP

SOAP_UP_TERMINAL=01

SOAP_UP_APP_NAME=Global

SOAP_UP_PRESTADOR_ID=8888

SOAP_UP_USER_ID=8888

SOAP_UP_USER_PASS=7777

SOAP_UP_LOG_ENABLED=true

SOAP_UP_DB_LOG_ENABLED=true

```

  

#### Para Ambiente de PRODUCCIÓN

  

Editar `.env`:

  

```bash

# SOAP - Unión Personal PRODUCCIÓN

SOAP_UP_ENDPOINT=https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios

SOAP_UP_WSDL=https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios?wsdl

SOAP_UP_EMISOR_ID=INTEGRACION-UP

SOAP_UP_TERMINAL=01

SOAP_UP_APP_NAME=Global

SOAP_UP_PRESTADOR_ID=8888

SOAP_UP_USER_ID=8888

SOAP_UP_USER_PASS=7777

SOAP_UP_LOG_ENABLED=true

SOAP_UP_DB_LOG_ENABLED=true

```

  

### Paso 2: Ejecutar Migraciones

  

```bash

php artisan migrate

```

  

### Paso 3: Verificar Configuración

  

```bash

php artisan config:clear

php artisan cache:clear

```

  

---

  

## Casos de Prueba

  

### Suite de Pruebas Completa

  

| # | Caso de Prueba | Ambiente | Afiliado | Tipo | Resultado Esperado |

|---|----------------|----------|----------|------|-------------------|

| 1 | Test de Conexión | TEST | - | TestWs | Mensaje de bienvenida |

| 2 | Elegibilidad con Plan | TEST | 54715500 | ELG | STATUS: OK |

| 3 | Elegibilidad con TOKEN | TEST | 54715500 | ELG | STATUS: OK |

| 4 | Autorización Simple | TEST | 54715500 | AP | STATUS: OK, IDAUT generado |

| 5 | Autorización Múltiple | TEST | 54715500 | AP | STATUS: OK, 2 prestaciones |

| 6 | Anulación por IDTRAN | TEST | 54715500 | ATR | STATUS: OK |

| 7 | Anulación por MSGID | TEST | 54715500 | ATR | STATUS: OK |

| 8 | Elegibilidad Prod | PROD | 54715500 | ELG | STATUS: OK |

| 9 | Autorización Prod | PROD | 54715500 | AP | STATUS: OK |

| 10 | Validador Farmacia | TEST | 54715500 | ELG+AP | Flujo completo |

  

---

  

## Pruebas por Tipo de Transacción

  

### 🧪 Prueba 1: Test de Conexión

  

#### Objetivo

Verificar conectividad con el servicio SOAP.

  

#### Método

```http

GET /api/soap/test

```

  

#### cURL

```bash

curl -X GET http://localhost/api/soap/test

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "Bienvenido SISCON2_TEST",

"data": {

"return": "Test exitoso"

}

}

```

  

---

  

### 🧪 Prueba 2: Elegibilidad con Plan y Versión de Credencial (TEST)

  

#### Objetivo

Validar elegibilidad de afiliado usando Plan y VerCred.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"verifid": "MANUAL"

}

```

  

#### Método

```http

POST /api/soap/elegibilidad

Content-Type: application/json

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/elegibilidad \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"verifid": "MANUAL"

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "AFILIACION VALIDA**SOLO VERIFICACION - NO VALIDO PARA FACTURAR**",

"data": {

"IDTRAN": "1114XXXXXX",

"OPER": "ELG",

"STATUS": "OK",

"RSPCODG": "MSGXML_1000",

"RSPMSGG": "AFILIACION VALIDA**SOLO VERIFICACION - NO VALIDO PARA FACTURAR**",

"AFICODIGO": "54715500",

"AFIAPE": "Prueba",

"AFINOM": "Afiliado Prueba",

"AFIPLAN": "150",

"AFIPLANNOM": "Accord",

"AFISEXO": "M",

"AFIFECNAC": "01/01/1980"

},

"idtran": "1114XXXXXX"

medicacion:

1111233 - metformina - 1



}

```

  

---

  

### 🧪 Prueba 3: Elegibilidad con TOKEN (TEST)

  

#### Objetivo

Validar elegibilidad usando TOKEN de credencial digital.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"token": "9999",

"verifid": "MANUAL"

}

```

  

#### Método

```http

POST /api/soap/elegibilidad

Content-Type: application/json

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/elegibilidad \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"token": "9999",

"verifid": "MANUAL"

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "AFILIACION VALIDA**SOLO VERIFICACION - NO VALIDO PARA FACTURAR**",

"data": {

"IDTRAN": "1114XXXXXX",

"STATUS": "OK",

"AFICODIGO": "54715500",

"AFIAPE": "Prueba",

"AFINOM": "Afiliado Prueba",

"AFIPLAN": "150"

},

"idtran": "1114XXXXXX"

}

```

  

#### Nota

El TOKEN 9999 es especial para desarrollo y no requiere Plan/VerCred.

  

---

  

### 🧪 Prueba 4: Autorización de Prestación Simple (TEST)

  

#### Objetivo

Autorizar una prestación médica.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"token": "9999",

"contexto_tipo": "A",

"prestaciones": [

{

"tipo": "P",

"id": "1420107",

"cant": 1

}

]

}

```

  

#### Método

```http

POST /api/soap/autorizar-prestacion

Content-Type: application/json

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/autorizar-prestacion \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"token": "9999",

"contexto_tipo": "A",

"prestaciones": [

{

"tipo": "P",

"id": "1420107",

"cant": 1

},


]

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "AP APROBADA",

"data": {

"IDTRAN": "1114XXXXXX",

"OPER": "AP",

"STATUS": "OK",

"RSPCODG": "MSGXML_1050",

"RSPMSGG": "AP APROBADA",

"AFICODIGO": "54715500",

"IDAUT": "117XXXXXX",

"PR": [

{

"TIPO": "P",

"ID": "1420107",

"DESCRIPCION": "CONSULTA ESPECIALIZADA",

"STATUS": "OK",

"CANT": "1",

"CARGO": "0",

"IMPOS": "703.99",

"IMPTOTAL": "703.99"

}

]

},

"idtran": "1114XXXXXX",

"idaut": "117XXXXXX"

}

```

  

---

  

### 🧪 Prueba 5: Autorización de Múltiples Prestaciones (TEST)

  

#### Objetivo

Autorizar múltiples prestaciones en una sola transacción.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"verifid": "MANUAL",

"contexto_tipo": "A",

"prestaciones": [

{

"tipo": "P",

"id": "1420107",

"cant": 1

},

{

"tipo": "P",

"id": "1420101",

"cant": 1

}

]

}

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/autorizar-prestacion \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"verifid": "MANUAL",

"contexto_tipo": "A",

"prestaciones": [

{"tipo": "P", "id": "1420107", "cant": 1},

{"tipo": "P", "id": "1420101", "cant": 1}

]

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "AP APROBADA",

"data": {

"IDTRAN": "1114XXXXXX",

"STATUS": "OK",

"IDAUT": "117XXXXXX",

"PR": [

{

"TIPO": "P",

"ID": "1420107",

"DESCRIPCION": "CONSULTA ESPECIALIZADA",

"STATUS": "OK",

"CANT": "1"

},

{

"TIPO": "P",

"ID": "1420101",

"DESCRIPCION": "CONSULTA MEDICA",

"STATUS": "OK",

"CANT": "1"

}

]

},

"idtran": "1114XXXXXX",

"idaut": "117XXXXXX"

}

```

  

---

  

### 🧪 Prueba 6: Anulación por IDTRAN (TEST)

  

#### Objetivo

Anular una transacción previamente autorizada usando el IDTRAN.

  

#### Pre-requisito

Ejecutar primero Prueba 4 o 5 para obtener un IDTRAN válido.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"tipoidanul": "IDTRAN",

"idanul": "1114XXXXXX",

"motivo": "Prueba de anulación"

}

```

  

**Nota:** Reemplazar `1114XXXXXX` con el IDTRAN real de la Prueba 4 o 5.

  

#### Método

```http

POST /api/soap/anular-transaccion

Content-Type: application/json

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/anular-transaccion \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"plan": "150",

"vercred": "45",

"tipoidanul": "IDTRAN",

"idanul": "1114XXXXXX",

"motivo": "Prueba de anulación"

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"message": "ANULACION APROBADA",

"data": {

"IDTRAN": "1114YYYYYY",

"OPER": "ATR",

"STATUS": "OK",

"RSPCODG": "MSGXML_1090",

"RSPMSGG": "ANULACION APROBADA",

"AFICODIGO": "54715500",

"IDAUT": "117XXXXXX"

},

"idtran": "1114YYYYYY"

}

```

  

---

  

### 🧪 Prueba 7: Anulación por MSGID (TEST)

  

#### Objetivo

Anular una transacción usando el MSGID interno.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"token": "9999",

"tipoidanul": "MSGID",

"idanul": "SISCON2_xxxxx",

"motivo": "Prueba anulación por MSGID"

}

```

  

**Nota:** Reemplazar `SISCON2_xxxxx` con un MSGID válido de una transacción previa.

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/anular-transaccion \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"token": "9999",

"tipoidanul": "MSGID",

"idanul": "SISCON2_xxxxx",

"motivo": "Prueba anulación por MSGID"

}'

```

  

---

  

### 🧪 Prueba 8: Elegibilidad en PRODUCCIÓN

  

#### Objetivo

Validar elegibilidad en ambiente de producción.

  

#### Pre-requisito

Cambiar `.env` a endpoints de PRODUCCIÓN.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715500",

"plan": "3C",

"vercred": "71",

"verifid": "MANUAL"

}

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/elegibilidad \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715500",

"plan": "3C",

"vercred": "71",

"verifid": "MANUAL"

}'

```

  

#### Resultado Esperado

```json

{

"success": true,

"data": {

"STATUS": "OK",

"AFICODIGO": "54715500",

"AFIAPE": "Pruebas",

"AFINOM": "Titular",

"AFIPLAN": "3C",

"AFIPLANNOM": "Accord"

}

}

```

  

---

  

### 🧪 Prueba 9: Autorización en PRODUCCIÓN

  

#### Objetivo

Autorizar prestación en ambiente de producción.

  

#### Datos de Entrada

```json

{

"afiliado_codigo": "54715300",

"plan": "2",

"vercred": "65",

"verifid": "MANUAL",

"contexto_tipo": "A",

"prestaciones": [

{

"tipo": "P",

"id": "1420107",

"cant": 1

}

]

}

```

  

#### cURL

```bash

curl -X POST http://localhost/api/soap/autorizar-prestacion \

-H "Content-Type: application/json" \

-d '{

"afiliado_codigo": "54715300",

"plan": "2",

"vercred": "65",

"verifid": "MANUAL",

"contexto_tipo": "A",

"prestaciones": [

{"tipo": "P", "id": "1420107", "cant": 1}

]

}'

```

  

---

  

### 🧪 Prueba 10: Validador de Farmacia (Flujo Completo)

  

#### Objetivo

Probar el flujo completo desde la interfaz del Validador de Farmacias.

  

#### Pasos

  

1. **Acceder al módulo:**

```

http://localhost/admin/validador_farmacia

```

  

2. **Completar formulario:**

- Número de afiliado: `54715500`

- TOKEN: `9999`

- Punto de Retiro: (Seleccionar cualquiera)

  

3. **Hacer clic en "Buscar y Validar con SOAP"**

  

4. **Verificar resultado:**

- Debe aparecer alerta verde con "Validación SOAP Unión Personal"

- Debe mostrar datos del afiliado:

- Nombre: Prueba, Afiliado Prueba

- Plan: Accord (150)

- ID Transacción: 1114XXXXXX

  

5. **Verificar en Base de Datos:**

```sql

SELECT * FROM soap_transactions

ORDER BY created_at DESC

LIMIT 1;

```

  

Debe mostrar:

- `transaction_type`: ELG

- `afiliado_codigo`: 54715500

- `status`: OK

- `response_message`: Mensaje de éxito

  

---

  

## Validación de Resultados

  

### Checklist de Validación

  

Para cada prueba, verificar:

  

- [ ] **HTTP Status Code**: 200 OK

- [ ] **Campo success**: true

- [ ] **Campo data.STATUS**: OK

- [ ] **IDTRAN generado**: Formato 1114XXXXXX

- [ ] **IDAUT generado** (solo AP): Formato 117XXXXXX

- [ ] **Datos del afiliado correctos**:

- [ ] AFICODIGO

- [ ] AFIAPE (Apellido)

- [ ] AFINOM (Nombre)

- [ ] AFIPLAN

- [ ] **Registro en BD**: Creado en tabla `soap_transactions`

- [ ] **Log en archivo**: Registro en `storage/logs/laravel.log`

  

### Verificación en Base de Datos

  

```sql

-- Ver última transacción

SELECT

id,

transaction_type,

afiliado_codigo,

status,

response_code,

created_at

FROM soap_transactions

ORDER BY created_at DESC

LIMIT 1;

  

-- Ver todas las transacciones de un afiliado

SELECT

transaction_type,

status,

idtran,

idaut,

response_message,

created_at

FROM soap_transactions

WHERE afiliado_codigo = '54715500'

ORDER BY created_at DESC;

  

-- Estadísticas

SELECT

transaction_type,

status,

COUNT(*) as total

FROM soap_transactions

GROUP BY transaction_type, status;

```

  

### Verificación en Logs

  

```bash

# Ver logs en tiempo real

tail -f storage/logs/laravel.log

  

# Buscar transacciones SOAP

grep "SOAP" storage/logs/laravel.log | tail -20

  

# Buscar errores

grep "ERROR" storage/logs/laravel.log | grep "SOAP"

```

  

---

  

## Troubleshooting

  

### Problema: "No se pudo conectar al servicio de Unión Personal"

  

**Posibles causas:**

  

1. **Extensión SOAP no habilitada**

```bash

php -m | grep soap

```

Si no aparece, habilitar en `php.ini`:

```ini

extension=soap

```

  

2. **Endpoint incorrecto en .env**

Verificar:

```bash

php artisan config:clear

grep SOAP_UP .env

```

  

3. **Firewall bloqueando conexión**

Probar conectividad:

```bash

curl -I http://181.13.241.19:7002/cawsTest/Servicios?wsdl

```

  

### Problema: "Errores de validación - token"

  

**Solución:**

El TOKEN debe ser exactamente 4 dígitos:

```json

{

"token": "9999" // ✅ Correcto

"token": "999" // ❌ Incorrecto

"token": "99999" // ❌ Incorrecto

}

```

  

### Problema: "MSGXML_2XXX" (Rechazado)

  

Códigos de rechazo comunes:

  

| Código | Significado | Solución |

|--------|-------------|----------|

| MSGXML_2000 | Afiliado no encontrado | Verificar código de afiliado |

| MSGXML_2010 | Prestación no autorizada | Usar prestaciones de prueba: 1420107, 1420101 |

| MSGXML_2020 | Plan inválido | Verificar plan según ambiente |

| MSGXML_2030 | Credencial vencida | Verificar versión de credencial |

  

### Problema: TOKEN Expirado

  

**Nota:** El TOKEN `9999` es especial para desarrollo y NO expira.

  

Para TOKENs reales:

- Vigencia: 5 minutos

- Solución: Generar nuevo TOKEN desde app móvil

  

### Problema: "Tabla soap_transactions no existe"

  

**Solución:**

```bash

php artisan migrate

```

  

### Problema: Datos no se guardan en BD

  

Verificar configuración en `.env`:

```bash

SOAP_UP_DB_LOG_ENABLED=true

```

  

Limpiar cache:

```bash

php artisan config:clear

php artisan cache:clear

```

  

---

  

## Matriz de Pruebas

  

### Resumen de Ejecución

  

| Prueba | Ambiente | Afiliado | Resultado | Observaciones |

|--------|----------|----------|-----------|---------------|

| 1. Test Conexión | TEST | - | ⬜ PENDIENTE | |

| 2. ELG Plan | TEST | 54715500 | ⬜ PENDIENTE | |

| 3. ELG TOKEN | TEST | 54715500 | ⬜ PENDIENTE | |

| 4. AP Simple | TEST | 54715500 | ⬜ PENDIENTE | |

| 5. AP Múltiple | TEST | 54715500 | ⬜ PENDIENTE | |

| 6. ATR IDTRAN | TEST | 54715500 | ⬜ PENDIENTE | |

| 7. ATR MSGID | TEST | 54715500 | ⬜ PENDIENTE | |

| 8. ELG Prod | PROD | 54715500 | ⬜ PENDIENTE | |

| 9. AP Prod | PROD | 54715300 | ⬜ PENDIENTE | |

| 10. Validador | TEST | 54715500 | ⬜ PENDIENTE | |

  

**Leyenda:**

- ⬜ PENDIENTE

- ✅ EXITOSO

- ❌ FALLIDO

- ⚠️ CON OBSERVACIONES

  

---

  

## Anexo: Datos Rápidos de Referencia

  

### Quick Reference - TEST

  

```bash

# Afiliado 1

CODIGO=54715500

PLAN=150

VERCRED=45

  

# Afiliado 2

CODIGO=54715300

PLAN=2

VERCRED=31

  

# TOKEN Desarrollo

TOKEN=9999

  

# Prestador

USRID=8888

USRPASS=7777

IDPRESTADOR=8888

  

# Prestaciones

PREST1=1420107

PREST2=1420101

```

  

### Quick Reference - PRODUCCIÓN

  

```bash

# Afiliado 1

CODIGO=54715500

PLAN=3C

VERCRED=71

  

# Afiliado 2

CODIGO=54715300

PLAN=2

VERCRED=65

  

# TOKEN Desarrollo

TOKEN=9999

  

# Prestador

USRID=8888

USRPASS=7777

IDPRESTADOR=8888

  

# Prestaciones

PREST1=1420107

PREST2=1420101

  

# APP Name

APP=Global

```

  

---

  

## Conclusión

  

Este manual proporciona todos los casos de prueba necesarios para validar la integración SOAP con Unión Personal.

  

Se recomienda:

  

1. Ejecutar primero todas las pruebas en ambiente TEST

2. Documentar resultados en la Matriz de Pruebas

3. Resolver cualquier issue antes de pasar a PRODUCCIÓN

4. Ejecutar pruebas en PRODUCCIÓN con datos reales

5. Mantener logs de todas las transacciones

  

Para soporte adicional, consultar:

- `SOAP_IMPLEMENTATION_README.md`

- Documentación oficial en `/documentacion up/`

- Logs del sistema en `storage/logs/laravel.log`

  

---

  

**Documento:** Manual de Pruebas SOAP UP

**Versión:** 1.0

**Fecha:** Octubre 2025

**Proyecto:** SISCON2_PROD