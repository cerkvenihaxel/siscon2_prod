@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-print"></i> Imprimir Documentos - Pedido: {{ $pedido->nro_solicitud }}
        </h3>
    </div>
    <div class="panel-body">
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

        <!-- Documentos Disponibles -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Documentos Disponibles</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Consentimiento Informado -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Consentimiento Informado
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Documento de consentimiento informado para el tratamiento de oxigenoterapia.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.consentimiento', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Términos y Condiciones -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Términos y Condiciones
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Términos y condiciones del préstamo de equipos de oxigenoterapia.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.terminos', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Contrato de Préstamo -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Contrato de Préstamo
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Contrato formal de préstamo de equipos médicos.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.contrato', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <!-- Hoja de Instrucciones -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Hoja de Instrucciones
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Instrucciones de uso y mantenimiento del equipo.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.instrucciones', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Checklist de Entrega -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Checklist de Entrega
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Lista de verificación para la entrega de equipos.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.checklist', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen del Pedido -->
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Resumen del Pedido
                                        </h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>Resumen completo del pedido y materiales solicitados.</p>
                                        <a href="{{ route('oxigenoterapia.documentos.resumen', $pedido->id) }}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                <button type="button" class="btn btn-success" onclick="descargarTodos()">
                    <i class="fa fa-download"></i> Descargar Todos los Documentos
                </button>
                <button type="button" class="btn btn-info" onclick="imprimirTodos()">
                    <i class="fa fa-print"></i> Imprimir Todos
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function descargarTodos() {
    if (confirm('¿Desea descargar todos los documentos?')) {
        // Abrir todos los enlaces en nuevas pestañas
        var enlaces = [
            '{{ route("oxigenoterapia.documentos.consentimiento", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.terminos", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.contrato", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.instrucciones", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.checklist", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.resumen", $pedido->id) }}'
        ];
        
        enlaces.forEach(function(enlace) {
            window.open(enlace, '_blank');
        });
    }
}

function imprimirTodos() {
    if (confirm('¿Desea imprimir todos los documentos?')) {
        // Abrir todos los enlaces en nuevas pestañas para imprimir
        var enlaces = [
            '{{ route("oxigenoterapia.documentos.consentimiento", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.terminos", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.contrato", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.instrucciones", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.checklist", $pedido->id) }}',
            '{{ route("oxigenoterapia.documentos.resumen", $pedido->id) }}'
        ];
        
        enlaces.forEach(function(enlace) {
            var ventana = window.open(enlace, '_blank');
            ventana.onload = function() {
                ventana.print();
            };
        });
    }
}
</script>

@endsection 