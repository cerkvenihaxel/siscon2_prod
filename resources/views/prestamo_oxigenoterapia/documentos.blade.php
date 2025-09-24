@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-file-text"></i> Documentos del Préstamo - {{ $prestamo->nro_prestamo }}
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
                                <strong>Estado:</strong><br>
                                <span class="label label-{{ $prestamo->estado_prestamo == 'ACTIVO' ? 'success' : ($prestamo->estado_prestamo == 'RENOVADO' ? 'warning' : 'danger') }}">
                                    {{ $prestamo->estado_prestamo }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Documentos:</strong><br>
                                {{ $documentos->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subir Nuevo Documento -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-upload"></i> Subir Documento Firmado
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ url('/admin/prestamo-oxigenoterapia/documentos/' . $prestamo->id . '/subir') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo_documento">Tipo de Documento</label>
                                        <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                            <option value="">Seleccionar tipo...</option>
                                            <option value="consentimiento">Consentimiento Informado</option>
                                            <option value="terminos">Términos y Condiciones</option>
                                            <option value="contrato">Contrato de Préstamo</option>
                                            <option value="instrucciones">Instrucciones de Uso</option>
                                            <option value="checklist">Checklist de Entrega</option>
                                            <option value="resumen">Resumen del Pedido</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="archivo">Archivo PDF</label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="archivo" 
                                               name="archivo" 
                                               accept=".pdf" 
                                               required>
                                        <small class="form-text text-muted">Máximo 10MB</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="observaciones">Observaciones</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="observaciones" 
                                               name="observaciones" 
                                               placeholder="Observaciones opcionales">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Subir Documento
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Documentos -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-folder"></i> Documentos Almacenados
                        </h4>
                    </div>
                    <div class="panel-body">
                        @if($documentos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Nombre del Archivo</th>
                                            <th>Tamaño</th>
                                            <th>Fecha de Subida</th>
                                            <th>Firmado Por</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documentos as $documento)
                                        <tr>
                                            <td>
                                                <span class="label label-info">{{ $documento->tipo_documento_label }}</span>
                                            </td>
                                            <td>{{ $documento->nombre_archivo }}</td>
                                            <td>{{ $documento->tamaño_formateado }}</td>
                                            <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $documento->firmado_por ?? 'No especificado' }}</td>
                                            <td>
                                                @if($documento->firmado)
                                                    <span class="label label-success">
                                                        <i class="fa fa-check"></i> Firmado
                                                    </span>
                                                @else
                                                    <span class="label label-warning">
                                                        <i class="fa fa-clock-o"></i> Pendiente
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ $documento->url_ver }}" 
                                                       class="btn btn-xs btn-info" 
                                                       target="_blank" 
                                                       title="Ver documento">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ $documento->url_descarga }}" 
                                                       class="btn btn-xs btn-success" 
                                                       title="Descargar">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-xs btn-danger" 
                                                            onclick="eliminarDocumento({{ $documento->id }})" 
                                                            title="Eliminar">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle"></i> No hay documentos almacenados para este préstamo.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Documentos -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-bar-chart"></i> Estadísticas de Documentos
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            @php
                                $tiposDocumento = ['consentimiento', 'terminos', 'contrato', 'instrucciones', 'checklist', 'resumen'];
                                $estadisticas = [];
                                foreach($tiposDocumento as $tipo) {
                                    $estadisticas[$tipo] = $documentos->where('tipo_documento', $tipo)->count();
                                }
                            @endphp
                            
                            @foreach($estadisticas as $tipo => $cantidad)
                            <div class="col-md-2 text-center">
                                <div class="well">
                                    <h4>{{ $cantidad }}</h4>
                                    <small>{{ \App\Models\DocumentosOxigenoterapia::TIPOS_DOCUMENTO[$tipo] ?? $tipo }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="{{ url('/admin/prestamo_oxigenoterapia') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver a Préstamos
                </a>
                <a href="{{ url('/admin/oxigenoterapia/imprimir/' . $prestamo->pedido_oxigenoterapia_id) }}" class="btn btn-info">
                    <i class="fa fa-print"></i> Generar Documentos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar Eliminación</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este documento?</p>
                <p><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
let documentoAEliminar = null;

function eliminarDocumento(documentoId) {
    documentoAEliminar = documentoId;
    $('#modalEliminar').modal('show');
}

document.getElementById('confirmarEliminar').addEventListener('click', function() {
    if (documentoAEliminar) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("/admin/prestamo-oxigenoterapia/documentos") }}/' + documentoAEliminar;
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Agregar método DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Validación del formulario de subida
document.querySelector('form[enctype="multipart/form-data"]').addEventListener('submit', function(e) {
    const archivo = document.getElementById('archivo').files[0];
    const tipoDocumento = document.getElementById('tipo_documento').value;
    
    if (!tipoDocumento) {
        e.preventDefault();
        alert('Debe seleccionar un tipo de documento.');
        return false;
    }
    
    if (!archivo) {
        e.preventDefault();
        alert('Debe seleccionar un archivo.');
        return false;
    }
    
    if (archivo.size > 10 * 1024 * 1024) { // 10MB
        e.preventDefault();
        alert('El archivo es demasiado grande. Máximo 10MB.');
        return false;
    }
    
    if (!archivo.type.includes('pdf')) {
        e.preventDefault();
        alert('Solo se permiten archivos PDF.');
        return false;
    }
});
</script>

@endsection 