@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-check"></i> Finalizar Préstamo - {{ $prestamo->nro_prestamo }}
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
                                <strong>Fecha Fin:</strong><br>
                                {{ date('d/m/Y', strtotime($prestamo->fecha_fin_prestamo)) }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <strong>Estado:</strong><br>
                                <span class="label label-{{ $prestamo->estado_prestamo == 'ACTIVO' ? 'success' : 'warning' }}">
                                    {{ $prestamo->estado_prestamo }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>Duración Total:</strong><br>
                                {{ \Carbon\Carbon::parse($prestamo->fecha_inicio_prestamo)->diffInDays($prestamo->fecha_fin_prestamo) }} días
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Finalización -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Datos de Finalización</h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ url('/admin/prestamo-oxigenoterapia/finalizar/' . $prestamo->id) }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_devolucion">Fecha de Devolución</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="fecha_devolucion" 
                                               name="fecha_devolucion" 
                                               value="{{ date('Y-m-d') }}" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado_actual">Estado Actual</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="estado_actual" 
                                               value="{{ $prestamo->estado_prestamo }}" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones_finalizacion">Observaciones de Finalización</label>
                                        <textarea class="form-control" 
                                                  id="observaciones_finalizacion" 
                                                  name="observaciones_finalizacion" 
                                                  rows="4" 
                                                  placeholder="Condición de los equipos, motivo de finalización, etc."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Verificación de Equipos Devueltos -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">
                                                <i class="fa fa-check-square-o"></i> Verificación de Equipos Devueltos
                                            </h5>
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $materiales = \App\Models\PedidoOxigenoterapiaMaterial::with('equipo')
                                                    ->where('pedido_oxigenoterapia_id', $prestamo->pedido_oxigenoterapia_id)
                                                    ->get();
                                            @endphp
                                            
                                            @if($materiales->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Equipo</th>
                                                                <th>Descripción</th>
                                                                <th>Cantidad</th>
                                                                <th>¿Devuelto?</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($materiales as $material)
                                                            <tr>
                                                                <td>{{ $material->equipo->nombre_equipo ?? 'No especificado' }}</td>
                                                                <td>{{ $material->equipo->descripcion ?? 'Sin descripción' }}</td>
                                                                <td>{{ $material->cantidad }}</td>
                                                                <td>
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <input type="checkbox" 
                                                                                   name="equipos_devueltos[{{ $material->id }}]" 
                                                                                   value="1" 
                                                                                   required>
                                                                            Confirmar devolución
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    No se encontraron equipos asociados a este préstamo.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <strong>⚠️ Importante:</strong>
                                        <ul style="margin-bottom: 0;">
                                            <li>La finalización cambiará el estado a "FINALIZADO"</li>
                                            <li>Se registrará la fecha de devolución</li>
                                            <li>Se actualizará el estado del pedido asociado</li>
                                            <li>Se marcarán los equipos como devueltos</li>
                                            <li>Esta acción no se puede deshacer</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a href="{{ url('/admin/prestamo_oxigenoterapia') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('¿Está seguro de que desea finalizar este préstamo? Esta acción no se puede deshacer.')">
                                        <i class="fa fa-check"></i> Finalizar Préstamo
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
    // Validar que al menos un equipo esté marcado como devuelto
    const form = document.querySelector('form');
    const checkboxes = document.querySelectorAll('input[name^="equipos_devueltos"]');
    
    form.addEventListener('submit', function(e) {
        let alMenosUnoMarcado = false;
        
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                alMenosUnoMarcado = true;
            }
        });
        
        if (!alMenosUnoMarcado) {
            e.preventDefault();
            alert('Debe confirmar la devolución de al menos un equipo.');
            return false;
        }
    });
});
</script>

@endsection 