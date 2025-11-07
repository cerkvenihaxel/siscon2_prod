                <!-- Paso 4 -->
                <div id="paso4" class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-clipboard-check"></i> Paso 4: Generar Validación de Entrega</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>Objetivo:</strong> Registrar que el medicamento fue entregado al afiliado</h5>
                        
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> NUEVO:</strong> 
                            Este paso reemplaza el simple "Marcar Entregado" y genera un registro completo de validación.
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>En un consumo con estado <span class="badge badge-success">APROBADO</span></li>
                                    <li>Entregue físicamente el medicamento al afiliado</li>
                                    <li>Haga clic en <button class="btn btn-primary btn-xs"><i class="fa fa-clipboard-check"></i> Generar Validación de Entrega</button></li>
                                    <li>Se creará automáticamente el registro de validación</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-database"></i> Registro:</strong><br>
                                    Se guarda información completa de la entrega para auditoría.
                                </div>
                            </div>
                        </div>

                        <h5><strong>Información Registrada:</strong></h5>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul>
                                            <li>Fecha y hora de entrega</li>
                                            <li>Usuario que entregó</li>
                                            <li>Datos del afiliado</li>
                                            <li>Medicamento y cantidad</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul>
                                            <li>Número IDAUT</li>
                                            <li>Importe autorizado</li>
                                            <li>Estado: <span class="badge badge-primary">ENTREGADO</span></li>
                                            <li>Trazabilidad completa</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Proceso Completado:</strong><br>
                            Una vez generada la validación, el flujo está completo y el registro queda disponible para auditoría y facturación.
                        </div>
                    </div>
                </div>

                <!-- Dashboard -->
                <div id="dashboard" class="panel panel-warning">
                    <div class="panel-heading">
                        <h4><i class="fa fa-dashboard"></i> Dashboard de Farmacia</h4>
                    </div>
                    <div class="panel-body">
                        <p><strong>Acceso:</strong> Convenio UP → Dashboard Farmacia</p>
                        
                        <h5><strong>Funcionalidades:</strong></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <strong>Métricas en Tiempo Real</strong>
                                    </div>
                                    <div class="panel-body">
                                        <ul>
                                            <li>Consumos pendientes hoy</li>
                                            <li>Elegibilidades confirmadas</li>
                                            <li>Prestaciones aprobadas</li>
                                            <li>Entregas validadas</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <strong>Accesos Rápidos</strong>
                                    </div>
                                    <div class="panel-body">
                                        <ul>
                                            <li>Búsqueda rápida por afiliado</li>
                                            <li>Consumos pendientes de procesar</li>
                                            <li>Listos para entrega</li>
                                            <li>Estadísticas del mes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong><i class="fa fa-lightbulb-o"></i> Tip:</strong> 
                            Use el dashboard como punto de partida para gestionar eficientemente los consumos UP.
                        </div>
                    </div>
                </div>

                <!-- Estados -->
                <div id="estados" class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-list"></i> Estados del Sistema</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Descripción</th>
                                        <th>Acción Disponible</th>
                                        <th>Siguiente Paso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-default">PENDIENTE</span></td>
                                        <td>Consumo sin procesar</td>
                                        <td><button class="btn btn-info btn-xs">Consultar Elegibilidad</button></td>
                                        <td>Verificar si el afiliado puede recibir el medicamento</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info">ELEGIBILIDAD OK</span></td>
                                        <td>Afiliado elegible</td>
                                        <td><button class="btn btn-success btn-xs">Aprobar Prestación</button></td>
                                        <td>Obtener autorización de UP</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-danger">ELEGIBILIDAD NO</span></td>
                                        <td>Afiliado no elegible</td>
                                        <td><button class="btn btn-info btn-xs">Consultar Elegibilidad</button></td>
                                        <td>Revisar datos y reintentar</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-success">APROBADO</span></td>
                                        <td>Prestación autorizada</td>
                                        <td><button class="btn btn-primary btn-xs">Generar Validación</button></td>
                                        <td>Entregar medicamento y validar</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-danger">RECHAZADO</span></td>
                                        <td>Prestación no autorizada</td>
                                        <td>-</td>
                                        <td>Fin del proceso</td>
                                    </tr>
                                    <tr class="success">
                                        <td><span class="badge badge-primary">ENTREGADO</span></td>
                                        <td>Medicamento entregado</td>
                                        <td>-</td>
                                        <td>Proceso completado</td>
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
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#error1">
                                            Error: "Afiliado no encontrado"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas:</strong>
                                        <ul>
                                            <li>Número de afiliado incorrecto</li>
                                            <li>Afiliado dado de baja</li>
                                            <li>Error de tipeo</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Verificar el número con la credencial del paciente</li>
                                            <li>Consultar directamente con UP</li>
                                            <li>Revisar si hay espacios o caracteres extra</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#error2">
                                            Error: "Prestación no autorizada"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas:</strong>
                                        <ul>
                                            <li>Medicamento no cubierto por el plan</li>
                                            <li>Límites de cobertura agotados</li>
                                            <li>Requiere autorización previa especial</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Revisar cobertura del plan del afiliado</li>
                                            <li>Contactar a UP para autorización especial</li>
                                            <li>Considerar medicamento alternativo</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#error3">
                                            Error: "No se puede generar validación"
                                        </a>
                                    </h4>
                                </div>
                                <div id="error3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <strong>Causas:</strong>
                                        <ul>
                                            <li>El consumo no está en estado APROBADO</li>
                                            <li>Falta el número IDAUT</li>
                                            <li>Ya se generó una validación</li>
                                        </ul>
                                        <strong>Solución:</strong>
                                        <ol>
                                            <li>Verificar que el estado sea APROBADO</li>
                                            <li>Completar primero los pasos anteriores</li>
                                            <li>Revisar si ya existe una validación</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Prueba -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-flask"></i> Datos de Prueba (Ambiente Testing)</h4>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Solo para Testing:</strong> 
                            Estos datos funcionan únicamente en el ambiente de pruebas.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>Afiliados de Prueba:</strong></h5>
                                <ul>
                                    <li><strong>54715500</strong> - Plan 150 (Accord)</li>
                                    <li><strong>54715300</strong> - Plan 2 (Básico)</li>
                                </ul>

                                <h5><strong>TOKEN de Desarrollo:</strong></h5>
                                <ul>
                                    <li><strong>9999</strong> - No expira, siempre OK</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><strong>Medicamentos de Prueba:</strong></h5>
                                <ul>
                                    <li><strong>1420107</strong> - Consulta Especializada</li>
                                    <li><strong>1420101</strong> - Consulta Médica</li>
                                </ul>

                                <h5><strong>Ambiente:</strong></h5>
                                <ul>
                                    <li><strong>URL:</strong> http://181.13.241.19:7002/cawsTest/Servicios</li>
                                    <li><strong>Usuario:</strong> 8888</li>
                                    <li><strong>Password:</strong> 7777</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="alert alert-success">
                    <h4><i class="fa fa-check-circle"></i> ¡Flujo Completado!</h4>
                    <p>Una vez que complete los 4 pasos, habrá procesado exitosamente un consumo UP con trazabilidad completa.</p>
                    <p><strong>Recuerde:</strong> Cada paso debe completarse en orden y el sistema registra toda la información para auditoría.</p>
                </div>
            </div>
        </div>
    </div>
</div>

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

<style>
.well {
    min-height: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.badge {
    font-size: 11px;
}

.panel-title a {
    text-decoration: none;
}

.panel-title a:hover {
    text-decoration: none;
}

.alert h4 {
    margin-top: 0;
}
</style>
@endsection
