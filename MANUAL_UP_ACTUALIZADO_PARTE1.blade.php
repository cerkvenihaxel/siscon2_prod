@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-book"></i> Manual de Usuario - Flujo Convenio UP Reestructurado
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-success">
                    <h4><i class="fa fa-check-circle"></i> Flujo Mejorado para Farmacias UP</h4>
                    <p>Este manual describe el nuevo flujo reestructurado para la validación y entrega de medicamentos a afiliados de Unión Personal.</p>
                </div>

                <!-- Índice -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-list"></i> Índice</h4>
                    </div>
                    <div class="panel-body">
                        <ol>
                            <li><a href="#introduccion">Introducción al Nuevo Flujo</a></li>
                            <li><a href="#paso1">Paso 1: Consultar Consumos UP</a></li>
                            <li><a href="#paso2">Paso 2: Consultar Elegibilidad</a></li>
                            <li><a href="#paso3">Paso 3: Aprobar Prestación</a></li>
                            <li><a href="#paso4">Paso 4: Generar Validación de Entrega</a></li>
                            <li><a href="#dashboard">Dashboard de Farmacia</a></li>
                            <li><a href="#estados">Estados del Sistema</a></li>
                            <li><a href="#errores">Resolución de Errores</a></li>
                        </ol>
                    </div>
                </div>

                <!-- Introducción -->
                <div id="introduccion" class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-info"></i> 1. Introducción al Nuevo Flujo</h4>
                    </div>
                    <div class="panel-body">
                        <p><strong>El nuevo flujo de convenio UP está diseñado específicamente para farmacias:</strong></p>
                        
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-search fa-3x text-primary"></i>
                                    <h5>1. Consultar Consumos</h5>
                                    <p>Buscar consumos del afiliado</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-user-check fa-3x text-info"></i>
                                    <h5>2. Consultar Elegibilidad</h5>
                                    <p>Verificar si puede recibir el medicamento</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-check-circle fa-3x text-success"></i>
                                    <h5>3. Aprobar Prestación</h5>
                                    <p>Obtener autorización de UP</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="well">
                                    <i class="fa fa-clipboard-check fa-3x text-primary"></i>
                                    <h5>4. Validar Entrega</h5>
                                    <p>Registrar entrega al afiliado</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <strong><i class="fa fa-star"></i> Mejoras del Nuevo Flujo:</strong>
                            <ul>
                                <li>Proceso más intuitivo para farmacias</li>
                                <li>Dashboard con métricas en tiempo real</li>
                                <li>Validación completa de entregas</li>
                                <li>Trazabilidad completa del proceso</li>
                                <li>Búsqueda mejorada por afiliado</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paso 1 -->
                <div id="paso1" class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-search"></i> Paso 1: Consultar Consumos UP</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>Objetivo:</strong> Encontrar los consumos disponibles para un afiliado</h5>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>Acceda a <strong>Convenio UP → 1. Consultar Consumos UP</strong></li>
                                    <li>Use el <strong>filtro por afiliado</strong>:
                                        <ul>
                                            <li>Ingrese el número de afiliado (ej: 54715500)</li>
                                            <li>Seleccione rango de fechas si es necesario</li>
                                            <li>Filtre por estado si desea</li>
                                        </ul>
                                    </li>
                                    <li>Revise los consumos disponibles</li>
                                    <li>Identifique consumos con estado <span class="badge badge-default">PENDIENTE</span></li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-lightbulb-o"></i> Tip:</strong><br>
                                    Use el <strong>Dashboard de Farmacia</strong> para ver consumos pendientes de forma rápida.
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Estados de Consumos</strong>
                            </div>
                            <div class="panel-body">
                                <span class="badge badge-default">PENDIENTE</span> - Listo para consultar elegibilidad<br>
                                <span class="badge badge-info">ELEGIBILIDAD OK</span> - Listo para aprobar<br>
                                <span class="badge badge-success">APROBADO</span> - Listo para generar validación<br>
                                <span class="badge badge-primary">ENTREGADO</span> - Proceso completado
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div id="paso2" class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-user-check"></i> Paso 2: Consultar Elegibilidad</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>Objetivo:</strong> Verificar si el afiliado puede recibir el medicamento</h5>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>En un consumo con estado <span class="badge badge-default">PENDIENTE</span></li>
                                    <li>Haga clic en el botón <button class="btn btn-info btn-xs"><i class="fa fa-user-check"></i> Consultar Elegibilidad</button></li>
                                    <li>El sistema consultará automáticamente a Unión Personal</li>
                                    <li>Espere la respuesta (puede tomar unos segundos)</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong><br>
                                    Esta consulta verifica el estado del afiliado y la cobertura del medicamento.
                                </div>
                            </div>
                        </div>

                        <h5><strong>Resultados Posibles:</strong></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <strong><i class="fa fa-check"></i> Elegibilidad Confirmada</strong>
                                    </div>
                                    <div class="panel-body">
                                        <ul>
                                            <li>Estado cambia a <span class="badge badge-info">ELEGIBILIDAD OK</span></li>
                                            <li>Aparece botón "Aprobar Prestación"</li>
                                            <li>El afiliado puede recibir el medicamento</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <strong><i class="fa fa-times"></i> Elegibilidad Rechazada</strong>
                                    </div>
                                    <div class="panel-body">
                                        <ul>
                                            <li>Estado cambia a <span class="badge badge-danger">ELEGIBILIDAD NO</span></li>
                                            <li>Se muestra el motivo del rechazo</li>
                                            <li>Puede reintentar si corrige el problema</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div id="paso3" class="panel panel-success">
                    <div class="panel-heading">
                        <h4><i class="fa fa-check-circle"></i> Paso 3: Aprobar Prestación</h4>
                    </div>
                    <div class="panel-body">
                        <h5><strong>Objetivo:</strong> Obtener la autorización formal de Unión Personal</h5>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>En un consumo con estado <span class="badge badge-info">ELEGIBILIDAD OK</span></li>
                                    <li>Haga clic en el botón <button class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i> Aprobar Prestación</button></li>
                                    <li>El sistema solicitará autorización a UP</li>
                                    <li>Se generará el número IDAUT para facturación</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <strong><i class="fa fa-key"></i> IDAUT:</strong><br>
                                    Es el número de autorización que necesita para facturar a UP.
                                </div>
                            </div>
                        </div>

                        <h5><strong>Resultados:</strong></h5>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <strong>Si es exitoso:</strong>
                                <ul>
                                    <li>Estado cambia a <span class="badge badge-success">APROBADO</span></li>
                                    <li>Se obtiene el número IDAUT</li>
                                    <li>Aparece botón "Generar Validación de Entrega"</li>
                                    <li>Ya puede entregar el medicamento al afiliado</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
