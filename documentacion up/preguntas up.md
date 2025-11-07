

- **ELG (Elegibilidad):** verificar si el afiliado (y opcionalmente una prestación y/o prestador) es elegible. 
    
- **AP (Autorizar Prestación):** pedir la autorización online de prestaciones/medicamentos/recetas para un afiliado desde un prestador. 
    
- **ATR (Anulación):** anular una transacción previa por decisión o por “reverso” (sin respuesta o falla).

---

# **Dónde pegarle (ambientes y WSDL)**

- **Producción (SOAP endpoint):** https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios
    
- **Test (SOAP endpoint):** http://181.13.241.19:7002/cawsTest/Servicios
    
- **WSDL** agregando ?wsdl a cada endpoint. 
    

---

# **Campos/segmentos que te van a pedir sí o sí**

- **EMISOR**: ID (terminal/punto emisor), PROT=CA_V20, APP, MSGID, TIME, etc. 
    
- **SEGURIDAD**: TIPOAUT (U/A/UA/N), TIPOCON="PRES", USRID, USRPASS, etc. 
    
- **OPER**: TIPO (ELG/AP/ATR), FECHA, IDASEG, IDPRESTADOR, y en ATR además TIPOIDANUL, IDANUL. 
    
- **PID**: ID del afiliado (+ opcionales TIPOID, PLAN, VERCRED, VERIFID, o **TOKEN** de 4 dígitos si usás credencial digital).     
    
- **PR** (lista de prestaciones): TIPO (P/M/D), ID, CANT, y para M: TROQUEL/CODBARRA. 
    

  

Las respuestas siempre traen IDTRAN, STATUS, RSPCODG/RSPMSGG, datos del afiliado/plan y, si corresponde, el detalle por prestación (con importes).


---



# **Qué hace cada operación (en simple)**

- **ELG**: confirmás afiliación/plan y, si mandás PR, te devuelve si la prestación está cubierta y los importes de convenio (honorarios/gastos). Útil para “pre-chequeo” antes de autorizar.  SOLAMENTE AFILIADO 

    
- **AP**: pedís la autorización de cada prestación (P), medicamento (M) o derivación (D). Responde estado por ítem (OK/NO/PEND) con importes a cargo de afiliado/obra social/otros. 
    
- **ATR**: anulás por **IDTRAN** (el id de la transacción devuelto por el motor) o por **MSGID** (id interno tuyo) si nunca recibiste respuesta pero quizá se registró.

USRID USRPASS con la anulación 


## ** 1. Integración Técnica**

- ¿El **WSDL** es único o cambia según el ambiente (test/prod)? el que tenemos en el servicio 
    
- ¿Qué **timeout** recomiendan para TransaccionStr? timeout no tiene. 

    
- ¿Hay **rate limit** o cantidad máxima de transacciones por prestador? no tiene 
    
- ¿El campo MSGID debe ser **único globalmente** o solo por día/sesión? no se controla nada de ese TAG. Mirá tengo tal transacción. IDTRAN viaja mejor en la respuesta. Es más fácil.


    
- ¿Cómo se valida USRID / USRPASS? (¿LDAP, base interna o dummy?)
    
- ¿Cómo manejan las **versiones del protocolo** (PROT = CA_V20)?
    

---

## **2. Seguridad y Token**

- ¿Cómo se **genera y obtiene** el token de 4 dígitos de la credencial digital? dura 5 minutos.
    
- ¿Qué pasa si el token **expira** durante una transacción?
    
- ¿El segmento SEGURIDAD se **envía cifrado o plano** bajo HTTPS?
    
- ¿Usan **WS-Security** o solo TLS?
    
- ¿Se usa TIPOAUT = UA (usuario y afiliado) o solo U/A?
    

---

## **3. Lógica de Negocio / Flujo**

- ¿Se recomienda ejecutar siempre un **ELG** antes del **AP**?
    
- ¿Cuánto tiempo esperar antes de enviar un **ATR por reverso**?
    
- ¿Qué significa exactamente STATUS = PEND (auditoría manual o async)?
    
- ¿Qué preferencia hay entre ATR por IDTRAN o ATR por MSGID?
    
- ¿Qué tipo de **códigos de prestación** se aceptan (nomenclador, Alfabeta, CIE10)?
    

---

## **💊 4. Recetas y Prescripciones Médicas**

- ¿Desde qué sistemas **prescriben los médicos** actualmente (portal, app, software propio)?
    
- ¿Las recetas son **digitales, electrónicas con firma, o papel escaneado**?
    
