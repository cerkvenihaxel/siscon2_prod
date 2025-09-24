@extends('crudbooster::admin_template')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-handshake-o"></i> Realizar Préstamo de Oxigenoterapia
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Datos del Paciente</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Nro Solicitud:</strong></td>
                                <td>{{ $pedido->nro_solicitud }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $pedido->nombre_apellido }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nro Afiliado:</strong></td>
                                <td>{{ $pedido->nro_afiliado }}</td>
                            </tr>
                            <tr>
                                <td><strong>Documento:</strong></td>
                                <td>{{ $pedido->documento }}</td>
                            </tr>
                            <tr>
                                <td><strong>Edad:</strong></td>
                                <td>{{ $pedido->edad }} años</td>
                            </tr>
                            <tr>
                                <td><strong>Médico:</strong></td>
                                <td>{{ $pedido->medico->nombremedico ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Clínica:</strong></td>
                                <td>{{ $pedido->clinica->nombre ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Datos de la Prescripción</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Fecha Prescripción:</strong></td>
                                <td>{{ $pedido->fecha_prescripcion->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha Vencimiento:</strong></td>
                                <td>{{ $pedido->fecha_vencimiento->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Zona Residencia:</strong></td>
                                <td>{{ $pedido->zona_residencia }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tel Afiliado:</strong></td>
                                <td>{{ $pedido->tel_afiliado ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $pedido->email ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <form action="{{ route('prestamo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <h4>Datos del Préstamo</h4>
                            
                            <div class="form-group">
                                <label for="fecha_inicio_prestamo">Fecha de Inicio del Préstamo *</label>
                                <input type="date" class="form-control" id="fecha_inicio_prestamo" name="fecha_inicio_prestamo" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin_prestamo">Fecha de Finalización del Préstamo *</label>
                                <input type="date" class="form-control" id="fecha_fin_prestamo" name="fecha_fin_prestamo" 
                                       value="{{ $pedido->fecha_vencimiento->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="equipo_id">Equipo a Prestar *</label>
                                <select class="form-control" id="equipo_id" name="equipo_id" required>
                                    <option value="">Seleccione un equipo</option>
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo->id }}">
                                            {{ $equipo->nombre_equipo }} - {{ $equipo->marca }} {{ $equipo->modelo }} 
                                            (S/N: {{ $equipo->nro_serie }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4>Datos de Entrega</h4>
                            
                            <div class="form-group">
                                <label for="tipo_direccion">Tipo de Dirección *</label>
                                <select class="form-control" id="tipo_direccion" name="tipo_direccion" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="PARTICULAR">Domicilio Particular</option>
                                    <option value="CLINICA">Clínica/Hospital</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="direccion_entrega">Dirección de Entrega *</label>
                                <input type="text" class="form-control" id="direccion_entrega" name="direccion_entrega" 
                                       placeholder="Calle, número, piso, depto" required>
                            </div>

                            <div class="form-group">
                                <label for="localidad_entrega">Localidad *</label>
                                <input type="text" class="form-control" id="localidad_entrega" name="localidad_entrega" required>
                            </div>

                            <div class="form-group">
                                <label for="provincia_entrega">Provincia *</label>
                                <input type="text" class="form-control" id="provincia_entrega" name="provincia_entrega" required>
                            </div>

                            <div class="form-group">
                                <label for="codigo_postal">Código Postal</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
                            </div>

                            <div class="form-group">
                                <label for="nombre_contacto">Nombre de Contacto *</label>
                                <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto" 
                                       value="{{ $pedido->nombre_apellido }}" required>
                            </div>

                            <div class="form-group">
                                <label for="telefono_contacto">Teléfono de Contacto *</label>
                                <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" 
                                       value="{{ $pedido->tel_afiliado }}" required>
                            </div>

                            <div class="form-group">
                                <label for="observaciones_entrega">Observaciones de Entrega</label>
                                <textarea class="form-control" id="observaciones_entrega" name="observaciones_entrega" 
                                          rows="3" placeholder="Instrucciones especiales, horarios preferidos, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Información Importante</h5>
                                <ul>
                                    <li>Al crear el préstamo se generarán automáticamente los documentos de términos y condiciones.</li>
                                    <li>El paciente deberá firmar los documentos antes de recibir el equipo.</li>
                                    <li>El equipo será marcado como "EN_USO" durante el período del préstamo.</li>
                                    <li>Se enviará una notificación al paciente con los detalles del préstamo.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Crear Préstamo
                                </button>
                                <a href="{{ url('/admin/oxigenoterapia') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('bottom')
<script>
$(document).ready(function() {
    // Validación de fechas
    $('#fecha_fin_prestamo').change(function() {
        var fechaInicio = $('#fecha_inicio_prestamo').val();
        var fechaFin = $(this).val();
        
        if (fechaInicio && fechaFin && fechaFin <= fechaInicio) {
            alert('La fecha de finalización debe ser posterior a la fecha de inicio');
            $(this).val('');
        }
    });

    // Auto-completar dirección si es clínica
    $('#tipo_direccion').change(function() {
        if ($(this).val() === 'CLINICA') {
            var clinica = '{{ $pedido->clinica->nombre ?? "" }}';
            if (clinica) {
                $('#direccion_entrega').val('Clínica: ' + clinica);
            }
        }
    });
});
</script>
@endpush 