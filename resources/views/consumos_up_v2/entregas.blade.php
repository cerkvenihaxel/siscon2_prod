@extends('crudbooster::admin_template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-truck"></i> Validaciones y Entregas de Consumos UP
                </h3>
                <div class="box-tools pull-right">
                    <a href="/admin/consumos_up_v2" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver a Consumos
                    </a>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="box-body">
                <!-- Botones de filtro rápido -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <a href="/admin/consumos_up_v2/entregas" class="btn btn-default {{ !request('estado_entrega') ? 'active' : '' }}">
                                <i class="fa fa-list"></i> Todas
                            </a>
                            <a href="/admin/consumos_up_v2/entregas?estado_entrega=completa" class="btn btn-success {{ request('estado_entrega') == 'completa' ? 'active' : '' }}">
                                <i class="fa fa-check-circle"></i> Completas 
                                <span class="badge">{{ $contadores['completas'] }}</span>
                            </a>
                            <a href="/admin/consumos_up_v2/entregas?estado_entrega=parcial" class="btn btn-warning {{ request('estado_entrega') == 'parcial' ? 'active' : '' }}">
                                <i class="fa fa-exclamation-triangle"></i> Parciales 
                                <span class="badge">{{ $contadores['parciales'] }}</span>
                            </a>
                            <a href="/admin/consumos_up_v2/entregas?estado_entrega=no_entregado" class="btn btn-danger {{ request('estado_entrega') == 'no_entregado' ? 'active' : '' }}">
                                <i class="fa fa-times-circle"></i> No Entregados 
                                <span class="badge">{{ $contadores['no_entregados'] }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <form method="GET" class="form-inline">
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Código Afiliado:</label>
                        <input type="text" name="codigo_afiliado" class="form-control" value="{{ request('codigo_afiliado') }}" placeholder="Código afiliado">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" class="form-control" value="{{ request('nombre') }}" placeholder="Nombre afiliado">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Estado:</label>
                        <select name="estado_entrega" class="form-control">
                            <option value="">Todos</option>
                            <option value="completa" {{ request('estado_entrega') == 'completa' ? 'selected' : '' }}>Completa</option>
                            <option value="parcial" {{ request('estado_entrega') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                            <option value="no_entregado" {{ request('estado_entrega') == 'no_entregado' ? 'selected' : '' }}>No Entregado</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Desde:</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="form-group" style="margin-right: 10px;">
                        <label>Hasta:</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </form>
            </div>
            
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'fecha_entrega', 'dir' => request('sort') == 'fecha_entrega' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Fecha Entrega
                                    @if(request('sort') == 'fecha_entrega')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'codigo_afiliado', 'dir' => request('sort') == 'codigo_afiliado' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Afiliado
                                    @if(request('sort') == 'codigo_afiliado')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'afiliado_nombre', 'dir' => request('sort') == 'afiliado_nombre' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Nombre
                                    @if(request('sort') == 'afiliado_nombre')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Medicamento</th>
                            <th>Cant. Solicitada</th>
                            <th>Cant. Entregada</th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'estado_entrega', 'dir' => request('sort') == 'estado_entrega' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Estado
                                    @if(request('sort') == 'estado_entrega')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Lugar</th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'usuario_validador', 'dir' => request('sort') == 'usuario_validador' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Usuario
                                    @if(request('sort') == 'usuario_validador')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'created_at', 'dir' => request('sort') == 'created_at' && request('dir') == 'desc' ? 'asc' : 'desc'])) }}">
                                    Registrado
                                    @if(request('sort') == 'created_at')
                                        <i class="fa fa-sort-{{ request('dir') == 'desc' ? 'desc' : 'asc' }}"></i>
                                    @else
                                        <i class="fa fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entregas as $entrega)
                        <tr class="
                            @if($entrega->estado_entrega == 'completa') success
                            @elseif($entrega->estado_entrega == 'parcial') warning
                            @elseif($entrega->estado_entrega == 'no_entregado') danger
                            @endif">
                            <td>{{ date('d/m/Y H:i', strtotime($entrega->fecha_entrega)) }}</td>
                            <td><strong>{{ $entrega->codigo_afiliado }}</strong></td>
                            <td>{{ $entrega->afiliado_nombre }}</td>
                            <td>
                                <small>{{ Str::limit($entrega->medicamento_descripcion, 40) }}</small><br>
                                <code>{{ $entrega->medicamento_codigo }}</code>
                            </td>
                            <td><span class="badge badge-info">{{ $entrega->cantidad_solicitada }}</span></td>
                            <td><span class="badge badge-primary">{{ $entrega->cantidad_entregada }}</span></td>
                            <td>
                                @php
                                    $badges = [
                                        'completa' => 'success',
                                        'parcial' => 'warning',
                                        'no_entregado' => 'danger'
                                    ];
                                    $color = $badges[$entrega->estado_entrega] ?? 'default';
                                    $texto = strtoupper(str_replace('_', ' ', $entrega->estado_entrega));
                                @endphp
                                <span class="label label-{{ $color }}">{{ $texto }}</span>
                            </td>
                            <td>{{ $entrega->lugar_entrega ?? 'N/A' }}</td>
                            <td>{{ $entrega->usuario_validador }}</td>
                            <td><small>{{ date('d/m/Y H:i', strtotime($entrega->created_at)) }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="box-footer">
                {{ $entregas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.btn-group .btn.active {
    background-color: #337ab7;
    color: white;
}

.btn .badge {
    background-color: rgba(255,255,255,0.3);
    color: inherit;
}

th a {
    color: inherit;
    text-decoration: none;
}

th a:hover {
    color: #337ab7;
}

.fa-sort, .fa-sort-asc, .fa-sort-desc {
    margin-left: 5px;
}

.form-group {
    margin-bottom: 10px;
}

.form-group label {
    margin-right: 5px;
    font-weight: normal;
}
</style>
@endsection
