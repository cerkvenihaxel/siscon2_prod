<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato de Préstamo - Oxigenoterapia</title>
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
        .parties-info {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .parties-info table {
            width: 100%;
        }
        .parties-info td {
            padding: 3px 0;
        }
        .parties-info .label {
            font-weight: bold;
            width: 30%;
        }
        .content {
            text-align: justify;
            margin-bottom: 15px;
        }
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .equipment-table th,
        .equipment-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .equipment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
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
        <div class="title">CONTRATO DE PRÉSTAMO</div>
        <div class="subtitle">EQUIPOS MÉDICOS DE OXIGENOTERAPIA</div>
        <div style="margin-top: 10px;">
            <strong>Nro. Solicitud:</strong> {{ $pedido->nro_solicitud }} | 
            <strong>Fecha:</strong> {{ date('d/m/Y', strtotime($pedido->created_at)) }}
        </div>
    </div>

    <div class="parties-info">
        <table>
            <tr>
                <td class="label">PACIENTE:</td>
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
        <div class="section-title">PRIMERA: OBJETO DEL CONTRATO</div>
        <div class="content">
            Por medio del presente contrato, la <strong>INSTITUCIÓN PRESTADORA DE SERVICIOS DE SALUD</strong> 
            (en adelante "EL PRESTADOR") y el/la paciente <strong>{{ $pedido->nombre_apellido }}</strong> 
            (en adelante "EL PRESTATARIO"), acuerdan el préstamo de equipos médicos de oxigenoterapia 
            para el tratamiento médico prescrito, bajo las siguientes cláusulas:
        </div>
    </div>

    <div class="section">
        <div class="section-title">SEGUNDA: EQUIPOS PRESTADOS</div>
        <div class="content">
            EL PRESTADOR entrega en calidad de préstamo los siguientes equipos médicos:
        </div>
        <table class="equipment-table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->materiales as $material)
                <tr>
                    <td>{{ $material->equipo->nombre_equipo ?? 'Equipo no especificado' }}</td>
                    <td>{{ $material->equipo->descripcion ?? 'Sin descripción disponible' }}</td>
                    <td>{{ $material->cantidad }}</td>
                    <td>Bueno</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">TERCERA: DURACIÓN DEL PRÉSTAMO</div>
        <div class="content">
            El presente préstamo tendrá una duración inicial según la prescripción médica. La renovación 
            del préstamo estará sujeta a la evaluación médica y la disponibilidad de equipos. EL PRESTATARIO 
            deberá devolver los equipos al finalizar el tratamiento o cuando sea requerido por el personal médico.
        </div>
    </div>

    <div class="section">
        <div class="section-title">CUARTA: OBLIGACIONES DEL PRESTATARIO</div>
        <div class="content">
            EL PRESTATARIO se compromete a:
        </div>
        <ol style="margin-left: 20px;">
            <li>Utilizar los equipos únicamente para el tratamiento médico prescrito</li>
            <li>Mantener los equipos en buen estado de conservación</li>
            <li>No realizar modificaciones o reparaciones sin autorización expresa</li>
            <li>No transferir los equipos a terceros bajo ninguna circunstancia</li>
            <li>Notificar inmediatamente cualquier daño o mal funcionamiento</li>
            <li>Mantener los equipos en un lugar seguro y accesible</li>
            <li>No fumar cerca de los equipos de oxígeno</li>
            <li>Asistir a los controles médicos programados</li>
            <li>Devolver los equipos en el estado en que fueron recibidos</li>
            <li>Informar cambios de domicilio o datos de contacto</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">QUINTA: OBLIGACIONES DEL PRESTADOR</div>
        <div class="content">
            EL PRESTADOR se compromete a:
        </div>
        <ol style="margin-left: 20px;">
            <li>Entregar equipos en buen estado de funcionamiento</li>
            <li>Proporcionar instrucciones de uso y mantenimiento</li>
            <li>Realizar mantenimiento preventivo según cronograma</li>
            <li>Atender emergencias técnicas en horarios establecidos</li>
            <li>Proporcionar repuestos y accesorios necesarios</li>
            <li>Mantener la confidencialidad de la información médica</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">SEXTA: PROHIBICIONES</div>
        <div class="content">
            Está estrictamente prohibido para EL PRESTATARIO:
        </div>
        <ul style="margin-left: 20px;">
            <li>Fumar en presencia de equipos de oxígeno</li>
            <li>Exponer los equipos a fuentes de calor o llama</li>
            <li>Usar los equipos para fines no médicos</li>
            <li>Desarmar o modificar los equipos</li>
            <li>Dejar los equipos sin supervisión en lugares inadecuados</li>
            <li>Prestar los equipos a otras personas</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">SÉPTIMA: RESPONSABILIDADES</div>
        <div class="content">
            <strong>7.1 Responsabilidad del PRESTATARIO:</strong> Será responsable de cualquier daño causado 
            a los equipos por uso inadecuado, negligencia o accidente evitable.<br><br>
            
            <strong>7.2 Responsabilidad del PRESTADOR:</strong> Se compromete a proporcionar equipos en buen 
            estado y a realizar el mantenimiento preventivo necesario.<br><br>
            
            <strong>7.3 Limitación de Responsabilidad:</strong> EL PRESTADOR no será responsable por daños 
            indirectos o consecuenciales derivados del uso de los equipos.
        </div>
    </div>

    <div class="section">
        <div class="section-title">OCTAVA: MANTENIMIENTO Y SERVICIO</div>
        <div class="content">
            EL PRESTADOR realizará el mantenimiento preventivo de los equipos según el cronograma establecido. 
            En caso de mal funcionamiento, EL PRESTATARIO deberá contactar inmediatamente al servicio técnico 
            autorizado. No se permiten reparaciones por personal no autorizado.
        </div>
    </div>

    <div class="section">
        <div class="section-title">NOVENA: DEVOLUCIÓN DE EQUIPOS</div>
        <div class="content">
            <strong>9.1 Condiciones de Devolución:</strong> Los equipos deben ser devueltos en el mismo estado 
            en que fueron entregados, con todos sus accesorios y documentación.<br><br>
            
            <strong>9.2 Fecha de Devolución:</strong> Al finalizar el tratamiento o cuando sea requerido 
            por el personal médico.<br><br>
            
            <strong>9.3 Lugar de Devolución:</strong> En las instalaciones de EL PRESTADOR o en el lugar 
            que se indique al momento de la devolución.
        </div>
    </div>

    <div class="section">
        <div class="section-title">DÉCIMA: SANCIONES</div>
        <div class="content">
            El incumplimiento de las obligaciones establecidas en este contrato puede resultar en:
        </div>
        <ul style="margin-left: 20px;">
            <li>Suspensión del servicio de oxigenoterapia</li>
            <li>Responsabilidad por daños a los equipos</li>
            <li>Acciones legales si corresponde</li>
            <li>Denegación de futuros préstamos</li>
        </ul>
    </div>

    <div class="section">
        <div class="section-title">UNDÉCIMA: CONFIDENCIALIDAD</div>
        <div class="content">
            Ambas partes se comprometen a mantener la confidencialidad de toda la información médica y personal 
            del paciente, de acuerdo con las leyes de protección de datos personales vigentes.
        </div>
    </div>

    <div class="section">
        <div class="section-title">DUODÉCIMA: MODIFICACIONES</div>
        <div class="content">
            Cualquier modificación a este contrato deberá realizarse por escrito y ser firmada por ambas partes.
        </div>
    </div>

    <div class="section">
        <div class="section-title">DECIMOTERCERA: JURISDICCIÓN</div>
        <div class="content">
            Para cualquier controversia derivada de este contrato, las partes se someten a la jurisdicción 
            de los tribunales competentes del lugar donde se presta el servicio.
        </div>
    </div>

    <div class="signature-section">
        <div class="content">
            En testimonio de lo cual, las partes firman este contrato en {{ date('d/m/Y') }}, 
            manifestando que han leído, comprendido y aceptado todas las cláusulas establecidas.
        </div>
        
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">EL PRESTATARIO</div>
                <div style="text-align: center; margin-top: 5px;">{{ $pedido->nombre_apellido }}</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">EL PRESTADOR</div>
                <div style="text-align: center; margin-top: 5px;">Representante Autorizado</div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Documento de Identidad</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Sello Institucional</div>
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