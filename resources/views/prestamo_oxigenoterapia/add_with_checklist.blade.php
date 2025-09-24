@extends('crudbooster::admin_template')
@section('content')

<style>
.material-row.table-success {
    background-color: #d4edda !important;
    border-color: #c3e6cb !important;
}

.material-row.table-danger {
    background-color: #f8d7da !important;
    border-color: #f5c6cb !important;
}

.text-success {
    color: #28a745 !important;
    font-weight: bold;
}

.text-muted {
    color: #6c757d !important;
    font-style: italic;
}

.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

.entregable-select {
    font-weight: bold;
}

.observaciones-textarea {
    transition: all 0.3s ease;
}
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-handshake-o"></i> Crear Préstamo - Pedido: {{ $pedido->nro_solicitud }}
        </h3>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ CRUDBooster::mainpath('add-save') }}" id="form-prestamo">
            @csrf
            <input type="hidden" name="pedido_oxigenoterapia_id" value="{{ $pedido->id }}">
            <input type="hidden" name="stamp_user" value="{{ DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email') }}">
            
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
                                        <input type="date" class="form-control" name="fecha_inicio_prestamo" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha de Fin:</label>
                                        <input type="date" class="form-control" name="fecha_fin_prestamo" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo de Dirección:</label>
                                        <select class="form-control" name="tipo_direccion" required>
                                            <option value="">Seleccione...</option>
                                            <option value="PARTICULAR" {{ ($pedido->tipo_direccion ?? '') == 'PARTICULAR' ? 'selected' : '' }}>Particular</option>
                                            <option value="CLINICA" {{ ($pedido->tipo_direccion ?? '') == 'CLINICA' ? 'selected' : '' }}>Clínica</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Dirección de Entrega:</label>
                                        <textarea class="form-control" name="direccion_entrega" rows="3" required placeholder="Ingrese la dirección completa de entrega">{{ $pedido->direccion_entrega ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Localidad:</label>
                                        <input type="text" class="form-control" name="localidad_entrega" value="{{ $pedido->localidad_entrega ?? '' }}" required placeholder="Localidad">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Provincia:</label>
                                        <input type="text" class="form-control" name="provincia_entrega" value="{{ $pedido->provincia_entrega ?? '' }}" required placeholder="Provincia">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Código Postal:</label>
                                        <input type="text" class="form-control" name="codigo_postal" value="{{ $pedido->codigo_postal ?? '' }}" placeholder="Código Postal">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono de Contacto:</label>
                                        <input type="text" class="form-control" name="telefono_contacto" value="{{ $pedido->tel_afiliado ?? '' }}" required placeholder="Teléfono de contacto">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre de Contacto:</label>
                                        <input type="text" class="form-control" name="nombre_contacto" value="{{ $pedido->nombre_apellido ?? '' }}" required placeholder="Nombre de la persona de contacto">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observaciones de Entrega:</label>
                                        <textarea class="form-control" name="observaciones_entrega" rows="3" placeholder="Observaciones adicionales sobre la entrega"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-cube"></i> Equipos Entregados:</label>
                                        <textarea class="form-control text-muted" name="equipo_entregado" id="equipo_entregado" rows="4" readonly placeholder="Seleccione materiales como entregables">Seleccione materiales como entregables</textarea>
                                        <small class="text-info">
                                            <i class="fa fa-info-circle"></i> 
                                            Los equipos se agregarán automáticamente según su selección en la tabla de materiales.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-barcode"></i> Números de Serie:</label>
                                        <textarea class="form-control text-muted" name="nro_serie_equipo" id="nro_serie_equipo" rows="4" readonly placeholder="Seleccione materiales como entregables">Seleccione materiales como entregables</textarea>
                                        <small class="text-info">
                                            <i class="fa fa-info-circle"></i> 
                                            Los números de serie se agregarán automáticamente según su selección en la tabla de materiales.
                                        </small>
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
                                            <tr class="material-row" data-material-id="{{ $material->id }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->codigo_equipo }}</td>
                                                <td>{{ $material->nombre_equipo }}</td>
                                                <td>{{ $material->cantidad }}</td>
                                                <td>
                                                    <select name="materiales[{{ $material->id }}][entregable]" class="form-control entregable-select" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="1">✅ Sí, se puede entregar</option>
                                                        <option value="0">❌ No, no se puede entregar</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="materiales[{{ $material->id }}][observaciones]" class="form-control observaciones-textarea" rows="2" placeholder="Razón si no se puede entregar o observaciones"></textarea>
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
                    <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">
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
    console.log('🚀 Script iniciado...');
    
    // Datos de los materiales para actualizar campos dinámicamente
    var materialesData = {};
    
    @foreach($materiales as $material)
        console.log('📦 Procesando material ID {{ $material->id }}:');
        console.log('   - Nombre equipo: {{ $material->equipo->nombre_equipo ?? "NO EQUIPO" }}');
        console.log('   - Nombre material: {{ $material->nombre_equipo ?? "NO NOMBRE" }}');
        console.log('   - Serie: {{ $material->equipo->nro_serie ?? "NO SERIE" }}');
        console.log('   - Código: {{ $material->codigo_equipo ?? "NO CODIGO" }}');
        
        materialesData[{{ $material->id }}] = {
            nombre: '{{ addslashes($material->equipo->nombre_equipo ?? $material->nombre_equipo ?? '') }}',
            serie: '{{ addslashes($material->equipo->nro_serie ?? '') }}',
            codigo: '{{ addslashes($material->codigo_equipo ?? '') }}'
        };
    @endforeach
    
    console.log('📊 Materiales cargados:', materialesData);
    console.log('📊 Número de materiales:', Object.keys(materialesData).length);
    console.log('📊 Claves de materiales:', Object.keys(materialesData));
    
    // Función para actualizar campos de equipo según materiales seleccionados
    function actualizarCamposEquipo() {
        console.log('🔄 Iniciando actualización de campos...');
        console.log('🔍 Buscando selectores .entregable-select...');
        
        var selectores = $('.entregable-select');
        console.log('📊 Selectores encontrados:', selectores.length);
        
        var equiposEntregables = [];
        var seriesEntregables = [];
        
        selectores.each(function(index) {
            var $select = $(this);
            var name = $select.attr('name');
            console.log('🔍 Selector ' + index + ':', name);
            
            var materialId = name.match(/\[(\d+)\]/)[1];
            var entregable = $select.val();
            var materialData = materialesData[materialId];
            
            console.log('📦 Material ' + index + ':', {
                id: materialId,
                entregable: entregable,
                materialData: materialData,
                nombre: materialData ? materialData.nombre : 'NO DATA',
                serie: materialData ? materialData.serie : 'NO DATA'
            });
            
            if (entregable === '1' && materialData && materialData.nombre) {
                var equipoText = materialData.nombre;
                if (materialData.codigo) {
                    equipoText += ' (' + materialData.codigo + ')';
                }
                equiposEntregables.push(equipoText);
                seriesEntregables.push(materialData.serie || 'Sin serie');
                console.log('✅ Agregando material entregable:', equipoText);
            }
        });
        
        console.log('📋 Equipos entregables finales:', equiposEntregables);
        console.log('📋 Series entregables finales:', seriesEntregables);
        
        // Actualizar campos
        if (equiposEntregables.length > 0) {
            var equiposText = equiposEntregables.join('\n• ');
            var seriesText = seriesEntregables.join('\n• ');
            
            console.log('✅ Actualizando campos:');
            console.log('   Equipos:', equiposText);
            console.log('   Series:', seriesText);
            
            $('#equipo_entregado').val(equiposText);
            $('#nro_serie_equipo').val(seriesText);
            
            $('#equipo_entregado, #nro_serie_equipo').removeClass('text-muted').addClass('text-success');
        } else {
            console.log('⚠️ No hay equipos entregables');
            $('#equipo_entregado').val('Seleccione materiales como entregables');
            $('#nro_serie_equipo').val('Seleccione materiales como entregables');
            $('#equipo_entregado, #nro_serie_equipo').removeClass('text-success').addClass('text-muted');
        }
    }
    
    // Evento cuando cambia la selección de entregable
    $(document).on('change', '.entregable-select', function() {
        console.log('🔄 Cambio detectado en select de entregable');
        console.log('   Selector:', $(this).attr('name'));
        console.log('   Valor:', $(this).val());
        actualizarCamposEquipo();
    });
    
    // Inicializar campos al cargar la página
    console.log('🚀 Inicializando campos al cargar la página...');
    actualizarCamposEquipo();
    
    // También ejecutar después de un pequeño delay para asegurar que todo esté cargado
    setTimeout(function() {
        console.log('⏰ Ejecutando actualización después de delay...');
        actualizarCamposEquipo();
    }, 1000);
    
    // Ejecutar cada 2 segundos para debugging
    setInterval(function() {
        console.log('🔄 Ejecutando actualización automática...');
        actualizarCamposEquipo();
    }, 2000);
    
    // Validación del formulario
    $('#form-prestamo').on('submit', function(e) {
        var materialesNoEntregables = $('select[name*="[entregable]"]').filter(function() {
            return $(this).val() === '0';
        });
        
        var materialesSinObservacion = materialesNoEntregables.filter(function() {
            var materialId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            var observacion = $('textarea[name="materiales[' + materialId + '][observaciones]"]').val();
            return !observacion.trim();
        });
        
        if (materialesSinObservacion.length > 0) {
            e.preventDefault();
            alert('Por favor, ingrese una razón u observación para los materiales que no se pueden entregar.');
            return false;
        }
        
        // Validar que al menos un material se pueda entregar
        var materialesEntregables = $('select[name*="[entregable]"]').filter(function() {
            return $(this).val() === '1';
        });
        
        if (materialesEntregables.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un material como entregable.');
            return false;
        }
        
        // Validar que se haya especificado equipo entregado
        if (!$('#equipo_entregado').val().trim()) {
            e.preventDefault();
            alert('Debe seleccionar al menos un material como entregable para especificar el equipo.');
            return false;
        }
    });
});
</script>

@endsection 