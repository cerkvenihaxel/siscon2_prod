@extends('crudbooster::admin_template')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-eye"></i> Ver Préstamo - {{ $prestamo->nro_prestamo }}
        </h3>
    </div>
    <div class="panel-body">
        <!-- Información del Préstamo -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Información del Préstamo</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Número de Préstamo:</strong><br>
                                {{ $prestamo->nro_prestamo }}
                            </div>
                            <div class="col-md-3">
                                <strong>Número de Solicitud:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->nro_solicitud ?? 'No especificado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Estado:</strong><br>
                                <span class="label label-{{ $prestamo->estado_prestamo == 'ACTIVO' ? 'success' : ($prestamo->estado_prestamo == 'FINALIZADO' ? 'danger' : 'warning') }}">
                                    {{ $prestamo->estado_prestamo }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Usuario:</strong><br>
                                {{ $prestamo->stamp_user }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <strong>Fecha de Inicio:</strong><br>
                                {{ date('d/m/Y', strtotime($prestamo->fecha_inicio_prestamo)) }}
                            </div>
                            <div class="col-md-4">
                                <strong>Fecha de Fin:</strong><br>
                                {{ date('d/m/Y', strtotime($prestamo->fecha_fin_prestamo)) }}
                            </div>
                            <div class="col-md-4">
                                <strong>Fecha de Entrega:</strong><br>
                                {{ $prestamo->fecha_entrega ? date('d/m/Y', strtotime($prestamo->fecha_entrega)) : 'No entregado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                {{ $prestamo->pedidoOxigenoterapia->nombre_apellido ?? 'No especificado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Nro. Afiliado:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->nro_afiliado ?? 'No especificado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Documento:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->documento ?? 'No especificado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Edad:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->edad ?? 'No especificado' }} años
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <strong>Teléfono:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->tel_afiliado ?: 'No especificado' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->email ?: 'No especificado' }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <strong>Médico:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->medicos->nombremedico ?? 'No especificado' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Clínica:</strong><br>
                                {{ $prestamo->pedidoOxigenoterapia->clinica->nombre ?? 'No especificada' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Entrega -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">Información de Entrega</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Tipo de Dirección:</strong><br>
                                {{ $prestamo->tipo_direccion }}
                            </div>
                            <div class="col-md-6">
                                <strong>Dirección:</strong><br>
                                {{ $prestamo->direccion_entrega }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <strong>Localidad:</strong><br>
                                {{ $prestamo->localidad_entrega }}
                            </div>
                            <div class="col-md-4">
                                <strong>Provincia:</strong><br>
                                {{ $prestamo->provincia_entrega }}
                            </div>
                            <div class="col-md-4">
                                <strong>Código Postal:</strong><br>
                                {{ $prestamo->codigo_postal ?: 'No especificado' }}
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6">
                                <strong>Teléfono de Contacto:</strong><br>
                                {{ $prestamo->telefono_contacto }}
                            </div>
                            <div class="col-md-6">
                                <strong>Nombre de Contacto:</strong><br>
                                {{ $prestamo->nombre_contacto }}
                            </div>
                        </div>
                        @if($prestamo->observaciones_entrega)
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <strong>Observaciones de Entrega:</strong><br>
                                {{ $prestamo->observaciones_entrega }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Equipo -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">Información del Equipo</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Equipos Entregados:</strong><br>
                                @php
                                    $equiposEntregables = $materiales->where('entregable', 1);
                                    $equiposList = [];
                                    foreach($equiposEntregables as $material) {
                                        $equiposList[] = $material->nombre_equipo . ' (' . $material->codigo_equipo . ')';
                                    }
                                @endphp
                                @if(count($equiposList) > 0)
                                    @foreach($equiposList as $equipo)
                                        • {{ $equipo }}<br>
                                    @endforeach
                                @else
                                    <span class="text-muted">No hay equipos marcados como entregables</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Números de Serie:</strong><br>
                                @php
                                    $seriesList = [];
                                    foreach($equiposEntregables as $material) {
                                        $serie = $material->equipo->nro_serie ?? 'Sin serie';
                                        $seriesList[] = $serie;
                                    }
                                @endphp
                                @if(count($seriesList) > 0)
                                    @foreach($seriesList as $serie)
                                        • {{ $serie }}<br>
                                    @endforeach
                                @else
                                    <span class="text-muted">No hay números de serie disponibles</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materiales del Pedido -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-list"></i> Materiales del Pedido
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
                                            <th width="15%">Entregable</th>
                                            <th width="25%">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materiales as $index => $material)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $material->codigo_equipo }}</td>
                                            <td>{{ $material->nombre_equipo }}</td>
                                            <td>{{ $material->cantidad }}</td>
                                            <td>
                                                @if($material->entregable == 1)
                                                    <span class="label label-success">Sí</span>
                                                @elseif($material->entregable == 0)
                                                    <span class="label label-danger">No</span>
                                                @else
                                                    <span class="label label-default">No especificado</span>
                                                @endif
                                            </td>
                                            <td>{{ $material->observaciones_entrega ?: 'Sin observaciones' }}</td>
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
                <a href="{{ url('/admin/prestamo_oxigenoterapia') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
                @if($prestamo->estado_prestamo == 'ACTIVO')
                    <a href="{{ url('/admin/prestamo_oxigenoterapia/edit/'.$prestamo->id) }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Editar
                    </a>
                    <a href="{{ url('/admin/prestamo_oxigenoterapia/renovar/'.$prestamo->id) }}" class="btn btn-warning">
                        <i class="fa fa-refresh"></i> Renovar
                    </a>
                    <a href="{{ url('/admin/prestamo_oxigenoterapia/finalizar/'.$prestamo->id) }}" class="btn btn-danger">
                        <i class="fa fa-stop"></i> Finalizar
                    </a>
                @endif
                <a href="{{ url('/admin/prestamo_oxigenoterapia/documentos/'.$prestamo->id) }}" class="btn btn-info">
                    <i class="fa fa-file-text"></i> Documentos
                </a>
            </div>
        </div>
    </div>
</div>

@endsection 