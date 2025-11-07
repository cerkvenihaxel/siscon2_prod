<div class="topbar" style="position: fixed; top: 0; left: 250px; right: 0; height: 60px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 999; display: flex; align-items: center; justify-content: space-between; padding: 0 20px;">
    <div class="welcome-text">
        <span style="font-size: 16px; color: #333;">Bienvenido, <strong>{{ CRUDBooster::myName() ?? 'Usuario' }}</strong></span>
    </div>
    <div class="logout-section">
        <a href="{{ CRUDBooster::adminPath('logout') }}" class="btn red waves-effect waves-light">
            <i class="material-icons left">exit_to_app</i>
            Cerrar Sesión
        </a>
    </div>
</div>
