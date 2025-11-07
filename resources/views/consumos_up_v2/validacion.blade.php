@extends('crudbooster::admin_template')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert@1.1.3/dist/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert@1.1.3/dist/sweetalert.css">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-clipboard-check"></i> Validación de Entrega - Consumo UP
                </h3>
            </div>
            
            <form method="POST" action="/admin/consumos_up_v2/{{ $consumo->id }}/procesar-validacion" id="formValidacion">
                @csrf
                
                <div class="box-body">
                    <!-- Datos del Consumo -->
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Datos del Consumo</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="dl-horizontal">
                                        <dt>Código Afiliado:</dt><dd>{{ $consumo->afiliado }}</dd>
                                        <dt>Nombre:</dt><dd>{{ $consumo->apellidos }}, {{ $consumo->nombres }}</dd>
                                        <dt>Código Prestación:</dt><dd>{{ $consumo->cod_prestacion }}</dd>
                                        <dt>Descripción:</dt><dd>{{ $consumo->desc }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="dl-horizontal">
                                        <dt>Cantidad:</dt><dd>{{ $consumo->cant }}</dd>
                                        <dt>Importe:</dt><dd>${{ number_format($consumo->imptot, 2) }}</dd>
                                        <dt>ID Autorización:</dt><dd>{{ $consumo->idaut }}</dd>
                                        <dt>Fecha Aprobación:</dt><dd>{{ $consumo->fecha_aprobacion ? $consumo->fecha_aprobacion->format('d/m/Y H:i') : 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Validación -->
                    <div class="panel panel-success">
                        <div class="panel-heading"><strong>Datos de Entrega</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Entrega *</label>
                                        <input type="datetime-local" name="fecha_entrega" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Estado de Entrega *</label>
                                        <select name="estado_entrega" class="form-control" required>
                                            <option value="completa">Entrega Completa</option>
                                            <option value="parcial">Entrega Parcial</option>
                                            <option value="no_entregado">No Entregado</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cantidad Entregada</label>
                                        <input type="number" name="cantidad_entregada" class="form-control" value="{{ $consumo->cant }}" min="0" max="{{ $consumo->cant }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Lugar de Entrega</label>
                                        <input type="text" name="lugar_entrega" class="form-control" placeholder="Ej: Farmacia Central, Domicilio, etc.">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones sobre la entrega..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Registrar Entrega
                    </button>
                    <a href="/admin/consumos_up_v2" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formValidacion').on('submit', function(e) {
        e.preventDefault();
        
        var form = this;
        
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: $(form).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    swal({
                        title: 'Éxito',
                        text: 'Entrega registrada exitosamente',
                        type: 'success',
                        confirmButtonText: 'Ver Consumo'
                    }, function() {
                        window.location.href = '/admin/consumos_up_v2';
                    });
                } else {
                    swal('Error', response.message, 'error');
                }
            },
            error: function() {
                swal('Error', 'Error al registrar la entrega', 'error');
            }
        });
    });
});
</script>
@endsection
