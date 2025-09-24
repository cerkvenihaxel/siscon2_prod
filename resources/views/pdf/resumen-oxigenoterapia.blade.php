<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen del Pedido - Oxigenoterapia</title>
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
            border-left: 4px solid #dc3545;
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
            background-color: #e9ecef;
            font-weight: bold;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pendiente { background-color: #fff3cd; color: #856404; }
        .status-autorizado { background-color: #d1ecf1; color: #0c5460; }
        .status-prestamo { background-color: #d4edda; color: #155724; }
        .status-finalizado { background-color: #f8d7da; color: #721c24; }
        .status-rechazado { background-color: #f5c6cb; color: #721c24; }
        .timeline {
            border-left: 2px solid #007bff;
            padding-left: 20px;
            margin: 15px 0;
        }
        .timeline-item {
            margin-bottom: 10px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #007bff;
            border-radius: 50%;
            position: absolute;
            left: -26px;
            top: 5px;
        }
        .timeline-date {
            font-weight: bold;
            color: #007bff;
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
        <div class="title">RESUMEN DEL PEDIDO</div>
        <div class="subtitle">OXIGENOTERAPIA</div>
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
            <tr>
                <td class="label">Estado Actual:</td>
                <td colspan="3">
                    @php
                        $estadoClass = '';
                        $estadoText = '';
                        switch($pedido->estado_oxigenoterapia_id) {
                            case 1: $estadoClass = 'status-pendiente'; $estadoText = 'PENDIENTE'; break;
                            case 2: $estadoClass = 'status-autorizado'; $estadoText = 'AUTORIZADO'; break;
                            case 3: $estadoClass = 'status-prestamo'; $estadoText = 'EN PRÉSTAMO'; break;
                            case 4: $estadoClass = 'status-autorizado'; $estadoText = 'RENOVADO'; break;
                            case 5: $estadoClass = 'status-finalizado'; $estadoText = 'FINALIZADO'; break;
                            case 6: $estadoClass = 'status-rechazado'; $estadoText = 'RECHAZADO'; break;
                            default: $estadoClass = 'status-pendiente'; $estadoText = 'PENDIENTE';
                        }
                    @endphp
                    <span class="status-badge {{ $estadoClass }}">{{ $estadoText }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">1. EQUIPOS SOLICITADOS</div>
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
                    <td>
                        @if($material->entregable)
                            <span class="status-badge status-prestamo">ENTREGABLE</span>
                        @else
                            <span class="status-badge status-pendiente">PENDIENTE</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. INFORMACIÓN MÉDICA</div>
        <div class="content">
            <strong>Diagnóstico:</strong> {{ $pedido->diagnostico ?? 'No especificado' }}<br><br>
            <strong>Indicación Médica:</strong> {{ $pedido->indicacion_medica ?? 'No especificada' }}<br><br>
            <strong>Duración del Tratamiento:</strong> {{ $pedido->duracion_tratamiento ?? 'No especificada' }}<br><br>
            <strong>Observaciones Médicas:</strong> {{ $pedido->observaciones ?? 'Sin observaciones' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">3. CRONOLOGÍA DEL PEDIDO</div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->created_at)) }}</div>
                <div>Solicitud creada - Estado: PENDIENTE</div>
            </div>
            
            @if($pedido->estado_oxigenoterapia_id >= 2)
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->updated_at)) }}</div>
                <div>Solicitud autorizada por personal médico</div>
            </div>
            @endif
            
            @if($pedido->estado_oxigenoterapia_id >= 3)
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->updated_at)) }}</div>
                <div>Préstamo iniciado - Equipos entregados</div>
            </div>
            @endif
            
            @if($pedido->estado_oxigenoterapia_id == 4)
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->updated_at)) }}</div>
                <div>Préstamo renovado</div>
            </div>
            @endif
            
            @if($pedido->estado_oxigenoterapia_id == 5)
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->updated_at)) }}</div>
                <div>Préstamo finalizado - Equipos devueltos</div>
            </div>
            @endif
            
            @if($pedido->estado_oxigenoterapia_id == 6)
            <div class="timeline-item">
                <div class="timeline-date">{{ date('d/m/Y H:i', strtotime($pedido->updated_at)) }}</div>
                <div>Solicitud rechazada</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. INFORMACIÓN DE PRÉSTAMO</div>
        @if($pedido->estado_oxigenoterapia_id >= 3)
        <div class="content">
            @php
                $prestamo = \App\Models\PrestamoOxigenoterapia::where('pedido_oxigenoterapia_id', $pedido->id)->first();
            @endphp
            @if($prestamo)
            <strong>Número de Préstamo:</strong> {{ $prestamo->nro_prestamo }}<br><br>
            <strong>Fecha de Inicio:</strong> {{ date('d/m/Y', strtotime($prestamo->fecha_inicio)) }}<br><br>
            <strong>Fecha de Finalización:</strong> {{ date('d/m/Y', strtotime($prestamo->fecha_fin)) }}<br><br>
            <strong>Duración:</strong> {{ $prestamo->duracion_dias }} días<br><br>
            <strong>Dirección de Entrega:</strong> {{ $prestamo->direccion_entrega }}<br><br>
            <strong>Estado del Préstamo:</strong> 
            <span class="status-badge {{ $prestamo->estado_prestamo == 'ACTIVO' ? 'status-prestamo' : 'status-finalizado' }}">
                {{ $prestamo->estado_prestamo }}
            </span><br><br>
            <strong>Observaciones del Préstamo:</strong> {{ $prestamo->observaciones ?? 'Sin observaciones' }}
            @else
            <p>No se encontró información de préstamo asociada.</p>
            @endif
        </div>
        @else
        <div class="content">
            <p>El préstamo aún no ha sido iniciado.</p>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">5. DOCUMENTACIÓN ASOCIADA</div>
        <div class="content">
            <strong>Documentos Generados:</strong><br>
            • Consentimiento Informado<br>
            • Términos y Condiciones<br>
            • Contrato de Préstamo<br>
            • Instrucciones de Uso<br>
            • Checklist de Entrega<br>
            • Resumen del Pedido (este documento)<br><br>
            
            <strong>Documentos Pendientes:</strong><br>
            @if($pedido->estado_oxigenoterapia_id < 2)
            • Autorización médica<br>
            @endif
            @if($pedido->estado_oxigenoterapia_id < 3)
            • Firma de contrato de préstamo<br>
            • Entrega física de equipos<br>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">6. CONTACTOS IMPORTANTES</div>
        <div class="content">
            <strong>Médico Tratante:</strong> {{ $pedido->medicos->nombremedico ?? 'No especificado' }}<br>
            <strong>Clínica:</strong> {{ $pedido->clinica->nombre ?? 'No especificada' }}<br>
            <strong>Servicio de Oxigenoterapia:</strong> [NÚMERO DE CONTACTO]<br>
            <strong>Emergencias 24/7:</strong> [NÚMERO DE EMERGENCIA]<br>
            <strong>Servicio Técnico:</strong> [NÚMERO DE SERVICIO TÉCNICO]
        </div>
    </div>

    <div class="section">
        <div class="section-title">7. RESUMEN EJECUTIVO</div>
        <div class="content">
            <strong>Total de Equipos:</strong> {{ $pedido->materiales->count() }} tipos diferentes<br><br>
            <strong>Estado General:</strong> 
            @if($pedido->estado_oxigenoterapia_id == 1)
                Solicitud pendiente de autorización médica
            @elseif($pedido->estado_oxigenoterapia_id == 2)
                Solicitud autorizada, pendiente de entrega
            @elseif($pedido->estado_oxigenoterapia_id == 3)
                Préstamo activo - Equipos en uso
            @elseif($pedido->estado_oxigenoterapia_id == 4)
                Préstamo renovado - Continuando tratamiento
            @elseif($pedido->estado_oxigenoterapia_id == 5)
                Préstamo finalizado - Tratamiento completado
            @elseif($pedido->estado_oxigenoterapia_id == 6)
                Solicitud rechazada
            @endif<br><br>
            
            <strong>Próximos Pasos:</strong>
            @if($pedido->estado_oxigenoterapia_id == 1)
                • Esperar autorización médica
            @elseif($pedido->estado_oxigenoterapia_id == 2)
                • Coordinar entrega de equipos
            @elseif($pedido->estado_oxigenoterapia_id == 3)
                • Seguimiento del tratamiento
            @elseif($pedido->estado_oxigenoterapia_id == 4)
                • Continuar monitoreo
            @elseif($pedido->estado_oxigenoterapia_id == 5)
                • Archivar documentación
            @elseif($pedido->estado_oxigenoterapia_id == 6)
                • Contactar al médico para nueva evaluación
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Este resumen es parte del expediente médico del paciente y debe ser conservado.</p>
        <p>Generado el {{ date('d/m/Y H:i:s') }} - Sistema de Gestión de Oxigenoterapia</p>
    </div>
</body>
</html> 