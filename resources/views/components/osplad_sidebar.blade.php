@php
    $base = $cfg['ruta_base'] ?? '/admin/osplad';
    $cont = $contadores ?? ['pendientes' => 0, 'transito' => 0, 'entregas' => 0];
@endphp
<div class="sidebar" id="sidebar" style="position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: 1000; overflow-y: auto; transition: transform 0.3s ease;">
    <div class="sidebar-header" style="padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2);">
        <h5 class="white-text no-margin">
            <i class="material-icons left">medical_services</i>
            {{ $cfg['titulo'] ?? 'OSPLAD' }}
        </h5>
    </div>

    <div class="sidebar-menu" style="padding: 20px 0;">
        <ul class="collection" style="border: none; margin: 0;">
            <li class="collection-item {{ request()->is('admin/osplad/pendientes*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="{{ $base }}/pendientes" class="white-text" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; text-decoration: none;">
                    <span><i class="material-icons left">schedule</i> PENDIENTES</span>
                    <span class="new badge orange" data-badge-caption="">{{ $cont['pendientes'] }}</span>
                </a>
            </li>
            <li class="collection-item {{ request()->is('admin/osplad/transito*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="{{ $base }}/transito" class="white-text" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; text-decoration: none;">
                    <span><i class="material-icons left">local_shipping</i> En tránsito</span>
                    <span class="new badge blue" data-badge-caption="">{{ $cont['transito'] }}</span>
                </a>
            </li>
            <li class="collection-item {{ request()->is('admin/osplad/entregas*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="{{ $base }}/entregas" class="white-text" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; text-decoration: none;">
                    <span><i class="material-icons left">assignment_turned_in</i> Entregas</span>
                    <span class="new badge green" data-badge-caption="">{{ $cont['entregas'] }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
.sidebar .collection-item.active { background: rgba(255,255,255,0.2) !important; }
.sidebar .collection-item:hover  { background: rgba(255,255,255,0.1) !important; }
.sidebar.collapsed { transform: translateX(-250px); }
.sidebar .new.badge { float: none; margin-left: 8px; }
</style>
