@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-history"></i> Historial SOAP - {{ $consumo->desc }}
                </h3>
            </div>
            <div class="panel-body">
                <!-- Información del Consumo -->
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="fa fa-user"></i> Información del Consumo</h4>
                        <dl class="dl-horizontal">
                            <dt>Afiliado:</dt>
                            <dd><strong>{{ $consumo->afiliado }}</strong></dd>
                            <dt>Nombre:</dt>
                            <dd>{{ $consumo->apellidos }}, {{ $consumo->nombres }}</dd>
                            <dt>Plan:</dt>
                            <dd>{{ $consumo->nombre_modelo_plan }} ({{ $consumo->modelo_plan }})</dd>
                            <dt>Prestación:</dt>
                            <dd>{{ $consumo->desc }} ({{ $consumo->cod_prestacion }})</dd>
                            <dt>Cantidad:</dt>
                            <dd>{{ $consumo->cant }}</dd>
                            <dt>Importe:</dt>
                            <dd>${{ number_format($consumo->imptot, 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h4><i class="fa fa-cogs"></i> Estado del Flujo</h4>
                        <dl class="dl-horizontal">
                            <dt>Estado Actual:</dt>
                            <dd>{!! $consumo->estado_flujo_badge !!}</dd>
                            <dt>Fecha Transacción:</dt>
                            <dd>{{ $consumo->fecha_tran->format('d/m/Y H:i') }}</dd>
                            @if($consumo->fecha_elegibilidad)
                            <dt>Fecha ELG:</dt>
                            <dd>{{ $consumo->fecha_elegibilidad->format('d/m/Y H:i') }}</dd>
                            @endif
                            @if($consumo->fecha_aprobacion)
                            <dt>Fecha AP:</dt>
                            <dd>{{ $consumo->fecha_aprobacion->format('d/m/Y H:i') }}</dd>
                            @endif
                            @if($consumo->fecha_entrega)
                            <dt>Fecha Entrega:</dt>
                            <dd>{{ $consumo->fecha_entrega->format('d/m/Y H:i') }}</dd>
                            @endif
                            @if($consumo->fecha_anulacion)
                            <dt>Fecha Anulación:</dt>
                            <dd>{{ $consumo->fecha_anulacion->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <hr>

                <!-- Historial de Transacciones SOAP -->
                <h4><i class="fa fa-exchange"></i> Transacciones SOAP Realizadas</h4>

                @if($consumo->elegibilidad)
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-check-circle"></i> Elegibilidad (ELG)
                            <span class="badge badge-{{ $consumo->elegibilidad->status == 'OK' ? 'success' : 'danger' }}">
                                {{ $consumo->elegibilidad->status }}
                            </span>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>MSGID:</dt>
                                    <dd><code>{{ $consumo->elegibilidad->msgid }}</code></dd>
                                    <dt>IDTRAN:</dt>
                                    <dd><code>{{ $consumo->elegibilidad->idtran }}</code></dd>
                                    <dt>Fecha:</dt>
                                    <dd>{{ $consumo->elegibilidad->created_at->format('d/m/Y H:i:s') }}</dd>
                                    <dt>Usuario:</dt>
                                    <dd>{{ $consumo->usuario_elegibilidad }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>Código Respuesta:</dt>
                                    <dd><code>{{ $consumo->elegibilidad->response_code }}</code></dd>
                                    <dt>Mensaje:</dt>
                                    <dd>{{ $consumo->elegibilidad->response_message }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($consumo->autorizacion)
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-check"></i> Autorización Previa (AP)
                            <span class="badge badge-{{ $consumo->autorizacion->status == 'OK' ? 'success' : 'danger' }}">
                                {{ $consumo->autorizacion->status }}
                            </span>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>MSGID:</dt>
                                    <dd><code>{{ $consumo->autorizacion->msgid }}</code></dd>
                                    <dt>IDTRAN:</dt>
                                    <dd><code>{{ $consumo->autorizacion->idtran }}</code></dd>
                                    <dt>IDAUT:</dt>
                                    <dd><code>{{ $consumo->autorizacion->idaut }}</code></dd>
                                    <dt>Fecha:</dt>
                                    <dd>{{ $consumo->autorizacion->created_at->format('d/m/Y H:i:s') }}</dd>
                                    <dt>Usuario:</dt>
                                    <dd>{{ $consumo->usuario_aprobacion }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>Código Respuesta:</dt>
                                    <dd><code>{{ $consumo->autorizacion->response_code }}</code></dd>
                                    <dt>Mensaje:</dt>
                                    <dd>{{ $consumo->autorizacion->response_message }}</dd>
                                    <dt>Importe Total:</dt>
                                    <dd>${{ number_format($consumo->autorizacion->importe_total, 2, ',', '.') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($consumo->anulacion)
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-times-circle"></i> Anulación (ATR)
                            <span class="badge badge-{{ $consumo->anulacion->status == 'OK' ? 'success' : 'danger' }}">
                                {{ $consumo->anulacion->status }}
                            </span>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>MSGID:</dt>
                                    <dd><code>{{ $consumo->anulacion->msgid }}</code></dd>
                                    <dt>IDTRAN:</dt>
                                    <dd><code>{{ $consumo->anulacion->idtran }}</code></dd>
                                    <dt>Tipo Anulación:</dt>
                                    <dd><code>{{ $consumo->anulacion->tipoidanul }}</code></dd>
                                    <dt>ID Anulado:</dt>
                                    <dd><code>{{ $consumo->anulacion->idanul }}</code></dd>
                                    <dt>Fecha:</dt>
                                    <dd>{{ $consumo->anulacion->created_at->format('d/m/Y H:i:s') }}</dd>
                                    <dt>Usuario:</dt>
                                    <dd>{{ $consumo->usuario_anulacion }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="dl-horizontal">
                                    <dt>Código Respuesta:</dt>
                                    <dd><code>{{ $consumo->anulacion->response_code }}</code></dd>
                                    <dt>Mensaje:</dt>
                                    <dd>{{ $consumo->anulacion->response_message }}</dd>
                                    <dt>Motivo:</dt>
                                    <dd>{{ $consumo->anulacion->motivo }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(!$consumo->elegibilidad && !$consumo->autorizacion && !$consumo->anulacion)
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Sin transacciones SOAP:</strong> Este consumo aún no ha pasado por ninguna transacción SOAP.
                    Use los botones de acción para iniciar el flujo.
                </div>
                @endif

                <!-- Botones de Acción -->
                <hr>
                <div class="text-center">
                    <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Volver a Consumos
                    </a>
                    
                    @if($consumo->puedeEjecutarELG())
                    <a href="{{ CRUDBooster::mainpath('verificar-elegibilidad/' . $consumo->id) }}" 
                       class="btn btn-info" onclick="return confirm('¿Verificar elegibilidad para este consumo?')">
                        <i class="fa fa-check-circle"></i> Verificar Elegibilidad (ELG)
                    </a>
                    @endif
                    
                    @if($consumo->puedeEjecutarAP())
                    <a href="{{ CRUDBooster::mainpath('aprobar-prestacion/' . $consumo->id) }}" 
                       class="btn btn-success" onclick="return confirm('¿Aprobar prestación para este consumo?')">
                        <i class="fa fa-check"></i> Aprobar Prestación (AP)
                    </a>
                    @endif
                    
                    @if($consumo->puedeValidarEntrega())
                    <a href="{{ CRUDBooster::mainpath('validar-entrega/' . $consumo->id) }}" 
                       class="btn btn-primary" onclick="return confirm('¿Validar entrega de este consumo?')">
                        <i class="fa fa-truck"></i> Validar Entrega
                    </a>
                    @endif
                    
                    @if($consumo->puedeAnular())
                    <a href="{{ CRUDBooster::mainpath('anular-transaccion/' . $consumo->id) }}" 
                       class="btn btn-danger" onclick="return confirm('¿ANULAR esta transacción? Esta acción es irreversible.')">
                        <i class="fa fa-times-circle"></i> Anular Transacción (ATR)
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
