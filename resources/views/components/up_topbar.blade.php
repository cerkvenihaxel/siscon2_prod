<div class="topbar" id="topbar" style="position: fixed; top: 0; left: 250px; right: 0; height: 60px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 999; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; transition: left 0.3s ease;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <button id="toggleSidebar" class="btn-flat waves-effect" style="padding: 0; min-width: 40px;">
            <i class="material-icons" style="font-size: 28px; color: #667eea;">menu</i>
        </button>
        <div class="welcome-text">
            <span style="font-size: 16px; color: #333;">Bienvenido, <strong>{{ CRUDBooster::myName() ?? 'Usuario' }}</strong></span>
        </div>
    </div>
    <div class="logout-section">
        <a href="{{ CRUDBooster::adminPath('logout') }}" class="btn red waves-effect waves-light">
            <i class="material-icons left">exit_to_app</i>
            Cerrar Sesión
        </a>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#toggleSidebar').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        if ($('#sidebar').hasClass('collapsed')) {
            $('#topbar').css('left', '0');
            $('body').css('margin-left', '0');
        } else {
            $('#topbar').css('left', '250px');
            $('body').css('margin-left', '250px');
        }
    });
});
</script>
