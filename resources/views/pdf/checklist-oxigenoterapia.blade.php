<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist de Entrega - Oxigenoterapia</title>
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
            border-left: 4px solid #28a745;
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
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .checklist-table th,
        .checklist-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .checklist-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            display: inline-block;
            margin-right: 10px;
        }
        .equipment-section {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        .equipment-section h4 {
            color: #28a745;
            margin-bottom: 8px;
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
        <div class="title">CHECKLIST DE ENTREGA</div>
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

    <div class="section">
        <div class="section-title">1. VERIFICACIÓN DE IDENTIDAD DEL PACIENTE</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Verificar documento de identidad del paciente
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Confirmar número de afiliado
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Verificar datos de contacto actualizados
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Confirmar dirección de entrega
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. VERIFICACIÓN DE AUTORIZACIÓN MÉDICA</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Verificar prescripción médica vigente
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Confirmar parámetros de oxigenoterapia prescritos
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Verificar duración del tratamiento autorizado
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Confirmar que el paciente ha firmado el consentimiento informado
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">3. VERIFICACIÓN DE EQUIPOS</div>
        @foreach($pedido->materiales as $material)
        <div class="equipment-section">
            <h4>{{ $material->equipo->nombre_equipo ?? 'Equipo no especificado' }} - Cantidad: {{ $material->cantidad }}</h4>
            <table class="checklist-table">
                <tr>
                    <td width="80%">
                        <div class="checkbox"></div>
                        Equipo en buen estado físico
                    </td>
                    <td width="20%">□ Sí □ No</td>
                </tr>
                <tr>
                    <td>
                        <div class="checkbox"></div>
                        Funcionamiento correcto verificado
                    </td>
                    <td>□ Sí □ No</td>
                </tr>
                <tr>
                    <td>
                        <div class="checkbox"></div>
                        Todos los accesorios incluidos
                    </td>
                    <td>□ Sí □ No</td>
                </tr>
                <tr>
                    <td>
                        <div class="checkbox"></div>
                        Manual de instrucciones incluido
                    </td>
                    <td>□ Sí □ No</td>
                </tr>
                <tr>
                    <td>
                        <div class="checkbox"></div>
                        Garantía y documentación técnica
                    </td>
                    <td>□ Sí □ No</td>
                </tr>
                <tr>
                    <td>
                        <div class="checkbox"></div>
                        Número de serie registrado
                    </td>
                    <td>□ Sí □ No</td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">4. VERIFICACIÓN DE ACCESORIOS</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Cánulas nasales (cantidad suficiente)
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Tubos de conexión
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Filtros de aire (si aplica)
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Adaptadores necesarios
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Cables de alimentación
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">5. VERIFICACIÓN DE DOCUMENTACIÓN</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Consentimiento informado firmado
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Términos y condiciones entregados
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Contrato de préstamo firmado
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Instrucciones de uso entregadas
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Tarjeta de contacto de emergencia
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">6. DEMOSTRACIÓN Y CAPACITACIÓN</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Funcionamiento del equipo demostrado
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Instrucciones de seguridad explicadas
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Procedimientos de limpieza explicados
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Contactos de emergencia proporcionados
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Paciente/cuidador confirma comprensión
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">7. VERIFICACIÓN DEL ENTORNO</div>
        <table class="checklist-table">
            <tr>
                <td width="80%">
                    <div class="checkbox"></div>
                    Ubicación adecuada para el equipo
                </td>
                <td width="20%">□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Toma de corriente disponible y funcional
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Ventilación adecuada en el área
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Distancia segura de fuentes de calor
                </td>
                <td>□ Sí □ No</td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox"></div>
                    Acceso fácil para mantenimiento
                </td>
                <td>□ Sí □ No</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">8. OBSERVACIONES Y COMENTARIOS</div>
        <table class="checklist-table">
            <tr>
                <td colspan="2" style="height: 100px; vertical-align: top;">
                    <strong>Observaciones del técnico:</strong><br><br>
                    _________________________________________________________________<br>
                    _________________________________________________________________<br>
                    _________________________________________________________________<br>
                    _________________________________________________________________
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 100px; vertical-align: top;">
                    <strong>Comentarios del paciente/cuidador:</strong><br><br>
                    _________________________________________________________________<br>
                    _________________________________________________________________<br>
                    _________________________________________________________________<br>
                    _________________________________________________________________
                </td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Firma del Técnico</div>
                <div style="text-align: center; margin-top: 5px;">Nombre: _______________________</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Firma del Paciente/Cuidador</div>
                <div style="text-align: center; margin-top: 5px;">Nombre: _______________________</div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div style="margin-top: 30px;">
            <div style="float: left; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Fecha y Hora</div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature-line"></div>
                <div style="text-align: center; margin-top: 5px;">Fecha y Hora</div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <div class="footer">
        <p>Este checklist debe ser completado en cada entrega de equipos.</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }} - Sistema de Gestión de Oxigenoterapia</p>
    </div>
</body>
</html> 