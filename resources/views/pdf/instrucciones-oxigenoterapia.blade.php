<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrucciones de Uso - Oxigenoterapia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
            background-color: #f0f0f0;
            padding: 5px;
            border-left: 4px solid #007bff;
        }
        .patient-info {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .patient-info table {
            width: 100%;
        }
        .patient-info td {
            padding: 3px 0;
        }
        .patient-info .label {
            font-weight: bold;
            width: 30%;
        }
        .content {
            text-align: justify;
            margin-bottom: 15px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .warning strong {
            color: #856404;
        }
        .equipment-instructions {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        .equipment-instructions h4 {
            color: #007bff;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">INSTRUCCIONES DE USO Y MANTENIMIENTO</div>
        <div class="subtitle">EQUIPOS DE OXIGENOTERAPIA</div>
        <div style="margin-top: 10px;">
            <strong>Nro. Solicitud:</strong> {{ $pedido->nro_solicitud }} | 
            <strong>Fecha:</strong> {{ date('d/m/Y', strtotime($pedido->created_at)) }}
        </div>
    </div>

    <div class="patient-info">
        <table>
            <tr>
                <td class="label">Paciente:</td>
                <td>{{ $pedido->nombre_apellido }}</td>
                <td class="label">Nro. Afiliado:</td>
                <td>{{ $pedido->nro_afiliado }}</td>
            </tr>
            <tr>
                <td class="label">Documento:</td>
                <td>{{ $pedido->documento }}</td>
                <td class="label">Edad:</td>
                <td>{{ $pedido->edad }} años</td>
            </tr>
            <tr>
                <td class="label">Médico Tratante:</td>
                <td>{{ $pedido->medicos->nombremedico ?? 'No especificado' }}</td>
                <td class="label">Clínica:</td>
                <td>{{ $pedido->clinica->nombre ?? 'No especificada' }}</td>
            </tr>
        </table>
    </div>

    <div class="warning">
        <strong>⚠️ ADVERTENCIA IMPORTANTE:</strong><br>
        • NO FUMAR cerca de los equipos de oxígeno<br>
        • Mantener los equipos alejados de fuentes de calor o llama<br>
        • No modificar ni desarmar los equipos<br>
        • En caso de emergencia, contactar inmediatamente al servicio técnico
    </div>

    <div class="section">
        <div class="section-title">1. EQUIPOS ENTREGADOS</div>
        <div class="content">
            Los siguientes equipos han sido entregados para su tratamiento:
        </div>
        @foreach($pedido->materiales as $material)
        <div class="equipment-instructions">
            <h4>{{ $material->equipo->nombre_equipo ?? 'Equipo no especificado' }}</h4>
            <p><strong>Cantidad:</strong> {{ $material->cantidad }}</p>
            <p><strong>Descripción:</strong> {{ $material->equipo->descripcion ?? 'Sin descripción disponible' }}</p>
        </div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">2. INSTRUCCIONES GENERALES DE SEGURIDAD</div>
        <div class="content">
            <strong>2.1 Ubicación del Equipo:</strong><br>
            • Colocar el equipo en un lugar bien ventilado<br>
            • Mantener al menos 2 metros de distancia de fuentes de calor<br>
            • Evitar lugares húmedos o con polvo excesivo<br>
            • Asegurar que el equipo esté estable y no pueda caerse<br><br>

            <strong>2.2 Precauciones de Seguridad:</strong><br>
            • NO FUMAR en la habitación donde se encuentra el equipo<br>
            • NO usar velas, estufas o calentadores cerca del equipo<br>
            • Mantener el equipo alejado de niños y mascotas<br>
            • No cubrir el equipo con telas o materiales<br><br>

            <strong>2.3 Emergencias:</strong><br>
            • En caso de mal funcionamiento, apagar inmediatamente<br>
            • Contactar al servicio técnico: [NÚMERO DE EMERGENCIA]<br>
            • Si hay humo o olor extraño, evacuar el área
        </div>
    </div>

    <div class="section">
        <div class="section-title">3. INSTRUCCIONES DE USO POR EQUIPO</div>
        
        <div class="equipment-instructions">
            <h4>Concentrador de Oxígeno</h4>
            <div class="content">
                <strong>Encendido:</strong><br>
                1. Conectar el cable de alimentación a una toma de corriente estable<br>
                2. Presionar el botón de encendido<br>
                3. Esperar 2-3 minutos para que el equipo se estabilice<br>
                4. Verificar que la luz indicadora esté encendida<br><br>

                <strong>Uso:</strong><br>
                1. Conectar la cánula nasal al puerto de salida<br>
                2. Ajustar el flujo de oxígeno según prescripción médica<br>
                3. Colocar la cánula en la nariz del paciente<br>
                4. Verificar que el oxígeno esté fluyendo correctamente<br><br>

                <strong>Apagado:</strong><br>
                1. Retirar la cánula nasal del paciente<br>
                2. Cerrar el flujo de oxígeno<br>
                3. Presionar el botón de apagado<br>
                4. Desconectar el cable de alimentación
            </div>
        </div>

        <div class="equipment-instructions">
            <h4>Cilindro de Oxígeno</h4>
            <div class="content">
                <strong>Preparación:</strong><br>
                1. Verificar que el cilindro esté asegurado en posición vertical<br>
                2. Conectar el regulador de presión al cilindro<br>
                3. Abrir lentamente la válvula del cilindro<br>
                4. Verificar que no haya fugas<br><br>

                <strong>Uso:</strong><br>
                1. Ajustar el flujo de oxígeno en el regulador<br>
                2. Conectar la cánula nasal<br>
                3. Colocar la cánula en la nariz del paciente<br>
                4. Monitorear el nivel de oxígeno en el manómetro<br><br>

                <strong>Almacenamiento:</strong><br>
                1. Cerrar la válvula del cilindro<br>
                2. Mantener el cilindro en posición vertical<br>
                3. Almacenar en lugar fresco y seco<br>
                4. No exponer a temperaturas extremas
            </div>
        </div>

        <div class="equipment-instructions">
            <h4>Cánula Nasal</h4>
            <div class="content">
                <strong>Uso:</strong><br>
                1. Verificar que la cánula esté limpia<br>
                2. Insertar las puntas en las fosas nasales<br>
                3. Ajustar el tubo detrás de las orejas<br>
                4. Asegurar que no esté demasiado apretada<br><br>

                <strong>Mantenimiento:</strong><br>
                1. Limpiar diariamente con agua y jabón suave<br>
                2. Enjuagar completamente<br>
                3. Secar al aire libre<br>
                4. Reemplazar según recomendación médica
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. MANTENIMIENTO PREVENTIVO</div>
        <div class="content">
            <strong>4.1 Limpieza Diaria:</strong><br>
            • Limpiar el exterior del equipo con un paño húmedo<br>
            • Verificar que no haya polvo en las rejillas de ventilación<br>
            • Limpiar la cánula nasal<br>
            • Verificar el funcionamiento de todos los controles<br><br>

            <strong>4.2 Limpieza Semanal:</strong><br>
            • Limpiar a fondo el equipo<br>
            • Verificar conexiones y cables<br>
            • Revisar filtros si corresponde<br>
            • Documentar cualquier problema detectado<br><br>

            <strong>4.3 Mantenimiento Profesional:</strong><br>
            • El servicio técnico realizará mantenimiento cada 3-6 meses<br>
            • No intentar reparar el equipo personalmente<br>
            • Contactar al servicio técnico para cualquier problema
        </div>
    </div>

    <div class="section">
        <div class="section-title">5. MONITOREO Y CONTROL</div>
        <div class="content">
            <strong>5.1 Signos Vitales:</strong><br>
            • Monitorear la saturación de oxígeno según indicación médica<br>
            • Observar la frecuencia respiratoria<br>
            • Verificar el color de la piel y mucosas<br>
            • Reportar cualquier cambio significativo al médico<br><br>

            <strong>5.2 Funcionamiento del Equipo:</strong><br>
            • Verificar que el flujo de oxígeno sea constante<br>
            • Observar que no haya ruidos extraños<br>
            • Confirmar que las alarmas funcionen correctamente<br>
            • Documentar el tiempo de uso diario
        </div>
    </div>

    <div class="section">
        <div class="section-title">6. SOLUCIÓN DE PROBLEMAS COMUNES</div>
        <div class="content">
            <strong>Problema: El equipo no enciende</strong><br>
            Solución: Verificar conexión eléctrica, fusibles, y contactar servicio técnico<br><br>

            <strong>Problema: Flujo de oxígeno irregular</strong><br>
            Solución: Verificar conexiones, filtros, y ajustar configuración<br><br>

            <strong>Problema: Ruidos extraños</strong><br>
            Solución: Apagar inmediatamente y contactar servicio técnico<br><br>

            <strong>Problema: Cánula nasal incómoda</strong><br>
            Solución: Ajustar posición, verificar tamaño, o solicitar cambio
        </div>
    </div>

    <div class="section">
        <div class="section-title">7. CONTACTOS DE EMERGENCIA</div>
        <div class="content">
            <strong>Servicio Técnico:</strong> [NÚMERO DE TELÉFONO]<br>
            <strong>Horario de Atención:</strong> [HORARIOS]<br>
            <strong>Emergencias 24/7:</strong> [NÚMERO DE EMERGENCIA]<br>
            <strong>Médico Tratante:</strong> {{ $pedido->medicos->nombremedico ?? 'No especificado' }}<br>
            <strong>Clínica:</strong> {{ $pedido->clinica->nombre ?? 'No especificada' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">8. INFORMACIÓN ADICIONAL</div>
        <div class="content">
            • Guardar estas instrucciones en un lugar accesible<br>
            • Compartir esta información con cuidadores y familiares<br>
            • Mantener un registro de uso y mantenimiento<br>
            • Consultar al médico ante cualquier duda sobre el uso<br>
            • No modificar la configuración sin autorización médica
        </div>
    </div>

    <div class="footer">
        <p>Estas instrucciones son específicas para el paciente y equipos entregados.</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }} - Sistema de Gestión de Oxigenoterapia</p>
    </div>
</body>
</html> 