@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-book"></i> Manual de Usuario - Flujo de Validación Unión Personal
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> Bienvenido al Sistema de Validación UP</h4>
                    <p>Este manual le guiará paso a paso en el proceso de validación de consumos con Unión Personal.</p>
                </div>

                <!-- Índice -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-list"></i> Índice</h4>
                    </div>
                    <div class="panel-body">
                        <ol>
                            <li><a href="#introduccion">Introducción al Flujo</a></li>
                            <li><a href="#paso1">Paso 1: Buscar Consumo</a></li>
                            <li><a href="#paso2">Paso 2: Verificar Elegibilidad (ELG)</a></li>
                            <li><a href="#paso3">Paso 3: Aprobar Prestación (AP)</a></li>
                            <li><a href="#paso4">Paso 4: Validar Entrega</a></li>
                            <li><a href="#paso5">Paso 5: Anular (si es necesario)</a></li>
                            <li><a href="#estados">Estados del Sistema</a></li>
                            <li><a href="#errores">Resolución de Errores</a></li>
                            <li><a href="#datos-prueba">Datos de Prueba</a></li>
                        </ol>
                    </div>
                </div>

                <!-- Introducción -->
                <div id="introduccion" class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-info"></i> 1. Introducción al Flujo</h4>
                    </div>
                    <div class="panel-body">
                        <p><strong>El flujo de validación con Unión Personal consta de 4 pasos principales:</strong></p>
                        
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-search fa-3x text-primary"></i>
                                    <h5>1. Buscar Consumo</h5>
                                    <p>Localizar el consumo a validar</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-check-circle fa-3x text-info"></i>
                                    <h5>2. Elegibilidad (ELG)</h5>
                                    <p>Verificar si el afiliado puede recibir la prestación</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-check fa-3x text-success"></i>
                                    <h5>3. Aprobación (AP)</h5>
                                    <p>Autorizar la prestación específica</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-truck fa-3x text-primary"></i>
                                    <h5>4. Validar Entrega</h5>
                                    <p>Confirmar que se entregó al paciente</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong> 
                            Cada paso debe completarse exitosamente antes de continuar al siguiente. 
                            El sistema no permitirá saltar pasos.
                        </div>
                    </div>
                </div>

                <!-- Paso 1 -->
                <div id="paso1" class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-search"></i> Paso 1: Buscar Consumo</h4>
                    </div>
                    <div class="panel-body">
                        <ol>
                            <li>Vaya a <strong>Consumos UP</strong> desde el menú principal</li>
                            <li>Use los <strong>filtros</strong> para buscar:
                                <ul>
                                    <li><strong>Por afiliado:</strong> Ingrese el número de afiliado</li>
                                    <li><strong>Por fecha:</strong> Seleccione rango de fechas</li>
                                    <li><strong>Por estado:</strong> Filtre por estado del flujo</li>
                                </ul>
                            </li>
                            <li>Identifique el consumo con estado <span class="badge badge-default">PENDIENTE</span></li>
                            <li>Verifique que los datos sean correctos:
                                <ul>
                                    <li>Número de afiliado</li>
                                    <li>Prestación solicitada</li>
                                    <li>Cantidad</li>
                                    <li>Importe</li>
                                </ul>
                            </li>
                        </ol>

                        <div class="alert alert-info">
                            <strong><i class="fa fa-lightbulb-o"></i> Tip:</strong> 
                            Use el botón <strong>"Ver Historial SOAP"</strong> para ver todas las transacciones realizadas en un consumo.
                        </div>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div id="paso2" class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-check-circle"></i> Paso 2: Verificar Elegibilidad (ELG)</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>¿Qué hace la Elegibilidad?</strong></h5>
                        <p>Verifica si el afiliado tiene derecho a recibir la prestación solicitada según su plan y estado de afiliación.</p>

                        <h5><strong>Cómo ejecutar:</strong></h5>
                        <ol>
                            <li>En la fila del consumo, haga clic en <button class="btn btn-info btn-xs"><i class="fa fa-check-circle"></i> Verificar Elegibilidad (ELG)</button></li>
                            <li>El sistema automáticamente:
                                <ul>
                                    <li>Consulta a Unión Personal</li>
                                    <li>Verifica los datos del afiliado</li>
                                    <li>Valida el plan y la prestación</li>
                                    <li>Actualiza el estado del consumo</li>
                                </ul>
                            </li>
                        </ol>

                        <h5><strong>Resultados posibles:</strong></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong><i class="fa fa-check"></i> Elegibilidad OK</strong>
                                    <ul>
                                        <li>Estado cambia a <span class="badge badge-info">ELEGIBILIDAD OK</span></li>
                                        <li>Se genera un IDTRAN</li>
                                        <li>Puede continuar con Aprobación (AP)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-danger">
                                    <strong><i class="fa fa-times"></i> Elegibilidad Rechazada</strong>
                                    <ul>
                                        <li>Estado cambia a <span class="badge badge-danger">ELEGIBILIDAD NO</span></li>
                                        <li>Se muestra el motivo del rechazo</li>
                                        <li>No se puede continuar el flujo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div id="paso3" class="panel panel-success">
                    <div class="panel-heading">
                        <h4><i class="fa fa-check"></i> Paso 3: Aprobar Prestación (AP)</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>¿Qué hace la Aprobación?</strong></h5>
                        <p>Autoriza específicamente la prestación solicitada y genera un número de autorización (IDAUT) necesario para la facturación.</p>

                        <h5><strong>Prerrequisito:</strong></h5>
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong> 
                            Solo aparece este botón si la Elegibilidad fue exitosa.
                        </div>

                        <h5><strong>Cómo ejecutar:</strong></h5>
                        <ol>
                            <li>En la fila del consumo con estado <span class="badge badge-info">ELEGIBILIDAD OK</span>, haga clic en <button class="btn btn-success btn-xs"><i class="fa fa-check"></i> Aprobar Prestación (AP)</button></li>
                            <li>El sistema automáticamente:
                                <ul>
                                    <li>Solicita autorización a Unión Personal</li>
                                    <li>Genera un IDAUT (número de autorización)</li>
                                    <li>Calcula importes y copagos</li>
                                    <li>Actualiza el estado del consumo</li>
                                </ul>
                            </li>
                        </ol>

                        <h5><strong>Resultados posibles:</strong></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong><i class="fa fa-check"></i> Aprobación OK</strong>
                                    <ul>
                                        <li>Estado cambia a <span class="badge badge-success">APROBADO</span></li>
                                        <li>Se genera un IDAUT</li>
                                        <li>Puede validar entrega</li>
                                        <li>Puede anular si es necesario</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-danger">
                                    <strong><i class="fa fa-times"></i> Aprobación Rechazada</strong>
                                    <ul>
                                        <li>Estado cambia a <span class="badge badge-danger">RECHAZADO</span></li>
                                        <li>Se muestra el motivo del rechazo</li>
                                        <li>No se puede continuar el flujo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div id="paso4" class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-truck"></i> Paso 4: Validar Entrega</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>¿Qué hace la Validación de Entrega?</strong></h5>
                        <p>Confirma que la prestación fue efectivamente entregada al paciente. Este paso es interno del sistema y no requiere comunicación con Unión Personal.</p>

                        <h5><strong>Prerrequisito:</strong></h5>
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong> 
                            Solo aparece este botón si la Aprobación fue exitosa.
                        </div>

                        <h5><strong>Cómo ejecutar:</strong></h5>
                        <ol>
                            <li>En la fila del consumo con estado <span class="badge badge-success">APROBADO</span>, haga clic en <button class="btn btn-primary btn-xs"><i class="fa fa-truck"></i> Validar Entrega</button></li>
                            <li>Confirme que efectivamente se entregó la prestación al paciente</li>
                            <li>El sistema:
                                <ul>
                                    <li>Marca el consumo como entregado</li>
                                    <li>Registra fecha y usuario de entrega</li>
                                    <li>Completa el flujo exitosamente</li>
                                </ul>
                            </li>
                        </ol>

                        <div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Resultado:</strong>
                            <ul>
                                <li>Estado cambia a <span class="badge badge-primary">ENTREGADO</span></li>
                                <li>Flujo completado exitosamente</li>
                                <li>Aún puede anular si es necesario</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paso 5 -->
                <div id="paso5" class="panel panel-warning">
                    <div class="panel-heading">
                        <h4><i class="fa fa-times-circle"></i> Paso 5: Anular (ATR) - Solo si es necesario</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>¿Cuándo usar la Anulación?</strong></h5>
                        <div class="alert alert-danger">
                            <strong><i class="fa fa-exclamation-triangle"></i> ¡CUIDADO!</strong> 
                            La anulación es <strong>irreversible</strong> y debe usarse solo en casos excepcionales.
                        </div>

                        <p><strong>Casos válidos para anular:</strong></p>
                        <ul>
                            <li>Error en la prestación autorizada</li>
                            <li>Paciente no retiró la prestación</li>
                            <li>Error administrativo</li>
                            <li>Solicitud del paciente</li>
                        </ul>

                        <h5><strong>Prerrequisito:</strong></h5>
                        <p>Solo se puede anular consumos con estado <span class="badge badge-success">APROBADO</span> o <span class="badge badge-primary">ENTREGADO</span></p>

                        <h5><strong>Cómo ejecutar:</strong></h5>
                        <ol>
                            <li>En la fila del consumo, haga clic en <button class="btn btn-danger btn-xs"><i class="fa fa-times-circle"></i> Anular Transacción (ATR)</button></li>
                            <li><strong>Confirme la acción</strong> - aparecerá un mensaje de advertencia</li>
                            <li>El sistema automáticamente:
                                <ul>
                                    <li>Envía solicitud de anulación a Unión Personal</li>
                                    <li>Anula la autorización (IDAUT)</li>
                                    <li>Registra el motivo de anulación</li>
                                    <li>Actualiza el estado del consumo</li>
                                </ul>
                            </li>
                        </ol>

                        <div class="alert alert-warning">
                            <strong><i class="fa fa-info-circle"></i> Resultado:</strong>
                            <ul>
                                <li>Estado cambia a <span class="badge badge-warning">ANULADO</span></li>
                                <li>Se genera un nuevo IDTRAN de anulación</li>
                                <li>La autorización queda sin efecto</li>
                                <li>No se puede revertir esta acción</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Estados -->
                <div id="estados" class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-cogs"></i> Estados del Sistema</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Descripción</th>
                                        <th>Acciones Disponibles</th>
                                        <th>Siguiente Paso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-default">PENDIENTE</span></td>
                                        <td>Consumo inicial sin procesar</td>
                                        <td>Verificar Elegibilidad</td>
                                        <td>ELG</td>
                                    </tr>
                                    <tr class="success">
                                        <td><span class="badge badge-info">ELEGIBILIDAD OK</span></td>
                                        <td>Afiliado elegible para la prestación</td>
                                        <td>Aprobar Prestación</td>
                                        <td>AP</td>
                                    </tr>
                                    <tr class="danger">
                                        <td><span class="badge badge-danger">ELEGIBILIDAD NO</span></td>
                                        <td>Afiliado no elegible</td>
                                        <td>Verificar Elegibilidad (reintentar)</td>
                                        <td>Revisar datos</td>
                                    </tr>
                                    <tr class="success">
                                        <td><span class="badge badge-success">APROBADO</span></td>
                                        <td>Prestación autorizada con IDAUT</td>
                                        <td>Validar Entrega, Anular</td>
                                        <td>Entrega</td>
                                    </tr>
                                    <tr class="danger">
                                        <td><span class="badge badge-danger">RECHAZADO</span></td>
                                        <td>Prestación no autorizada</td>
                                        <td>Ninguna</td>
                                        <td>Fin del flujo</td>
                                    </tr>
                                    <tr class="info">
                                        <td><span class="badge badge-primary">ENTREGADO</span></td>
                                        <td>Prestación entregada al paciente</td>
                                        <td>Anular</td>
                                        <td>Flujo completado</td>
                                    </tr>
                                    <tr class="warning">
                                        <td><span class="badge badge-warning">ANULADO</span></td>
                                        <td>Transacción anulada</td>
                                        <td>Ninguna</td>
                                        <td>Fin del flujo</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Errores -->
                <div id="errores" class="panel panel-danger">
                    <div class="panel-heading">
                        <h4><i class="fa fa-exclamation-triangle"></i> Resolución de Errores Comunes</h4>
                    </div>
                    <div class="panel-body">
                        <div class="accordion" id="errores-accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#error1">
                                            <i class="fa fa-question-circle"></i> "Afiliado no encontrado"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas posibles:</strong>
                                        <ul>
                                            <li>Número de afiliado incorrecto</li>
                                            <li>Afiliado dado de baja</li>
                                            <li>Error de tipeo</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Verificar el número de afiliado</li>
                                            <li>Consultar con el paciente</li>
                                            <li>Verificar en el sistema de UP</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#error2">
                                            <i class="fa fa-question-circle"></i> "Prestación no autorizada"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas posibles:</strong>
                                        <ul>
                                            <li>Prestación no incluida en el plan</li>
                                            <li>Límites de cobertura agotados</li>
                                            <li>Requiere autorización previa especial</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Revisar cobertura del plan</li>
                                            <li>Contactar a UP para autorización especial</li>
                                            <li>Considerar prestación alternativa</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#error3">
                                            <i class="fa fa-question-circle"></i> "Error de conexión SOAP"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas posibles:</strong>
                                        <ul>
                                            <li>Problemas de conectividad</li>
                                            <li>Servicio de UP temporalmente no disponible</li>
                                            <li>Credenciales incorrectas</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Verificar conexión a internet</li>
                                            <li>Reintentar en unos minutos</li>
                                            <li>Contactar soporte técnico</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Prueba -->
                <div id="datos-prueba" class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-flask"></i> Datos de Prueba</h4>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Solo para Ambiente de Testing</strong>
                            <p>Estos datos solo funcionan en el ambiente de pruebas de Unión Personal.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>Afiliados de Prueba:</strong></h5>
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Plan</th>
                                            <th>VerCred</th>
                                            <th>Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td><code>54715500</code></td>
                                            <td>150 (Accord)</td>
                                            <td>45</td>
                                            <td>✅ OK</td>
                                        </tr>
                                        <tr class="success">
                                            <td><code>54715300</code></td>
                                            <td>2 (Básico)</td>
                                            <td>31</td>
                                            <td>✅ OK</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5><strong>Prestaciones de Prueba:</strong></h5>
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td><code>1420107</code></td>
                                            <td>Consulta Especializada</td>
                                            <td>✅ OK</td>
                                        </tr>
                                        <tr class="success">
                                            <td><code>1420101</code></td>
                                            <td>Consulta Médica</td>
                                            <td>✅ OK</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h5><strong>TOKEN de Desarrollo:</strong></h5>
                        <p>Use el TOKEN <code>9999</code> para pruebas. Este TOKEN no expira y siempre devuelve resultados exitosos.</p>
                    </div>
                </div>

                <!-- Botones de Navegación -->
                <div class="text-center" style="margin-top: 30px;">
                    <a href="{{ CRUDBooster::adminPath('up_consumos') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-arrow-left"></i> Volver a Consumos UP
                    </a>
                    <a href="{{ CRUDBooster::adminPath('up_elegibilidad') }}" class="btn btn-info">
                        <i class="fa fa-check-circle"></i> Elegibilidad (ELG)
                    </a>
                    <a href="{{ CRUDBooster::adminPath('up_autorizacion_previa') }}" class="btn btn-success">
                        <i class="fa fa-check"></i> Autorización (AP)
                    </a>
                    <a href="{{ CRUDBooster::adminPath('up_anulaciones') }}" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i> Anulaciones (ATR)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel-heading h4 {
    margin: 0;
}
.well {
    min-height: 150px;
}
.badge {
    font-size: 11px;
}
</style>

<script>
$(document).ready(function() {
    // Smooth scrolling para los enlaces del índice
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });
});
</script>
@endsection
