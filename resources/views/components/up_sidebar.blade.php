<div class="sidebar" id="sidebar" style="position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: 1000; overflow-y: auto; transition: transform 0.3s ease;">
    <div class="sidebar-header" style="padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2);">
        <h5 class="white-text no-margin">
            <i class="material-icons left">medical_services</i>
            Sistema UP
        </h5>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0;">
        <ul class="collection" style="border: none; margin: 0;">
            <li class="collection-item {{ request()->is('admin/transaccion-ap*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="/admin/transaccion-ap" class="white-text" style="display: block; padding: 15px 20px; text-decoration: none;">
                    <i class="material-icons left">add_circle</i>
                    Transacción AP
                </a>
            </li>
            <li class="collection-item {{ request()->is('admin/consumos-up*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="/admin/consumos-up" class="white-text" style="display: block; padding: 15px 20px; text-decoration: none;">
                    <i class="material-icons left">list</i>
                    Consumos UP
                </a>
            </li>
            <li class="collection-item {{ request()->is('admin/entregas-up*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="/admin/entregas-up" class="white-text" style="display: block; padding: 15px 20px; text-decoration: none;">
                    <i class="material-icons left">assignment_turned_in</i>
                    Entregas UP
                </a>
            </li>
            <li class="collection-item {{ request()->is('admin/anulacion-up*') || request()->is('admin/up_anulaciones*') ? 'active' : '' }}" style="background: transparent; border: none; padding: 0;">
                <a href="/admin/anulaciones-up" class="white-text" style="display: block; padding: 15px 20px; text-decoration: none;">
                    <i class="material-icons left">cancel</i>
                    Anulaciones UP
                </a>
            </li>
        </ul>
        
        <!-- Selector de Ambiente - Solo Super Admin -->
        @if(CRUDBooster::isSuperadmin())
        <div style="padding: 20px; border-top: 1px solid rgba(255,255,255,0.2);">
            <h6 class="white-text" style="margin-bottom: 10px;">
                <i class="material-icons left tiny">cloud</i>
                Ambiente
            </h6>
            <div class="input-field">
                <select id="ambienteSelector" class="white-text" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3);">
                    <option value="test">Test</option>
                    <option value="produccion">Producción</option>
                </select>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar select
    $('select').formSelect();
    
    // Cargar ambiente actual
    $.get('/admin/up_ambiente/ambiente-actual', function(response) {
        $('#ambienteSelector').val(response.ambiente);
        $('select').formSelect();
    });
    
    // Cambiar ambiente
    $('#ambienteSelector').on('change', function() {
        const nuevoAmbiente = $(this).val();
        
        $.post('/admin/up_ambiente/cambiar-ambiente', {
            ambiente: nuevoAmbiente,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                M.toast({html: response.message, classes: 'green'});
                // Refrescar la página después de cambiar ambiente
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                M.toast({html: response.message, classes: 'red'});
            }
        })
        .fail(function() {
            M.toast({html: 'Error al cambiar ambiente', classes: 'red'});
        });
    });
});
</script>

<style>
.sidebar .collection-item.active {
    background: rgba(255,255,255,0.2) !important;
}
.sidebar .collection-item:hover {
    background: rgba(255,255,255,0.1) !important;
}
.sidebar.collapsed {
    transform: translateX(-250px);
}
</style>
