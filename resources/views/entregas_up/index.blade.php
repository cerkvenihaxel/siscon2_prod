<!DOCTYPE html>
<html>
<head>
    <title>Entregas UP - Sistema de Gestión</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-content { padding: 20px; }
        .stats-card { margin-bottom: 20px; }
        .filter-card { margin-bottom: 20px; }
        .table-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .status-chip { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .status-completa { background: #00b894; color: white; }
        .status-parcial { background: #fdcb6e; color: #333; }
        .status-rechazada { background: #d63031; color: white; }
        .btn-action { margin: 2px; }
    </style>
</head>
<body>
    @include('components.up_topbar')
    @include('components.up_sidebar')

    <div class="main-content">
        <div class="row">
            <div class="col s12">
                <div class="card-panel teal lighten-5">
                    <h4 class="teal-text text-darken-2">
                        <i class="material-icons left">assignment_turned_in</i>
                        Entregas UP - Gestión de Entregas
                    </h4>
                    <p class="grey-text">Registro y seguimiento de entregas de prestaciones</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row stats-card">
            <div class="col s12 m3">
                <div class="card green lighten-4">
                    <div class="card-content center">
                        <h5 class="green-text text-darken-2">{{ $contadores['completas'] }}</h5>
                        <p class="green-text text-darken-1">Entregas Completas</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card orange lighten-4">
                    <div class="card-content center">
                        <h5 class="orange-text text-darken-2">{{ $contadores['parciales'] }}</h5>
                        <p class="orange-text text-darken-1">Entregas Parciales</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card red lighten-4">
                    <div class="card-content center">
                        <h5 class="red-text text-darken-2">{{ $contadores['rechazadas'] }}</h5>
                        <p class="red-text text-darken-1">Rechazadas</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card grey lighten-3">
                    <div class="card-content center">
                        <h5 class="grey-text text-darken-2">{{ $contadores['total'] }}</h5>
                        <p class="grey-text text-darken-1">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card filter-card">
            <div class="card-content">
                <form method="GET" id="filtrosForm">
                    <div class="row">
                        <div class="input-field col s12 m2">
                            <input id="codigo_afiliado" name="codigo_afiliado" type="text" value="{{ request('codigo_afiliado') }}">
                            <label for="codigo_afiliado">Código Afiliado</label>
                        </div>
                        <div class="input-field col s12 m3">
                            <input id="nombre_afiliado" name="nombre_afiliado" type="text" value="{{ request('nombre_afiliado') }}">
                            <label for="nombre_afiliado">Nombre Afiliado</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <select name="estado_entrega">
                                <option value="">Todos</option>
                                <option value="completa" {{ request('estado_entrega') == 'completa' ? 'selected' : '' }}>Completa</option>
                                <option value="parcial" {{ request('estado_entrega') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                                <option value="rechazada" {{ request('estado_entrega') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                            <label>Estado</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="fecha_desde" name="fecha_desde" type="date" value="{{ request('fecha_desde') }}">
                            <label for="fecha_desde">Desde</label>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ request('fecha_hasta') }}">
                            <label for="fecha_hasta">Hasta</label>
                        </div>
                        <div class="col s12 m1">
                            <button type="submit" class="btn waves-effect waves-light full-width" style="margin-top: 25px;">
                                <i class="material-icons">search</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Entregas -->
        <div class="card table-card">
            <div class="card-content">
                <div class="card-title">
                    <i class="material-icons left">table_chart</i>
                    Lista de Entregas
                </div>

                <div class="table-responsive">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha Entrega</th>
                                <th>Afiliado</th>
                                <th>Nombre</th>
                                <th>Prestación</th>
                                <th>Cantidad</th>
                                <th>Quien Recibe</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entregas as $entrega)
                            <tr>
                                <td>#{{ $entrega->id }}</td>
                                <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                                <td>{{ $entrega->codigo_afiliado }}</td>
                                <td>{{ $entrega->nombre_afiliado }}</td>
                                <td>{{ $entrega->descripcion_prestacion }}</td>
                                <td>{{ $entrega->cantidad_entregada }}/{{ $entrega->cantidad_solicitada }}</td>
                                <td>{{ $entrega->quien_recibe }}</td>
                                <td>
                                    <span class="status-chip status-{{ $entrega->estado_entrega }}">
                                        {{ strtoupper($entrega->estado_entrega) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/entregas-up/{{ $entrega->id }}/consentimiento"
                                       class="btn btn-small blue waves-effect waves-light btn-action tooltipped"
                                       data-position="top"
                                       data-tooltip="Ver Consentimiento"
                                       target="_blank">
                                        <i class="material-icons">description</i>
                                    </a>
                                    <a href="/admin/consumos-up/{{ $entrega->consumo_id }}"
                                       class="btn btn-small teal waves-effect waves-light btn-action tooltipped"
                                       data-position="top"
                                       data-tooltip="Ver Consumo"
                                       target="_blank">
                                        <i class="material-icons">visibility</i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="center">No hay entregas registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col s6">
                        <span class="grey-text">
                            Mostrando {{ $entregas->firstItem() ?? 0 }} - {{ $entregas->lastItem() ?? 0 }} de {{ $entregas->total() }} registros
                        </span>
                    </div>
                    <div class="col s6 right-align">
                        {{ $entregas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            M.AutoInit();
        });
    </script>
</body>
</html>
