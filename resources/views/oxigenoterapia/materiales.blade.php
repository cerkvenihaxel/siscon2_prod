@extends('crudbooster::admin_template')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-list"></i> Materiales del Pedido: {{ $pedido->nro_solicitud }}
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Información del Pedido</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Nro. Solicitud:</strong></td>
                                <td>{{ $pedido->nro_solicitud }}</td>
                            </tr>
                            <tr>
                                <td><strong>Afiliado:</strong></td>
                                <td>{{ $pedido->nombre_apellido }} ({{ $pedido->nro_afiliado }})</td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($pedido->estado_oxigenoterapia_id == 1)
                                        <span class="label label-warning">PENDIENTE</span>
                                    @elseif($pedido->estado_oxigenoterapia_id == 2)
                                        <span class="label label-success">AUTORIZADO</span>
                                    @elseif($pedido->estado_oxigenoterapia_id == 3)
                                        <span class="label label-primary">EN PRÉSTAMO</span>
                                    @elseif($pedido->estado_oxigenoterapia_id == 4)
                                        <span class="label label-info">RENOVADO</span>
                                    @elseif($pedido->estado_oxigenoterapia_id == 5)
                                        <span class="label label-default">FINALIZADO</span>
                                    @else
                                        <span class="label label-danger">RECHAZADO</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Prescripción:</strong></td>
                                <td>{{ $pedido->fecha_prescripcion ? $pedido->fecha_prescripcion->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Vencimiento:</strong></td>
                                <td>{{ $pedido->fecha_vencimiento ? $pedido->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Información Médica</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Médico:</strong></td>
                                <td>{{ $pedido->medico ? $pedido->medico->nombremedico : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Clínica:</strong></td>
                                <td>{{ $pedido->clinica ? $pedido->clinica->nombre : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Teléfono Médico:</strong></td>
                                <td>{{ $pedido->tel_medico ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td>{{ $pedido->observaciones ?: 'Sin observaciones' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <h4>Materiales/Equipos Solicitados</h4>
                @if($pedido->materiales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Equipo</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Observaciones</th>
                                    <th>Estado Equipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->materiales as $index => $material)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $material->equipo ? $material->equipo->nombre_equipo : 'N/A' }}</td>
                                        <td>{{ $material->equipo ? $material->equipo->codigo_equipo : 'N/A' }}</td>
                                        <td>{{ $material->equipo ? $material->equipo->tipo_equipo : 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $material->cantidad }}</span>
                                        </td>
                                        <td>{{ $material->observaciones ?: 'Sin observaciones' }}</td>
                                        <td>
                                            @if($material->equipo)
                                                @if($material->equipo->estado_equipo == 'DISPONIBLE')
                                                    <span class="label label-success">DISPONIBLE</span>
                                                @elseif($material->equipo->estado_equipo == 'EN_USO')
                                                    <span class="label label-warning">EN USO</span>
                                                @elseif($material->equipo->estado_equipo == 'MANTENIMIENTO')
                                                    <span class="label label-info">MANTENIMIENTO</span>
                                                @else
                                                    <span class="label label-danger">RETIRADO</span>
                                                @endif
                                            @else
                                                <span class="label label-default">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> No se han seleccionado materiales para este pedido.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a href="{{ url('/admin/pedido_oxigenoterapia') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                            @if($pedido->estado_oxigenoterapia_id == 1)
                                <a href="{{ url('/admin/oxigenoterapia/autorizar/'.$pedido->id) }}" class="btn btn-success">
                                    <i class="fa fa-check"></i> Autorizar Pedido
                                </a>
                            @elseif($pedido->estado_oxigenoterapia_id == 2)
                                <a href="{{ url('/admin/oxigenoterapia/prestamo/'.$pedido->id) }}" class="btn btn-primary">
                                    <i class="fa fa-handshake-o"></i> Realizar Préstamo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 