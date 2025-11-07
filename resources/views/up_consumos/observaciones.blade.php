@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-comment"></i> Gestionar Observaciones
                </h3>
            </div>
            <div class="panel-body">
                <!-- Información del Consumo -->
                <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> Información del Medicamento</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Descripción:</strong> {{ $consumo->desc }}<br>
                            <strong>Código Afiliado:</strong> {{ $consumo->afiliado }}<br>
                            <strong>Cantidad:</strong> {{ $consumo->cant }}<br>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado Actual:</strong> 
                            @php
                                $badges = [
                                    'pendiente' => 'default',
                                    'elegibilidad_ok' => 'info',
                                    'elegibilidad_no' => 'danger',
                                    'aprobado' => 'success',
                                    'rechazado' => 'danger',
                                    'entregado' => 'primary',
                                    'anulado' => 'warning',
                                ];
                                $color = $badges[$consumo->estado_flujo] ?? 'default';
                            @endphp
                            <span class="badge badge-{{ $color }}">{{ strtoupper(str_replace('_', ' ', $consumo->estado_flujo)) }}</span><br>
                            <strong>Fecha Creación:</strong> {{ $consumo->created_at->format('d/m/Y H:i') }}<br>
                            @if($consumo->fecha_entrega)
                                <strong>Fecha Entrega:</strong> {{ $consumo->fecha_entrega->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Observaciones Actuales -->
                @if($consumo->observaciones_flujo)
                <div class="alert alert-warning">
                    <h4><i class="fa fa-comment-o"></i> Observaciones Actuales</h4>
                    <p>{{ $consumo->observaciones_flujo }}</p>
                    @if($consumo->usuario_observaciones)
                        <small class="text-muted">
                            <i class="fa fa-user"></i> {{ $consumo->usuario_observaciones }} - 
                            {{ $consumo->fecha_observaciones ? $consumo->fecha_observaciones->format('d/m/Y H:i') : 'N/A' }}
                        </small>
                    @endif
                </div>
                @endif

                <!-- Formulario de Observaciones -->
                <form id="formObservaciones">
                    <input type="hidden" name="consumo_id" value="{{ $consumo->id }}">
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="6" 
                                  placeholder="Ingrese observaciones sobre este medicamento...">{{ $consumo->observaciones_flujo }}</textarea>
                        <small class="text-muted">
                            Puede incluir información sobre: estado del medicamento, condiciones especiales, 
                            notas para la farmacia, instrucciones de entrega, etc.
                        </small>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> Guardar Observaciones
                        </button>
                        <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default btn-lg">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formObservaciones').on('submit', function(e) {
        e.preventDefault();
        
        var btnSubmit = $(this).find('button[type=submit]');
        var originalText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: '{{ CRUDBooster::mainpath("observaciones") }}',
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "¡Éxito!",
                        text: response.message,
                        type: "success",
                        confirmButtonText: "OK"
                    }, function() {
                        window.location.href = response.redirect;
                    });
                } else {
                    swal("Error", response.message, "error");
                    btnSubmit.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error al guardar observaciones';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                swal("Error", errorMsg, "error");
                btnSubmit.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
