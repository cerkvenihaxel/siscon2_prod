@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-refresh"></i> Renovar Préstamo - {{ $prestamo->nro_prestamo }}
        </h3>
    </div>
    <div class="panel-body">
        <!-- Información del Préstamo -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">Información del Préstamo</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nro. Préstamo:</strong><br>
                                {{ $prestamo->nro_prestamo }}
                            </div>
                            <div class="col-md-3">
                                <strong>Paciente:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->nombre_apellido ?? 'No especificado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Fecha Inicio:</strong><br>
                                {{ date('d/m/Y', strtotime($prestamo->fecha_inicio_prestamo)) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Fecha Fin Actual:</strong><br>
                                {{ date('d/m/Y', strtotime($prestamo->fecha_fin_prestamo)) }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <strong>Estado:</strong><br>
                                <span class="label label-success">{{ $prestamo->estado_prestamo }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Duración Original:</strong><br>
                                {{ \Carbon\Carbon::parse($prestamo->fecha_inicio_prestamo)->diffInDays($prestamo->fecha_fin_prestamo) }} días
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Renovación -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Datos de Renovación</h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ url('/admin/prestamo-oxigenoterapia/renovar/' . $prestamo->id) }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="duracion_renovacion">Duración de Renovación (días)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="duracion_renovacion" 
                                               name="duracion_renovacion" 
                                               min="1" 
                                               max="365" 
                                               value="30" 
                                               required>
                                        <small class="form-text text-muted">
                                            Máximo 365 días. La nueva fecha de fin será: 
                                            <span id="nueva_fecha_fin" class="text-info"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_fin_actual">Fecha de Fin Actual</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="fecha_fin_actual" 
                                               value="{{ date('d/m/Y', strtotime($prestamo->fecha_fin_prestamo)) }}" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones_renovacion">Observaciones de Renovación</label>
                                        <textarea class="form-control" 
                                                  id="observaciones_renovacion" 
                                                  name="observaciones_renovacion" 
                                                  rows="4" 
                                                  placeholder="Motivo de la renovación, condiciones especiales, etc."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <strong>⚠️ Importante:</strong>
                                        <ul style="margin-bottom: 0;">
                                            <li>La renovación extenderá la fecha de fin del préstamo</li>
                                            <li>El estado cambiará a "RENOVADO"</li>
                                            <li>Se registrará la observación en el historial</li>
                                            <li>El pedido asociado también cambiará su estado</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a href="{{ url('/admin/prestamo_oxigenoterapia') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-refresh"></i> Renovar Préstamo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const duracionInput = document.getElementById('duracion_renovacion');
    const nuevaFechaSpan = document.getElementById('nueva_fecha_fin');
    const fechaFinActual = '{{ $prestamo->fecha_fin_prestamo }}';
    
    function calcularNuevaFecha() {
        const duracion = parseInt(duracionInput.value) || 0;
        if (duracion > 0) {
            const fechaActual = new Date(fechaFinActual);
            const nuevaFecha = new Date(fechaActual);
            nuevaFecha.setDate(fechaActual.getDate() + duracion);
            
            nuevaFechaSpan.textContent = nuevaFecha.toLocaleDateString('es-ES');
        } else {
            nuevaFechaSpan.textContent = 'Seleccione duración';
        }
    }
    
    duracionInput.addEventListener('input', calcularNuevaFecha);
    calcularNuevaFecha(); // Calcular inicial
});
</script>

@endsection 