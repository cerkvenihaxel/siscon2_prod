@extends('crudbooster::admin_template')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-eye"></i> Detalles del Préstamo de Oxigenoterapia
                </h3>
            </div>
            <div class="panel-body">
                @if($pedido->prestamoActivo)
                    @php $prestamo = $pedido->prestamoActivo; @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Datos del Paciente</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Nro Solicitud:</strong></td>
                                    <td>{{ $pedido->nro_solicitud }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $pedido->nombre_apellido }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nro Afiliado:</strong></td>
                                    <td>{{ $pedido->nro_afiliado }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Documento:</strong></td>
                                    <td>{{ $pedido->documento }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Edad:</strong></td>
                                    <td>{{ $pedido->edad }} años</td>
                                </tr>
                                <tr>
                                    <td><strong>Médico:</strong></td>
                                    <td>{{ $pedido->medico->nombremedico ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Clínica:</strong></td>
                                    <td>{{ $pedido->clinica->nombre ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Datos del Préstamo</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Nro Préstamo:</strong></td>
                                    <td>{{ $prestamo->nro_prestamo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($prestamo->estado_prestamo == 'ACTIVO')
                                            <span class="label label-success">ACTIVO</span>
                                        @elseif($prestamo->estado_prestamo == 'RENOVADO')
                                            <span class="label label-warning">RENOVADO</span>
                                        @elseif($prestamo->estado_prestamo == 'FINALIZADO')
                                            <span class="label label-danger">FINALIZADO</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Inicio:</strong></td>
                                    <td>{{ $prestamo->fecha_inicio_prestamo->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Fin:</strong></td>
                                    <td>
                                        {{ $prestamo->fecha_fin_prestamo->format('d/m/Y') }}
                                        @if($prestamo->fecha_fin_prestamo < now())
                                            <span class="label label-danger">VENCIDO</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Equipo:</strong></td>
                                    <td>{{ $prestamo->equipo_entregado }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nro Serie:</strong></td>
                                    <td>{{ $prestamo->nro_serie_equipo }}</td>
                                </tr>
                                @if($prestamo->fecha_entrega)
                                <tr>
                                    <td><strong>Fecha Entrega:</strong></td>
                                    <td>{{ $prestamo->fecha_entrega->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($prestamo->fecha_devolucion)
                                <tr>
                                    <td><strong>Fecha Devolución:</strong></td>
                                    <td>{{ $prestamo->fecha_devolucion->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Dirección de Entrega</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>{{ $prestamo->tipo_direccion }}</td>
                                    <td><strong>Contacto:</strong></td>
                                    <td>{{ $prestamo->nombre_contacto }} - {{ $prestamo->telefono_contacto }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dirección:</strong></td>
                                    <td colspan="3">{{ $prestamo->direccion_entrega }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Localidad:</strong></td>
                                    <td>{{ $prestamo->localidad_entrega }}</td>
                                    <td><strong>Provincia:</strong></td>
                                    <td>{{ $prestamo->provincia_entrega }}</td>
                                </tr>
                                @if($prestamo->codigo_postal)
                                <tr>
                                    <td><strong>Código Postal:</strong></td>
                                    <td colspan="3">{{ $prestamo->codigo_postal }}</td>
                                </tr>
                                @endif
                                @if($prestamo->observaciones_entrega)
                                <tr>
                                    <td><strong>Observaciones:</strong></td>
                                    <td colspan="3">{{ $prestamo->observaciones_entrega }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Documentos del Préstamo</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Nombre</th>
                                            <th>Fecha Generación</th>
                                            <th>Estado</th>
                                            <th>Fecha Firma</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prestamo->documentos as $documento)
                                        <tr>
                                            <td>
                                                @switch($documento->tipo_documento)
                                                    @case('TERMINOS_CONDICIONES')
                                                        <span class="label label-info">Términos y Condiciones</span>
                                                        @break
                                                    @case('CONTRATO')
                                                        <span class="label label-primary">Contrato</span>
                                                        @break
                                                    @case('RENOVACION')
                                                        <span class="label label-warning">Renovación</span>
                                                        @break
                                                    @case('FINALIZACION')
                                                        <span class="label label-danger">Finalización</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $documento->nombre_documento }}</td>
                                            <td>{{ $documento->fecha_generacion->format('d/m/Y') }}</td>
                                            <td>
                                                @if($documento->estado_documento == 'GENERADO')
                                                    <span class="label label-warning">Pendiente Firma</span>
                                                @elseif($documento->estado_documento == 'FIRMADO')
                                                    <span class="label label-success">Firmado</span>
                                                @elseif($documento->estado_documento == 'VENCIDO')
                                                    <span class="label label-danger">Vencido</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $documento->fecha_firma ? $documento->fecha_firma->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ url('/admin/oxigenoterapia/ver-documento/' . $documento->id) }}" 
                                                   class="btn btn-xs btn-info" target="_blank">
                                                    <i class="fa fa-eye"></i> Ver
                                                </a>
                                                @if($documento->estado_documento == 'GENERADO')
                                                <a href="{{ url('/admin/oxigenoterapia/firmar-documento/' . $documento->id) }}" 
                                                   class="btn btn-xs btn-success">
                                                    <i class="fa fa-pencil"></i> Firmar
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Acciones Disponibles</h4>
                            <div class="btn-group">
                                @if($prestamo->estado_prestamo == 'ACTIVO')
                                    @if(!$prestamo->fecha_entrega)
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEntrega">
                                        <i class="fa fa-check"></i> Registrar Entrega
                                    </button>
                                    @endif
                                    
                                    <a href="{{ url('/admin/oxigenoterapia/renovar/' . $pedido->id) }}" 
                                       class="btn btn-warning">
                                        <i class="fa fa-refresh"></i> Renovar Préstamo
                                    </a>
                                    
                                    <a href="{{ url('/admin/oxigenoterapia/finalizar/' . $pedido->id) }}" 
                                       class="btn btn-danger">
                                        <i class="fa fa-stop"></i> Finalizar Préstamo
                                    </a>
                                @endif
                                
                                <a href="{{ url('/admin/oxigenoterapia/imprimir/' . $pedido->id) }}" 
                                   class="btn btn-info" target="_blank">
                                    <i class="fa fa-print"></i> Imprimir Documentos
                                </a>
                                
                                <a href="{{ url('/admin/oxigenoterapia') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h4><i class="fa fa-exclamation-triangle"></i> No hay préstamo activo</h4>
                        <p>Este pedido no tiene un préstamo activo asociado.</p>
                        <a href="{{ url('/admin/oxigenoterapia/prestamo/' . $pedido->id) }}" class="btn btn-primary">
                            <i class="fa fa-handshake-o"></i> Crear Préstamo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar entrega -->
@if($pedido->prestamoActivo && !$pedido->prestamoActivo->fecha_entrega)
<div class="modal fade" id="modalEntrega" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Registrar Entrega del Equipo</h4>
            </div>
            <form action="{{ route('prestamo.entregar', $pedido->prestamoActivo->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fecha_entrega">Fecha de Entrega *</label>
                        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="observaciones_entrega">Observaciones</label>
                        <textarea class="form-control" id="observaciones_entrega" name="observaciones_entrega" 
                                  rows="3" placeholder="Detalles de la entrega, estado del equipo, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check"></i> Registrar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('bottom')
<script>
$(document).ready(function() {
    // Contador de días restantes
    @if($pedido->prestamoActivo)
        var fechaFin = new Date('{{ $pedido->prestamoActivo->fecha_fin_prestamo->format("Y-m-d") }}');
        var hoy = new Date();
        var diasRestantes = Math.ceil((fechaFin - hoy) / (1000 * 60 * 60 * 24));
        
        if (diasRestantes < 0) {
            $('.panel-heading').append('<span class="label label-danger pull-right">VENCIDO HACE ' + Math.abs(diasRestantes) + ' DÍAS</span>');
        } else if (diasRestantes <= 7) {
            $('.panel-heading').append('<span class="label label-warning pull-right">VENCE EN ' + diasRestantes + ' DÍAS</span>');
        }
    @endif
});
</script>
@endpush 