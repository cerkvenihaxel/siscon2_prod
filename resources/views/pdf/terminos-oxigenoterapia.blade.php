<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - Oxigenoterapia</title>
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
        .numbered-list {
            margin-left: 20px;
        }
        .numbered-list li {
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
        <div class="title">TÉRMINOS Y CONDICIONES</div>
        <div class="subtitle">PRÉSTAMO DE EQUIPOS DE OXIGENOTERAPIA</div>
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

    <div class="section">
        <div class="section-title">1. OBJETO DEL CONTRATO</div>
        <div class="content">
            El presente documento establece los términos y condiciones para el préstamo de equipos médicos 
            de oxigenoterapia al paciente identificado, con el fin de proporcionar tratamiento médico 
            complementario según la prescripción médica correspondiente.
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. EQUIPOS PRESTADOS</div>
        <div class="content">
            Los siguientes equipos serán prestados al paciente:
        </div>
        <ul class="numbered-list">
            @foreach($pedido->materiales as $material)
                <li><strong>{{ $material->equipo->nombre_equipo ?? 'Equipo no especificado' }}</strong><br>
                    Cantidad: {{ $material->cantidad }}<br>
                    Descripción: {{ $material->equipo->descripcion ?? 'Sin descripción disponible' }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <div class="section-title">3. DURACIÓN DEL PRÉSTAMO</div>
        <div class="content">
            El préstamo tendrá una duración inicial según la prescripción médica. La renovación del préstamo 
            estará sujeta a la evaluación médica y la disponibilidad de equipos. El paciente deberá devolver 
            los equipos al finalizar el tratamiento o cuando sea requerido por el personal médico.
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. OBLIGACIONES DEL PACIENTE</div>
        <div class="content">
            El paciente se compromete a:
        </div>
        <ol class="numbered-list">
            <li>Utilizar los equipos únicamente para el tratamiento prescrito</li>
            <li>Mantener los equipos en buen estado de conservación</li>
            <li>No realizar modificaciones o reparaciones sin autorización</li>
            <li>No transferir los equipos a terceros</li>
            <li>Notificar inmediatamente cualquier daño o mal funcionamiento</li>
            <li>Mantener los equipos en un lugar seguro y accesible</li>
            <li>No fumar cerca de los equipos de oxígeno</li>
            <li>Asistir a los controles médicos programados</li>
            <li>Devolver los equipos en el estado en que fueron recibidos</li>
            <li>Informar cambios de domicilio o contacto</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">5. PROHIBICIONES</div>
        <div class="content">
            Está estrictamente prohibido:
        </div>
        <ul class="numbered-list">
            <li>Fumar en presencia de equipos de oxígeno</li>
            <li>Exponer los equipos a fuentes de calor o llama</li>
            <li>Usar los equipos para fines no médicos</li>
            <li>Desarmar o modificar los equipos</li>
            <li>Dejar los equipos sin supervisión en lugares inadecuados</li>
            <li>Prestar los equipos a otras personas</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">6. RESPONSABILIDADES</div>
        <div class="content">
            <strong>6.1 Responsabilidad del Paciente:</strong> El paciente será responsable de cualquier daño 
            causado a los equipos por uso inadecuado, negligencia o accidente evitable.<br><br>
            
            <strong>6.2 Responsabilidad de la Institución:</strong> La institución se compromete a proporcionar 
            equipos en buen estado y a realizar el mantenimiento preventivo necesario.<br><br>
            
            <strong>6.3 Limitación de Responsabilidad:</strong> La institución no será responsable por daños 
            indirectos o consecuenciales derivados del uso de los equipos.
        </div>
    </div>

    <div class="section">
        <div class="section-title">7. MANTENIMIENTO Y SERVICIO</div>
        <div class="content">
            La institución realizará el mantenimiento preventivo de los equipos según el cronograma establecido. 
            En caso de mal funcionamiento, el paciente deberá contactar inmediatamente al servicio técnico 
            autorizado. No se permiten reparaciones por personal no autorizado.
        </div>
    </div>

    <div class="section">
        <div class="section-title">8. DEVOLUCIÓN DE EQUIPOS</div>
        <div class="content">
            <strong>8.1 Condiciones de Devolución:</strong> Los equipos deben ser devueltos en el mismo estado 
            en que fueron entregados, con todos sus accesorios y documentación.<br><br>
            
            <strong>8.2 Fecha de Devolución:</strong> Al finalizar el tratamiento o cuando sea requerido 
            por el personal médico.<br><br>
            
            <strong>8.3 Lugar de Devolución:</strong> En las instalaciones de la institución o en el lugar 
            que se indique al momento de la devolución.
        </div>
    </div>

    <div class="section">
        <div class="section-title">9. SANCIONES</div>
        <div class="content">
            El incumplimiento de estos términos y condiciones puede resultar en:
        </div>
        <ul class="numbered-list">
            <li>Suspensión del servicio de oxigenoterapia</li>
            <li>Responsabilidad por daños a los equipos</li>
            <li>Acciones legales si corresponde</li>
            <li>Denegación de futuros préstamos</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">10. CONFIDENCIALIDAD</div>
        <div class="content">
            Toda la información médica y personal del paciente será tratada con la máxima confidencialidad, 
            de acuerdo con las leyes de protección de datos personales vigentes.
        </div>
    </div>

    <div class="section">
        <div class="section-title">11. MODIFICACIONES</div>
        <div class="content">
            La institución se reserva el derecho de modificar estos términos y condiciones, notificando 
            previamente al paciente sobre cualquier cambio significativo.
        </div>
    </div>

    <div class="section">
        <div class="section-title">12. ACEPTACIÓN</div>
        <div class="content">
            Al recibir los equipos, el paciente acepta expresamente todos los términos y condiciones 
            establecidos en este documento, comprometiéndose a cumplirlos durante todo el período del préstamo.
        </div>
    </div>

    <div class="footer">
        <p>Este documento es parte del expediente médico del paciente y debe ser conservado.</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }} - Sistema de Gestión de Oxigenoterapia</p>
    </div>
</body>
</html> 