<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consentimiento Informado - Oxigenoterapia</title>
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
        .signature-section {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 30px;
            text-align: center;
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
        <div class="title">CONSENTIMIENTO INFORMADO</div>
        <div class="subtitle">TRATAMIENTO DE OXIGENOTERAPIA</div>
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
        <div class="section-title">1. DESCRIPCIÓN DEL TRATAMIENTO</div>
        <div class="content">
            La oxigenoterapia es un tratamiento médico que consiste en la administración de oxígeno suplementario 
            para mantener niveles adecuados de oxigenación en la sangre. Este tratamiento puede ser administrado 
            de forma continua o intermitente, según las necesidades médicas del paciente.
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. EQUIPOS A UTILIZAR</div>
        <div class="content">
            Para el tratamiento se utilizarán los siguientes equipos médicos:
        </div>
        <ul style="margin-left: 20px;">
            @foreach($pedido->materiales as $material)
                <li>{{ $material->equipo->nombre_equipo ?? 'Equipo no especificado' }} - Cantidad: {{ $material->cantidad }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <div class="section-title">3. BENEFICIOS ESPERADOS</div>
        <div class="content">
            • Mejora de la oxigenación sanguínea<br>
            • Reducción de la disnea (dificultad para respirar)<br>
            • Mejora de la capacidad funcional<br>
            • Prevención de complicaciones por hipoxemia<br>
            • Mejora de la calidad de vida
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. RIESGOS Y COMPLICACIONES</div>
        <div class="content">
            Aunque la oxigenoterapia es generalmente segura, pueden presentarse los siguientes riesgos:<br><br>
            • Sequedad de las mucosas nasales<br>
            • Irritación de la piel en el área de contacto<br>
            • Riesgo de incendio (no fumar cerca del equipo)<br>
            • Dependencia psicológica del oxígeno<br>
            • Posibles efectos secundarios por uso prolongado
        </div>
    </div>

    <div class="section">
        <div class="section-title">5. ALTERNATIVAS AL TRATAMIENTO</div>
        <div class="content">
            Las alternativas incluyen otros tratamientos médicos para la condición subyacente, 
            pero la oxigenoterapia es el tratamiento estándar recomendado para su condición actual.
        </div>
    </div>

    <div class="section">
        <div class="section-title">6. COMPROMISOS DEL PACIENTE</div>
        <div class="content">
            Al aceptar este tratamiento, me comprometo a:<br><br>
            • Utilizar el equipo según las instrucciones médicas<br>
            • Mantener el equipo en buen estado<br>
            • No fumar cerca del equipo de oxígeno<br>
            • Informar inmediatamente cualquier problema o efecto secundario<br>
            • Asistir a los controles médicos programados<br>
            • Devolver el equipo al finalizar el tratamiento
        </div>
    </div>

    <div class="signature-section">
        <div class="content">
            <strong>DECLARACIÓN:</strong> He leído y comprendido la información proporcionada sobre el tratamiento 
            de oxigenoterapia. He tenido la oportunidad de hacer preguntas y mis dudas han sido respondidas 
            satisfactoriamente. Consiento voluntariamente en recibir este tratamiento.
        </div>
        
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Firma del Paciente o Familiar</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Firma del Médico</div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Fecha</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Fecha</div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <div class="footer">
        <p>Este documento es parte del expediente médico del paciente y debe ser conservado.</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }} - Sistema de Gestión de Oxigenoterapia</p>
    </div>
</body>
</html> 