@extends('crudbooster::admin_template')
@section('content')

<div class="modal-header">
    <h4 class="modal-title">Materiales del Pedido: {{ $pedido->nro_solicitud }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h5>Información del Afiliado</h5>
            <table class="table table-bordered">
                <tr>
                    <td><strong>Nombre:</strong></td>
                    <td>{{ $pedido->nombre_apellido }}</td>
                    <td><strong>Nro. Afiliado:</strong></td>
                    <td>{{ $pedido->nro_afiliado }}</td>
                </tr>
                <tr>
                    <td><strong>Documento:</strong></td>
                    <td>{{ $pedido->documento }}</td>
                    <td><strong>Edad:</strong></td>
                    <td>{{ $pedido->edad }} años</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5>Materiales Solicitados</h5>
            @if($materiales->count() > 0)
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código Equipo</th>
                            <th>Nombre del Equipo</th>
                            <th>Cantidad</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materiales as $index => $material)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $material->codigo_equipo }}</td>
                            <td>{{ $material->nombre_equipo }}</td>
                            <td>{{ $material->cantidad }}</td>
                            <td>{{ $material->observaciones ?: 'Sin observaciones' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> No se encontraron materiales para este pedido.
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5>Información Médica</h5>
            <table class="table table-bordered">
                <tr>
                    <td><strong>Médico:</strong></td>
                    <td>{{ $pedido->medicos->nombremedico ?? 'No especificado' }}</td>
                    <td><strong>Clínica:</strong></td>
                    <td>{{ $pedido->clinica->nombre ?? 'No especificada' }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha Prescripción:</strong></td>
                    <td>{{ $pedido->fecha_prescripcion ? $pedido->fecha_prescripcion->format('d/m/Y') : 'No especificada' }}</td>
                    <td><strong>Fecha Vencimiento:</strong></td>
                    <td>{{ $pedido->fecha_vencimiento ? $pedido->fecha_vencimiento->format('d/m/Y') : 'No especificada' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    <button type="button" class="btn btn-primary" onclick="window.print()">
        <i class="fa fa-print"></i> Imprimir
    </button>
</div>

@endsection 