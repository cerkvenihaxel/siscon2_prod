curl --location 'http://localhost:8081/notification-medication' \
--header 'Content-Type: application/json' \
--data '{
  "phone": "+5493804213845",
  "message_vars": "{\"1\":\"Dardo Reyna\",\"2\":\"GLUCEMIX\",\"3\":\"PRUEBA\"}"
}'

Respuesta: 

¡Hola buenos días! 👋🏻, Dardo Reyna. Le informamos que su medicación está lista para ser retirada en la sucursal GLUCEMIX.

Horarios de atención: PRUEBA
Número de contacto : +543804212399
📍Ubicación: https://maps.app.goo.gl/dkYXFZDCo6mtRXeJA



curl --location 'http://localhost:8081/notification-approved' \
--header 'Content-Type: application/json' \
--data '{
  "phone": "+5493804213845",
  "message_vars": "{\"1\":\"DARDO REYNA\",\"2\":\"PRUEBA APROBACION\"}"
}'

Buenos días Sr/a 👋🏻 DARDO REYNA, su medicación fue procesada el día PRUEBA APROBACION y pronto podrá retirarla en nuestra sucursal más cercana.

Le deseamos un excelente día 🤗.


curl --location 'http://localhost:8081/general-message' \
--header 'Content-Type: application/json' \
--data '{
  "phone": "+5493804213845",
  "message_vars": "{\"1\":\"variable 1\",\"2\":\"variable 2\",\"3\":\"variable 3\"}"
}'

Buenos días, nos contactamos desde variable 1.

Queremos informarle que variable 2

Desde ya muchas gracias, variable 3

Global Médica S.A.


