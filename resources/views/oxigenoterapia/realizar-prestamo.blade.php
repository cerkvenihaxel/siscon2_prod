@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-handshake-o"></i> Realizar Préstamo - Pedido: {{ $pedido->nro_solicitud }}
        </h3>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('oxigenoterapia.prestamo.store') }}" id="form-prestamo">
            @csrf
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
            
            <!-- Información del Afiliado -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">Información del Afiliado</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nombre:</strong><br>
                                    {{ $pedido->nombre_apellido }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Nro. Afiliado:</strong><br>
                                    {{ $pedido->nro_afiliado }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Documento:</strong><br>
                                    {{ $pedido->documento }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Edad:</strong><br>
                                    {{ $pedido->edad }} años
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6">
                                    <strong>Teléfono:</strong><br>
                                    {{ $pedido->tel_afiliado ?: 'No especificado' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong><br>
                                    {{ $pedido->email ?: 'No especificado' }}
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6">
                                    <strong>Médico:</strong><br>
                                    {{ $pedido->medicos->nombremedico ?? 'No especificado' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Clínica:</strong><br>
                                    {{ $pedido->clinica->nombre ?? 'No especificada' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Préstamo -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">Información del Préstamo</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Número de Préstamo:</label>
                                        <input type="text" class="form-control" name="nro_prestamo" value="{{ $nro_prestamo }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Duración (días):</label>
                                        <input type="number" class="form-control" name="duracion_dias" value="30" min="1" max="365" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Dirección de Entrega:</label>
                                        <textarea class="form-control" name="direccion_entrega" rows="3" required placeholder="Ingrese la dirección completa de entrega"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Observaciones del Préstamo:</label>
                                        <textarea class="form-control" name="observaciones_prestamo" rows="3" placeholder="Observaciones adicionales del préstamo"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checklist de Materiales -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <i class="fa fa-check-square-o"></i> Checklist de Materiales
                            </h4>
                        </div>
                        <div class="panel-body">
                            @if($materiales->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Código</th>
                                                <th width="30%">Equipo</th>
                                                <th width="10%">Cantidad</th>
                                                <th width="15%">¿Se puede entregar?</th>
                                                <th width="25%">Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($materiales as $index => $material)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->codigo_equipo }}</td>
                                                <td>{{ $material->nombre_equipo }}</td>
                                                <td>{{ $material->cantidad }}</td>
                                                <td>
                                                    <select name="materiales[{{ $material->id }}][entregable]" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="1">Sí, se puede entregar</option>
                                                        <option value="0">No, no se puede entregar</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="materiales[{{ $material->id }}][observaciones]" class="form-control" rows="2" placeholder="Razón si no se puede entregar o observaciones"></textarea>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> No se encontraron materiales para este pedido.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="{{ url('/admin/pedido_oxigenoterapia') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-success" id="btn-crear-prestamo">
                        <i class="fa fa-save"></i> Crear Préstamo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#form-prestamo').on('submit', function(e) {
        var materialesEntregables = $('select[name*="[entregable]"]').filter(function() {
            return $(this).val() === '0';
        });
        
        var materialesSinObservacion = materialesEntregables.filter(function() {
            var materialId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            var observacion = $('textarea[name="materiales[' + materialId + '][observaciones]"]').val();
            return !observacion.trim();
        });
        
        if (materialesSinObservacion.length > 0) {
            e.preventDefault();
            alert('Por favor, ingrese una razón u observación para los materiales que no se pueden entregar.');
            return false;
        }
        
        // Confirmar creación del préstamo
        if (!confirm('¿Está seguro de que desea crear el préstamo?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Mostrar/ocultar campo de observaciones según si se puede entregar
    $('select[name*="[entregable]"]').on('change', function() {
        var materialId = $(this).attr('name').match(/\[(\d+)\]/)[1];
        var observacionField = $('textarea[name="materiales[' + materialId + '][observaciones]"]');
        
        if ($(this).val() === '0') {
            observacionField.attr('required', 'required');
            observacionField.css('border-color', '#a94442');
        } else {
            observacionField.removeAttr('required');
            observacionField.css('border-color', '');
        }
    });
});
</script>

@endsection 