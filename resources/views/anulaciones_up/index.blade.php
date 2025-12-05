<!DOCTYPE html>
<html>
<head>
    <title>Anulaciones UP - Sistema de Gestión</title>
    <link rel="icon" type="image/png" href="{{ asset('SISCON.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Material UI CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-content { padding: 20px; }
        .stats-card { margin-bottom: 20px; }
        .filter-card { margin-bottom: 20px; }
        .table-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .status-chip { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .status-ok { background: #00b894; color: white; }
        .status-no { background: #d63031; color: white; }
        .status-error { background: #d63031; color: white; }
        .status-pend { background: #fdcb6e; color: #856404; }
        .tipo-chip { padding: 4px 8px; border-radius: 8px; font-size: 10px; font-weight: 500; }
        .tipo-idtran { background: #0984e3; color: white; }
        .tipo-msgid { background: #e17055; color: white; }
        .tipo-idaut { background: #6c5ce7; color: white; }
        .btn-action { margin: 2px; }
        .pagination-wrapper { display: inline-flex; align-items: center; }
        .pagination-wrapper .btn-flat { margin: 0 2px; min-width: 36px; height: 36px; line-height: 36px; padding: 0; text-align: center; }
        .pagination-wrapper .disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    @include('components.up_topbar')
    @include('components.up_sidebar')

    <div class="main-content">
        <!-- Header -->
        <div class="row">
            <div class="col s12">
                <div class="card-panel red lighten-5">
                    <h4 class="red-text text-darken-2">
                        <i class="material-icons left">cancel</i>
                        Anulaciones UP - Gestión de Transacciones
                    </h4>
                    <p class="grey-text">Administración de anulaciones de transacciones ATR</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row stats-card">
            <div class="col s12 m3">
                <div class="card-panel green white-text center">
                    <i class="material-icons large">check_circle</i>
                    <h4>{{ $contadores['exitosas'] }}</h4>
                    <span>Exitosas</span>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card-panel red white-text center">
                    <i class="material-icons large">error</i>
                    <h4>{{ $contadores['fallidas'] }}</h4>
                    <span>Fallidas</span>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card-panel orange white-text center">
                    <i class="material-icons large">schedule</i>
                    <h4>{{ $contadores['pendientes'] }}</h4>
                    <span>Pendientes</span>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card-panel blue white-text center">
                    <i class="material-icons large">assessment</i>
                    <h4>{{ $contadores['total'] }}</h4>
                    <span>Total</span>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card filter-card">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons left">filter_list</i>
                    Filtros de Búsqueda
                </span>
                <form method="GET" action="{{ url('/admin/anulaciones-up') }}">
                    <div class="row">
                        <div class="input-field col s12 m6 l3">
                            <input type="text" id="codigo_afiliado" name="codigo_afiliado" value="{{ request('codigo_afiliado') }}">
                            <label for="codigo_afiliado">Código Afiliado</label>
                        </div>
                        <div class="input-field col s12 m6 l3">
                            <input type="text" id="afi_nombre" name="afi_nombre" value="{{ request('afi_nombre') }}">
                            <label for="afi_nombre">Nombre/Apellido</label>
                        </div>
                        <div class="input-field col s12 m6 l3">
                            <select name="status">
                                <option value="">Todos los estados</option>
                                <option value="OK" {{ request('status') == 'OK' ? 'selected' : '' }}>OK</option>
                                <option value="NO" {{ request('status') == 'NO' ? 'selected' : '' }}>NO</option>
                                <option value="ERROR" {{ request('status') == 'ERROR' ? 'selected' : '' }}>ERROR</option>
                                <option value="PEND" {{ request('status') == 'PEND' ? 'selected' : '' }}>PEND</option>
                            </select>
                            <label>Estado</label>
                        </div>
                        <div class="input-field col s12 m6 l3">
                            <select name="tipoidanul">
                                <option value="">Todos los tipos</option>
                                <option value="IDTRAN" {{ request('tipoidanul') == 'IDTRAN' ? 'selected' : '' }}>IDTRAN</option>
                                <option value="MSGID" {{ request('tipoidanul') == 'MSGID' ? 'selected' : '' }}>MSGID</option>
                                <option value="IDAUT" {{ request('tipoidanul') == 'IDAUT' ? 'selected' : '' }}>IDAUT</option>
                            </select>
                            <label>Tipo Anulación</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6 l3">
                            <input type="date" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                            <label for="fecha_desde">Fecha Desde</label>
                        </div>
                        <div class="input-field col s12 m6 l3">
                            <input type="date" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                            <label for="fecha_hasta">Fecha Hasta</label>
                        </div>
                        <div class="col s12 m12 l6">
                            <button type="submit" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">search</i>Filtrar
                            </button>
                            <a href="{{ url('/admin/anulaciones-up') }}" class="btn grey waves-effect waves-light">
                                <i class="material-icons left">clear</i>Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Anulaciones -->
        <div class="card table-card">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons left">list</i>
                    Listado de Anulaciones ({{ $anulaciones->total() }} registros)
                </span>
                
                <div class="table-container" style="overflow-x: auto;">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Afiliado</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>ID Anulado</th>
                                <th>Estado</th>
                                <th>Motivo</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anulaciones as $anulacion)
                            <tr>
                                <td>{{ $anulacion->created_at->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $anulacion->codigo_afiliado }}</strong></td>
                                <td>{{ $anulacion->nombre_completo ?: '-' }}</td>
                                <td>
                                    <span class="tipo-chip tipo-{{ strtolower($anulacion->tipoidanul) }}">
                                        {{ $anulacion->tipoidanul }}
                                    </span>
                                </td>
                                <td><code>{{ $anulacion->idanul }}</code></td>
                                <td>
                                    <span class="status-chip status-{{ strtolower($anulacion->status) }}">
                                        {{ $anulacion->status }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($anulacion->motivo, 40) }}</td>
                                <td>{{ $anulacion->usuario_creador ?: '-' }}</td>
                                <td>
                                    <a href="{{ url('/admin/anulaciones-up/detalle/' . $anulacion->id) }}" class="btn-small blue btn-action waves-effect" title="Ver Detalle">
                                        <i class="material-icons">visibility</i>
                                    </a>
                                    @if($anulacion->idtran)
                                    <a href="{{ url('/admin/anulaciones-up/xml/' . $anulacion->id) }}" class="btn-small green btn-action waves-effect" title="Ver XML">
                                        <i class="material-icons">code</i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="center grey-text">
                                    <i class="material-icons large">inbox</i><br>
                                    No hay anulaciones registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($anulaciones->hasPages())
                <div class="center" style="margin-top: 20px;">
                    <div class="pagination-wrapper">
                        @if ($anulaciones->onFirstPage())
                            <a class="btn-flat disabled"><i class="material-icons">chevron_left</i></a>
                        @else
                            <a href="{{ $anulaciones->previousPageUrl() }}" class="btn-flat waves-effect"><i class="material-icons">chevron_left</i></a>
                        @endif

                        @foreach ($anulaciones->getUrlRange(1, $anulaciones->lastPage()) as $page => $url)
                            @if ($page == $anulaciones->currentPage())
                                <a class="btn-flat blue white-text">{{ $page }}</a>
                            @else
                                <a href="{{ $url }}" class="btn-flat waves-effect">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($anulaciones->hasMorePages())
                            <a href="{{ $anulaciones->nextPageUrl() }}" class="btn-flat waves-effect"><i class="material-icons">chevron_right</i></a>
                        @else
                            <a class="btn-flat disabled"><i class="material-icons">chevron_right</i></a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('select').formSelect();
        });
    </script>
</body>
</html>
