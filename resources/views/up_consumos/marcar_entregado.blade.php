@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-truck"></i> Marcar Medicamento como Entregado
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
                            <span class="badge badge-success">{{ strtoupper(str_replace('_', ' ', $consumo->estado_flujo)) }}</span><br>
                            <strong>Fecha Aprobación:</strong> {{ $consumo->fecha_aprobacion ? $consumo->fecha_aprobacion->format('d/m/Y H:i') : 'N/A' }}<br>
                            <strong>ID Autorización:</strong> {{ $consumo->idaut ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Formulario de Entrega -->
                <form id="formMarcarEntregado">
                    <input type="hidden" name="consumo_id" value="{{ $consumo->id }}">
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones de Entrega</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="4" 
                                  placeholder="Ingrese observaciones sobre la entrega del medicamento (opcional)...">{{ $consumo->observaciones_flujo }}</textarea>
                        <small class="text-muted">
                            Puede incluir información sobre: condiciones de entrega, instrucciones especiales, 
                            identificación del receptor, etc.
                        </small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Al confirmar la entrega, el medicamento será marcado como 
                        <strong>ENTREGADO</strong> y el flujo se completará. Esta acción no se puede deshacer 
                        fácilmente.
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Confirmar Entrega
                        </button>
                        <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default btn-lg">
                            <i class="fa fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formMarcarEntregado').on('submit', function(e) {
        e.preventDefault();
        
        var btnSubmit = $(this).find('button[type=submit]');
        var originalText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: '{{ CRUDBooster::mainpath("marcar-entregado") }}',
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
                var errorMsg = 'Error al marcar como entregado';
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
