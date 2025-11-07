@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Estado Actual -->
        <div class="panel panel-{{ $ambiente_actual == 'test' ? 'success' : 'warning' }}">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-{{ $ambiente_actual == 'test' ? 'flask' : 'exclamation-triangle' }}"></i> 
                    Ambiente Actual: {{ strtoupper($ambiente_actual) }}
                </h3>
            </div>
            <div class="panel-body">
                @if($ambiente_actual == 'test')
                    <div class="alert alert-success">
                        <h4><i class="fa fa-flask"></i> Ambiente de Testing</h4>
                        <p>Está en modo de pruebas. Las transacciones SOAP se envían al servidor de testing de Unión Personal.</p>
                        <ul>
                            <li>✅ Seguro para pruebas</li>
                            <li>✅ No genera transacciones reales</li>
                            <li>✅ Usa datos de prueba</li>
                        </ul>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h4><i class="fa fa-exclamation-triangle"></i> Ambiente de Producción</h4>
                        <p><strong>¡CUIDADO!</strong> Está en modo producción. Las transacciones SOAP son reales y se facturan.</p>
                        <ul>
                            <li>⚠️ Transacciones reales</li>
                            <li>⚠️ Se generan costos</li>
                            <li>⚠️ Afecta a afiliados reales</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cambio de Ambiente -->
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-exchange"></i> Cambiar Ambiente
                </h3>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ CRUDBooster::adminPath('up_ambiente/cambiar-ambiente') }}">
                    {{ csrf_field() }}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Seleccionar Ambiente:</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="ambiente" value="test" {{ $ambiente_actual == 'test' ? 'checked' : '' }}>
                                        <strong>Testing</strong> - Ambiente de pruebas
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="ambiente" value="produccion" {{ $ambiente_actual == 'produccion' ? 'checked' : '' }}>
                                        <strong>Producción</strong> - Ambiente real (¡CUIDADO!)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="well">
                                <h5><i class="fa fa-info-circle"></i> Información</h5>
                                <p><strong>Testing:</strong> Servidor de pruebas de UP</p>
                                <p><strong>Producción:</strong> Servidor real de UP</p>
                                <p><small>El cambio requiere limpiar cache del sistema.</small></p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('¿Está seguro de cambiar el ambiente? Esto afectará todas las transacciones SOAP.')">
                            <i class="fa fa-exchange"></i> Cambiar Ambiente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Gestión de Datos de Prueba -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-database"></i> Gestión de Datos de Prueba
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <h4><i class="fa fa-lightbulb-o"></i> Datos de Prueba</h4>
                    <p>Puede crear datos de consumos de prueba para testing completo del flujo ELG → AP → Entrega.</p>
                    <p><strong>Incluye:</strong></p>
                    <ul>
                        <li>Consumos con afiliados de testing (54715500, 54715300)</li>
                        <li>Prestaciones válidas (1420107, 1420101)</li>
                        <li>Estados variados (pendiente, elegibilidad_ok, aprobado, entregado, anulado)</li>
                        <li>Casos especiales para testing completo</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-6 text-center">
                        <a href="{{ CRUDBooster::adminPath('up_ambiente/crear-datos-prueba') }}" 
                           class="btn btn-success btn-lg"
                           onclick="return confirm('¿Crear datos de prueba? Se agregarán múltiples consumos de testing.')">
                            <i class="fa fa-plus"></i> Crear Datos de Prueba
                        </a>
                        <p class="text-muted">Crea consumos de prueba para testing</p>
                    </div>
                    <div class="col-md-6 text-center">
                        <a href="{{ CRUDBooster::adminPath('up_ambiente/limpiar-datos-prueba') }}" 
                           class="btn btn-danger btn-lg"
                           onclick="return confirm('¿Eliminar TODOS los datos de prueba? Esta acción no se puede deshacer.')">
                            <i class="fa fa-trash"></i> Limpiar Datos de Prueba
                        </a>
                        <p class="text-muted">Elimina solo datos marcados como prueba</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración Actual -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-cog"></i> Configuración Actual
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal">
                            <dt>Ambiente:</dt>
                            <dd><span class="label label-{{ $ambiente_actual == 'test' ? 'success' : 'warning' }}">{{ strtoupper($ambiente_actual) }}</span></dd>
                            <dt>Endpoint:</dt>
                            <dd><code>{{ config('union_personal.' . $ambiente_actual . '.endpoint') }}</code></dd>
                            <dt>WSDL:</dt>
                            <dd><code>{{ config('union_personal.' . $ambiente_actual . '.wsdl') }}</code></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="dl-horizontal">
                            <dt>Prestador ID:</dt>
                            <dd><code>{{ config('union_personal.prestador_id') }}</code></dd>
                            <dt>Usuario SOAP:</dt>
                            <dd><code>{{ config('union_personal.user_id') }}</code></dd>
                            <dt>App Name:</dt>
                            <dd><code>{{ config('union_personal.app_name') }}</code></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <div class="text-center" style="margin-top: 30px;">
            <a href="{{ CRUDBooster::adminPath('up_consumos') }}" class="btn btn-primary btn-lg">
                <i class="fa fa-arrow-left"></i> Volver a Consumos UP
            </a>
            <a href="{{ CRUDBooster::adminPath('manual-flujo-up') }}" class="btn btn-info">
                <i class="fa fa-book"></i> Manual de Usuario
            </a>
        </div>
    </div>
</div>
@endsection
