#!/bin/bash

echo "🚀 Iniciando migraciones de la rama actual..."
echo "================================================"

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

echo "📋 Ejecutando migraciones de 2025_10 en adelante..."

# Migraciones de octubre 2025
echo "1️⃣ Creando tabla afiliados_convenio_up..."
php artisan migrate --path=database/migrations/2025_10_28_000001_create_afiliados_convenio_up_table.php

echo "2️⃣ Creando tabla up_consumos..."
php artisan migrate --path=database/migrations/2025_10_28_000002_create_up_consumos_table.php

echo "3️⃣ Creando tabla up_solicitudes..."
php artisan migrate --path=database/migrations/2025_10_28_000003_create_up_solicitudes_table.php

echo "4️⃣ Creando tabla up_solicitud_items..."
php artisan migrate --path=database/migrations/2025_10_28_000004_create_up_solicitud_items_table.php

echo "5️⃣ Creando tabla up_transacciones_soap..."
php artisan migrate --path=database/migrations/2025_10_28_000005_create_up_transacciones_soap_table.php

echo "6️⃣ Creando tabla up_elegibilidad..."
php artisan migrate --path=database/migrations/2025_10_31_000001_create_up_elegibilidad_table.php

echo "7️⃣ Creando tabla up_autorizacion_previa..."
php artisan migrate --path=database/migrations/2025_10_31_000002_create_up_autorizacion_previa_table.php

echo "8️⃣ Creando tabla up_anulaciones..."
php artisan migrate --path=database/migrations/2025_10_31_000003_create_up_anulaciones_table.php

echo "9️⃣ Agregando campos de workflow a up_consumos..."
php artisan migrate --path=database/migrations/2025_10_31_000004_add_workflow_fields_to_up_consumos.php

# Migraciones de noviembre 2025
echo "🔟 Creando tabla up_validaciones_entrega..."
php artisan migrate --path=database/migrations/2025_11_04_000001_create_up_validaciones_entrega_table.php

echo "1️⃣1️⃣ Agregando campos de validación entrega a up_consumos..."
php artisan migrate --path=database/migrations/2025_11_04_000002_add_validacion_entrega_fields_to_up_consumos.php

echo "1️⃣2️⃣ Agregando campo remito a up_consumos..."
php artisan migrate --path=database/migrations/2025_11_06_000001_add_remito_to_up_consumos.php

echo "1️⃣3️⃣ Agregando columnas de workflow a up_consumos..."
php artisan migrate --path=database/migrations/2025_11_06_000002_add_workflow_columns_to_up_consumos.php

echo "1️⃣4️⃣ Agregando todas las columnas de workflow..."
php artisan migrate --path=database/migrations/2025_11_06_000003_add_all_workflow_columns.php

echo "1️⃣5️⃣ Creando tabla up_entregas..."
php artisan migrate --path=database/migrations/2025_11_07_130701_create_up_entregas_table.php

echo "1️⃣6️⃣ Creando tabla up_usuarios_datos..."
php artisan migrate --path=database/migrations/2025_11_07_140625_create_up_usuarios_datos_table.php

echo "1️⃣7️⃣ Creando tabla twilio_sender_logs..."
php artisan migrate --path=database/migrations/2025_11_07_140949_create_twilio_sender_logs_table.php

echo "📊 Ejecutando seeders..."

echo "1️⃣8️⃣ Insertando usuario inicial en up_usuarios_datos..."
php artisan db:seed --class=UpUsuariosDatosSeeder

echo "1️⃣9️⃣ Creando menú de Anulación UP..."
php artisan db:seed --class=AnulacionUpMenuSeeder

echo "✅ Migraciones completadas exitosamente!"
echo "================================================"
echo "📝 Resumen de cambios aplicados:"
echo "   - 17 migraciones ejecutadas (2025_10 en adelante)"
echo "   - Sistema completo UP implementado"
echo "   - Usuario inicial insertado (ID: 54715500)"
echo "   - Menús del sistema configurados"
echo ""
echo "🔧 Servicios implementados:"
echo "   - Sistema completo de Unión Personal (UP)"
echo "   - Transacciones AP, Consumos, Entregas, Anulaciones"
echo "   - TwilioSender para notificaciones WhatsApp"
echo "   - Control de permisos por usuario"
echo "   - Vistas homogéneas para todo el flujo"
echo ""
echo "🎉 ¡Sistema UP listo para usar!"
