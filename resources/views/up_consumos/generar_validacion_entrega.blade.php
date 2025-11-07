@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clipboard-check"></i> Generar Validación de Entrega
                </h3>
            </div>
            <div class="panel-body">
                
                <!-- Información del Consumo -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4><i class="fa fa-pills"></i> Información del Medicamento</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong>Código:</strong></td>
                                        <td>{{ $consumo->cod_prestacion }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descripción:</strong></td>
                                        <td>{{ $consumo->desc }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo:</strong></td>
                                        <td>{{ $consumo->tipo_prestacion_descripcion }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cantidad Autorizada:</strong></td>
                                        <td><span class="badge badge-success">{{ $consumo->cant }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Importe Autorizado:</strong></td>
                                        <td><strong>${{ number_format($consumo->imptot, 2, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>ID Autorización:</strong></td>
                                        <td><code>{{ $consumo->idaut }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4><i class="fa fa-user"></i> Información del Afiliado</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong>Código:</strong></td>
                                        <td>{{ $consumo->afiliado }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $consumo->nombre_completo }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plan:</strong></td>
                                        <td>{{ $consumo->nombre_modelo_plan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Localidad:</strong></td>
                                        <td>{{ $consumo->localidad }}, {{ $consumo->provincia }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Edad:</strong></td>
                                        <td>{{ $consumo->edad }} años</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Validación -->
                <form id="form-validacion-entrega" method="POST">
                    <input type="hidden" name="consumo_id" value="{{ $consumo->id }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="fa fa-store"></i> Datos de la Farmacia</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="farmacia_codigo">Código de Farmacia <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="farmacia_codigo" name="farmacia_codigo" 
                                               value="{{ old('farmacia_codigo', $consumo->farmacia_codigo) }}" required>
                                        <small class="text-muted">Código identificador de la farmacia</small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="farmacia_nombre">Nombre de la Farmacia <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="farmacia_nombre" name="farmacia_nombre" 
                                               value="{{ old('farmacia_nombre', $consumo->farmacia_nombre) }}" required>
                                        <small class="text-muted">Razón social o nombre comercial</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="farmacia_direccion">Dirección de la Farmacia</label>
                                        <input type="text" class="form-control" id="farmacia_direccion" name="farmacia_direccion" 
                                               value="{{ old('farmacia_direccion') }}" placeholder="Calle, número, localidad">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fa fa-prescription-bottle-alt"></i> Detalles de la Entrega</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_receta">Número de Receta</label>
                                        <input type="text" class="form-control" id="numero_receta" name="numero_receta" 
                                               value="{{ old('numero_receta', $consumo->numero_receta) }}" 
                                               placeholder="Ej: R-123456">
                                        <small class="text-muted">Número de la receta médica (si aplica)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medico_prescriptor">Médico Prescriptor</label>
                                        <input type="text" class="form-control" id="medico_prescriptor" name="medico_prescriptor" 
                                               value="{{ old('medico_prescriptor') }}" 
                                               placeholder="Dr. Juan Pérez">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lote_medicamento">Lote del Medicamento</label>
                                        <input type="text" class="form-control" id="lote_medicamento" name="lote_medicamento" 
                                               value="{{ old('lote_medicamento') }}" 
                                               placeholder="Ej: L2024001">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                               value="{{ old('fecha_vencimiento') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="laboratorio">Laboratorio</label>
                                        <input type="text" class="form-control" id="laboratorio" name="laboratorio" 
                                               value="{{ old('laboratorio') }}" 
                                               placeholder="Ej: Laboratorio XYZ">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="entrega_completa" name="entrega_completa" value="1" checked>
                                            Entrega Completa
                                        </label>
                                        <small class="text-muted d-block">Marque si se entrega la cantidad total autorizada</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="cantidad-entregada-group" style="display: none;">
                                        <label for="cantidad_entregada">Cantidad Entregada</label>
                                        <input type="number" class="form-control" id="cantidad_entregada" name="cantidad_entregada" 
                                               value="{{ $consumo->cant }}" min="1" max="{{ $consumo->cant }}">
                                        <small class="text-muted">Máximo: {{ $consumo->cant }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Observaciones adicionales sobre la entrega...">{{ old('observaciones') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h4><i class="fa fa-exclamation-triangle"></i> Confirmación de Entrega</h4>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-warning">
                                <i class="fa fa-info-circle"></i>
                                <strong>Importante:</strong> Al confirmar esta validación, se registrará que el medicamento fue 
                                entregado al afiliado. Esta acción no se puede deshacer. Verifique que todos los datos sean correctos.
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="confirmar_entrega" required>
                                    Confirmo que el medicamento fue entregado físicamente al afiliado o persona autorizada
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-default" onclick="window.history.back()">
                            <i class="fa fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg" id="btn-generar-validacion">
                            <i class="fa fa-clipboard-check"></i> Generar Validación de Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Manejar checkbox de entrega completa
    $('#entrega_completa').change(function() {
        if ($(this).is(':checked')) {
            $('#cantidad-entregada-group').hide();
            $('#cantidad_entregada').val({{ $consumo->cant }});
        } else {
            $('#cantidad-entregada-group').show();
        }
    });

    // Manejar envío del formulario
    $('#form-validacion-entrega').submit(function(e) {
        e.preventDefault();
        
        if (!$('#confirmar_entrega').is(':checked')) {
            swal('Error', 'Debe confirmar que el medicamento fue entregado', 'error');
            return;
        }

        var btn = $('#btn-generar-validacion');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generando...');

        $.ajax({
            url: '{{ CRUDBooster::mainpath("generar-validacion-entrega") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    swal({
                        title: 'Validación Generada',
                        text: response.message,
                        type: 'success',
                        confirmButtonText: 'Ver Comprobante'
                    }, function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    swal('Error', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fa fa-clipboard-check"></i> Generar Validación de Entrega');
                }
            },
            error: function(xhr) {
                var message = 'Error al generar validación de entrega';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                swal('Error', message, 'error');
                btn.prop('disabled', false).html('<i class="fa fa-clipboard-check"></i> Generar Validación de Entrega');
            }
        });
    });

    // Auto-completar datos de farmacia si ya existen
    $('#farmacia_codigo').blur(function() {
        var codigo = $(this).val();
        if (codigo) {
            // Aquí se podría implementar una búsqueda AJAX de datos de farmacia
            // por ahora solo es un placeholder
        }
    });
});
</script>

<style>
.panel-heading h4 {
    margin: 0;
    font-size: 16px;
}

.table-condensed td {
    padding: 5px 8px;
}

.badge {
    font-size: 12px;
}

code {
    background-color: #f5f5f5;
    padding: 2px 4px;
    border-radius: 3px;
}

.text-danger {
    color: #d9534f;
}

.alert-warning {
    border-left: 4px solid #f0ad4e;
}
</style>
@endsection
