@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-dashboard"></i> Dashboard - Farmacia UP
                </h3>
            </div>
            <div class="panel-body">
                <p class="lead">Panel de control para gestión de medicamentos Unión Personal</p>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas del Día -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $stats_hoy['consumos_pendientes'] }}</h3>
                <p>Consumos Pendientes Hoy</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
            <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=pendiente') }}" class="small-box-footer">
                Ver Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>{{ $stats_hoy['elegibilidades_ok'] }}</h3>
                <p>Elegibilidades Confirmadas</p>
            </div>
            <div class="icon">
                <i class="fa fa-user-check"></i>
            </div>
            <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=elegibilidad_ok') }}" class="small-box-footer">
                Ver Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $stats_hoy['aprobaciones'] }}</h3>
                <p>Prestaciones Aprobadas</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
            <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=aprobado') }}" class="small-box-footer">
                Ver Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $stats_hoy['entregas'] }}</h3>
                <p>Entregas Validadas</p>
            </div>
            <div class="icon">
                <i class="fa fa-clipboard-check"></i>
            </div>
            <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=entregado') }}" class="small-box-footer">
                Ver Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Búsqueda Rápida -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-search"></i> Búsqueda Rápida de Afiliado
                </h3>
            </div>
            <div class="panel-body">
                <form id="form-buscar-afiliado" class="form-inline">
                    <div class="form-group">
                        <label for="codigo_afiliado">Código de Afiliado:</label>
                        <input type="text" class="form-control" id="codigo_afiliado" name="codigo_afiliado" 
                               placeholder="Ej: 54715500" style="width: 200px;">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Buscar Consumos
                    </button>
                    <a href="{{ CRUDBooster::mainpath('buscar-por-afiliado') }}" class="btn btn-default">
                        <i class="fa fa-list"></i> Búsqueda Avanzada
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Consumos Pendientes y Listos para Entrega -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Consumos Pendientes de Procesar
                </h3>
            </div>
            <div class="panel-body">
                @if($consumos_pendientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Afiliado</th>
                                    <th>Medicamento</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consumos_pendientes as $consumo)
                                <tr>
                                    <td>
                                        <strong>{{ $consumo->afiliado }}</strong><br>
                                        <small>{{ $consumo->nombre_completo }}</small>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($consumo->desc, 30) }}</small><br>
                                        <span class="badge badge-info">{{ $consumo->cant }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $consumo->fecha_tran->format('d/m H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ CRUDBooster::mainpath('../up_consumos/consultar-elegibilidad/' . $consumo->id) }}" 
                                           class="btn btn-xs btn-info">
                                            <i class="fa fa-user-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=pendiente') }}" 
                           class="btn btn-warning">
                            <i class="fa fa-list"></i> Ver Todos los Pendientes
                        </a>
                    </div>
                @else
                    <div class="alert alert-success text-center">
                        <i class="fa fa-check-circle"></i>
                        No hay consumos pendientes de procesar
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-truck"></i> Listos para Entrega
                </h3>
            </div>
            <div class="panel-body">
                @if($listos_entrega->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Afiliado</th>
                                    <th>Medicamento</th>
                                    <th>IDAUT</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listos_entrega as $consumo)
                                <tr>
                                    <td>
                                        <strong>{{ $consumo->afiliado }}</strong><br>
                                        <small>{{ $consumo->nombre_completo }}</small>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($consumo->desc, 30) }}</small><br>
                                        <span class="badge badge-success">{{ $consumo->cant }}</span>
                                    </td>
                                    <td>
                                        <code>{{ $consumo->idaut }}</code>
                                    </td>
                                    <td>
                                        <a href="{{ CRUDBooster::mainpath('../up_consumos/generar-validacion-entrega/' . $consumo->id) }}" 
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-clipboard-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ CRUDBooster::mainpath('../up_consumos?estado_flujo=aprobado') }}" 
                           class="btn btn-success">
                            <i class="fa fa-list"></i> Ver Todos los Aprobados
                        </a>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle"></i>
                        No hay consumos listos para entrega
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas del Mes -->
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-bar-chart"></i> Estadísticas del Mes
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chart-estados" width="300" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-condensed">
                            <tr>
                                <td><i class="fa fa-circle text-yellow"></i> Pendientes</td>
                                <td class="text-right">{{ $stats_mes['pendientes'] }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-circle text-blue"></i> Elegibilidad OK</td>
                                <td class="text-right">{{ $stats_mes['elegibilidad_ok'] }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-circle text-green"></i> Aprobados</td>
                                <td class="text-right">{{ $stats_mes['aprobados'] }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-circle text-purple"></i> Entregados</td>
                                <td class="text-right">{{ $stats_mes['entregados'] }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-circle text-red"></i> Rechazados</td>
                                <td class="text-right">{{ $stats_mes['rechazados'] }}</td>
                            </tr>
                            <tr class="info">
                                <td><strong>Total Consumos</strong></td>
                                <td class="text-right"><strong>{{ $stats_mes['total_consumos'] }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-dollar"></i> Importes del Mes
                </h3>
            </div>
            <div class="panel-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Importe Total</span>
                        <span class="info-box-number">${{ number_format($stats_mes['importe_total'], 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Importe Entregado</span>
                        <span class="info-box-number">${{ number_format($stats_mes['importe_entregado'], 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($stats_entregas)
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-clipboard-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Validaciones</span>
                        <span class="info-box-number">{{ $stats_entregas['total_entregas'] }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-rocket"></i> Accesos Rápidos
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ CRUDBooster::mainpath('../up_consumos') }}" class="btn btn-block btn-primary">
                            <i class="fa fa-list"></i><br>
                            Ver Todos los Consumos
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ CRUDBooster::mainpath('buscar-por-afiliado') }}" class="btn btn-block btn-info">
                            <i class="fa fa-search"></i><br>
                            Buscar por Afiliado
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ CRUDBooster::mainpath('../up_elegibilidad') }}" class="btn btn-block btn-warning">
                            <i class="fa fa-user-check"></i><br>
                            Historial Elegibilidad
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ CRUDBooster::mainpath('exportar-validaciones') }}" class="btn btn-block btn-success">
                            <i class="fa fa-download"></i><br>
                            Exportar Validaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Manejar búsqueda rápida
    $('#form-buscar-afiliado').submit(function(e) {
        e.preventDefault();
        var codigo = $('#codigo_afiliado').val().trim();
        if (codigo) {
            window.location.href = '{{ CRUDBooster::mainpath("buscar-por-afiliado") }}/' + codigo;
        } else {
            swal('Error', 'Ingrese un código de afiliado', 'error');
        }
    });

    // Gráfico de estados
    var ctx = document.getElementById('chart-estados').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'Elegibilidad OK', 'Aprobados', 'Entregados', 'Rechazados'],
            datasets: [{
                data: [
                    {{ $stats_mes['pendientes'] }},
                    {{ $stats_mes['elegibilidad_ok'] }},
                    {{ $stats_mes['aprobados'] }},
                    {{ $stats_mes['entregados'] }},
                    {{ $stats_mes['rechazados'] }}
                ],
                backgroundColor: [
                    '#f39c12',
                    '#3c8dbc',
                    '#00a65a',
                    '#605ca8',
                    '#dd4b39'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            }
        }
    });

    // Auto-refresh cada 5 minutos
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>

<style>
.small-box {
    border-radius: 5px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    text-decoration: none;
}

.small-box > .small-box-footer:hover {
    color: #fff;
    background: rgba(0,0,0,0.15);
}

.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 15px;
}

.small-box .icon {
    -webkit-transition: all .3s linear;
    -o-transition: all .3s linear;
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endsection