- ¿Existe un **ID de receta** o QR que se deba enviar en la transacción?
    
- ¿Qué campos del segmento <PRESCRIP> son **obligatorios** (MAT, ORG, FECHA)?
    
- ¿El médico prescriptor es el mismo que el **efector**, o son dos roles distintos?
    

---

## **🧠 5. Flujo Operativo del Servicio**

- ¿El HMS recibe directamente desde el prestador o hay un **integrador intermedio**?
    
- ¿Qué pasa si el médico no tiene conexión al momento de la receta?
    
- ¿Existen **autorizaciones diferidas** o deben ser online?
    
- ¿Cómo se **notifica** al médico/paciente el resultado del AP (callback, consulta, email)?
    
- ¿Las transacciones quedan auditadas con **IDTRAN/MSGID**? ¿Por cuánto tiempo?
    

---

## **🧾 6. Manejo de Respuestas y Errores**

- ¿Hay una tabla oficial de **códigos de respuesta (RSPCODG, RSPMSGP)**?

Respuesta basica y respuesta mesage personalizado

    
- ¿Qué pasa si falta un campo obligatorio (ej. FECHA, PLAN)?
    
- ¿Formato exacto de las **fechas** (YYYY-MM-DD o DD/MM/YYYY)? Hay que hablar con el negocio. para ver si no esta fuera de rango.
- 
    
- ¿Qué validaciones cruzadas existen entre IDPRESTADOR y USRID?
    

---

## **🧪 7. Pruebas y Despliegue**

- ¿Existe un entorno **sandbox con afiliados de prueba**?
    
- ¿A quién se notifica para habilitar un nuevo **EMISOR.ID**?
    
- ¿Se puede solicitar **logs o trazas** al equipo HMS si hay error 500?
    
- ¿El servicio **almacena auditorías** de las transacciones? (TTL o retención)
    


## **🔄 8. Futuro e Interoperabilidad**

- ¿Planean migrar estos servicios a una **API REST** además de SOAP?
    
- ¿Se prevé integrar con la **Receta Digital Nacional (SNRD)**?
    
- ¿Permiten **batch de transacciones** en una sola llamada SOAP?
    
- ¿Cómo manejan **idempotencia** si se repite el mismo MSGID?





# **Cómo implementarlo (paso a paso, práctico)**

1. **Cliente SOAP**
    
    - Elegí el ambiente (Test/Prod) y consumí el **WSDL** .../Servicios?wsdl.
        
    - En Node/NestJS podés usar soap (npm) o strong-soap. En Python, zeep.
        
    - Método principal: **TransaccionStr** recibe el XML de <SOLICITUD> como string y devuelve el XML de <RESPUESTA> como string (ambos en CDATA), tal como muestran los ejemplos. 
        
    
2. **Armar los DTOs y el builder de XML**
    
    - Modelá **Emisor**, **Seguridad**, **Oper**, **Pid**, **Pr**.
        
    - Generá el XML exactamente con los tags/valores del protocolo y mételo dentro del envoltorio SOAP tal como en los ejemplos del PDF.     
        
    
3. **Flujos recomendados**
    
    - **(Opcional) ELG →** validar afiliado/plan/prestación antes de autorizar. 
        
    - **AP →** enviar prestaciones. Si usás **credencial digital**, incluye **<TOKEN>** en PID (vigencia 5 min); si no, mandá PLAN/VERCRED/VERIFID.   
        
    - **ATR →** ante errores de comunicación o decisión del prestador/afiliado, anulá por **IDTRAN**; si no lo tenés, anulá por **MSGID** (última transacción de ese emisor con ese MSGID). 
        
    
4. **Seguridad & transporte**
    
    - La capa **SEGURIDAD** del mensaje usa USRID/USRPASS y TIPOAUT/TIPOCON.
        
    - Conectá **Prod por HTTPS (8443)** y **Test por HTTP (7002)** según indica la doc; en Prod usá TLS, timeouts y verificación de certificado. 
        
    
5. **Errores, idempotencia y trazabilidad**
    
    - Guardá MSGID propio y logueá IDTRAN de respuesta para correlación.
        
    - Ante timeout sin respuesta, **no repitas AP “a ciegas”**: aplicá **ATR por MSGID** para evitar duplicados y luego reintentá. 
        
    - Parseá STATUS (OK/NO/PEND) y RSPCODG/RSPMSGG/RSPMSGP para tu UX y reglas. 
        
    
6. **Testing**
    
    - Usá el endpoint de **Test** y el servicio **TestWs** para sanidad inicial, luego TransaccionStr con ejemplos del PDF.