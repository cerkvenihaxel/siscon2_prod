<?php namespace App\Http\Controllers;

use App\Models\PedidoOxigenoterapia;
use App\Models\PedidoOxigenoterapiaMaterial;
use App\Models\PrestamoOxigenoterapia;
use App\Models\DocumentosPrestamo;
use App\Models\EquiposOxigenoterapia;
use App\Models\EstadoOxigenoterapia;
use App\Models\Afiliados;
use App\Models\Medicos;
use App\Models\Clinicas;
use Session;
use DB;
use CRUDBooster;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminOxigenoterapiaController extends \crocodicstudio\crudbooster\controllers\CBController {

    function countSolicitudes($estado_id){
        $total = DB::table('pedido_oxigenoterapia')->where('estado_oxigenoterapia_id', $estado_id)->count();
        return $total;
    }

    function countSolicitudesTotal(){
        $total = DB::table('pedido_oxigenoterapia')->count();
        return $total;
    }

    function countPrestamosActivos(){
        $total = DB::table('prestamo_oxigenoterapia')->where('estado_prestamo', 'ACTIVO')->count();
        return $total;
    }

    function countPrestamosVencidos(){
        $total = DB::table('prestamo_oxigenoterapia')
                    ->where('estado_prestamo', 'ACTIVO')
                    ->where('fecha_fin_prestamo', '<', date('Y-m-d'))
                    ->count();
        return $total;
    }

    function getState($q){
        switch($q) {
            case 'PENDIENTE':
                return 1;
            case 'AUTORIZADO':
                return 2;
            case 'EN_PRESTAMO':
                return 3;
            case 'RENOVADO':
                return 4;
            case 'FINALIZADO':
                return 5;
            case 'RECHAZADO':
                return 6;
            default:
                return 0;
        }
    }

    function returnState($value){
        if ($value != 0) {
            return "<h4 style='text-align: left; padding-left: 1rem;'> Solicitudes : ". $_GET['q'] ."   - Cantidad de resultados: " . DB::table('pedido_oxigenoterapia')->where('estado_oxigenoterapia_id', $this->getState($_GET['q']))->count() . "</h4>";
        } else {
            return "<h4 style='text-align: left; padding-left: 1rem;'> Solicitudes : VER TODAS   - Cantidad de resultados: " . DB::table('pedido_oxigenoterapia')->count() . "</h4>";
        }
    }

    private function generateSolicitudNumber()
    {
        $today = date('Ymd');
        $prefix = 'OXI-' . $today . '-';
        
        // Buscar el último número de solicitud del día actual
        $lastSolicitud = PedidoOxigenoterapia::where('nro_solicitud', 'like', $prefix . '%')
            ->orderBy('nro_solicitud', 'desc')
            ->first();

        if ($lastSolicitud) {
            // Extraer el número del último registro del día
            $lastNumber = (int)substr($lastSolicitud->nro_solicitud, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Si no hay registros del día, empezar con 0001
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    private function generatePrestamoNumber()
    {
        $today = date('Ymd');
        $prefix = 'PREST-' . $today . '-';
        
        // Buscar el último número de préstamo del día actual
        $lastPrestamo = PrestamoOxigenoterapia::where('nro_prestamo', 'like', $prefix . '%')
            ->orderBy('nro_prestamo', 'desc')
            ->first();

        if ($lastPrestamo) {
            // Extraer el número del último registro del día
            $lastNumber = (int)substr($lastPrestamo->nro_prestamo, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Si no hay registros del día, empezar con 0001
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public function cbInit() {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "nro_solicitud";
        $this->limit = "20";
        $this->orderby = "id,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = true;
        $this->button_action_style = "button_icon";
        $this->button_add = true;
        $this->button_edit = true;
        $this->button_delete = true;
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "pedido_oxigenoterapia";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label"=>"Fecha de creación","name"=>"created_at"];
        $this->col[] = ["label"=>"Nro Solicitud","name"=>"nro_solicitud"];
        $this->col[] = ["label"=>"Nombre Afiliado","name"=>"nombre_apellido"];
        $this->col[] = ["label"=>"Nro Afiliado","name"=>"nro_afiliado"];
        $this->col[] = ["label"=>"Estado", "name"=>"estado_oxigenoterapia_id", "join"=>"estado_oxigenoterapia,estado"];
        $this->col[] = ["label"=>"Fecha Prescripción","name"=>"fecha_prescripcion"];
        $this->col[] = ["label"=>"Fecha Vencimiento","name"=>"fecha_vencimiento"];
        $this->col[] = ["label"=>"Médico", "name"=>"medicos_id", "join"=>"medicos,nombremedico"];
        $this->col[] = ["label"=>"Clínica", "name"=>"clinicas_id", "join"=>"clinicas,nombre"];
        $this->col[] = ["label"=>"Materiales", "name"=>"id", "callback"=>function($row) {
            $count = DB::table('pedido_oxigenoterapia_materiales')->where('pedido_oxigenoterapia_id', $row->id)->count();
            return $count > 0 ? $count . ' item(s)' : 'Sin materiales';
        }];
        # END COLUMNS DO NOT REMOVE THIS LINE

        $myEmail = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ['label'=>'Nro Solicitud','name'=>'nro_solicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$this->generateSolicitudNumber(), 'readonly'=>'true'];
        $this->form[] = ['label' => 'Nombre y Apellido Afiliado', 'name' => 'afiliados_id', 'type' => 'datamodal', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datamodal_table' => 'afiliados', 'datamodal_columns' => 'apeynombres,nroAfiliado,documento,sexo,localidad', 'datamodal_select_to' => 'nombre_apellido:apeynombres,nro_afiliado:nroAfiliado,documento:documento', 'datamodal_size' => 'large'];
        $this->form[] = ['label'=>'Nombre y Apellido','name'=>'nombre_apellido','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true];
        $this->form[] = ['label'=>'Nro de Afiliado','name'=>'nro_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true];
        $this->form[] = ['label'=>'Documento','name'=>'documento','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true];
        $this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Clínica','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre'];
        $this->form[] = ['label'=>'Médico','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico'];
        $this->form[] = ['label'=>'Zona Residencia','name'=>'zona_residencia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'dataenum'=>'Norte;Sur;Este;Oeste;Centro;Interior'];
        $this->form[] = ['label'=>'Tel Afiliado','name'=>'tel_afiliado','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Email','name'=>'email','type'=>'email','validation'=>'min:1|max:255|email','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Fecha Prescripción','name'=>'fecha_prescripcion','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>date('Y-m-d')];
        $this->form[] = ['label'=>'Fecha Vencimiento','name'=>'fecha_vencimiento','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>date('Y-m-d', strtotime('+30 days'))];
        $this->form[] = ['label'=>'Tel Médico','name'=>'tel_medico','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:1000','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Archivo Prescripción','name'=>'archivo_prescripcion','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Archivo Estudios','name'=>'archivo_estudios','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Otros Archivos','name'=>'archivo_otros','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];

        // MATERIALES/EQUIPOS
        $columns = [];
        $columns[] = ['label'=>'Equipo','name'=>'equipos_oxigenoterapia_id','type'=>'datamodal','validation'=>'required|integer','width'=>'col-sm-10','datamodal_table'=>'equipos_oxigenoterapia','datamodal_columns'=>'codigo_equipo,nombre_completo,marca,modelo,tipo_equipo,estado_equipo,descripcion','datamodal_select_to'=>'nombre_equipo:nombre_equipo,codigo_equipo:codigo_equipo','datamodal_where'=>'estado_equipo = \'DISPONIBLE\'','datamodal_size'=>'large'];
        $columns[] = ['label'=>'Nombre Equipo','name'=>'nombre_equipo','type'=>'text','width'=>'col-sm-10','readonly'=>true];
        $columns[] = ['label'=>'Código Equipo','name'=>'codigo_equipo','type'=>'text','width'=>'col-sm-10','readonly'=>true];
        $columns[] = ['label'=>'Cantidad','name'=>'cantidad','type'=>'number','validation'=>'required|integer|min:1','width'=>'col-sm-10'];
        $columns[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'max:500','width'=>'col-sm-10'];

        $this->form[] = ['label'=>'Materiales/Equipos Solicitados','name'=>'pedido_oxigenoterapia_materiales','type'=>'child','columns'=>$columns,'table'=>'pedido_oxigenoterapia_materiales','foreign_key'=>'pedido_oxigenoterapia_id', 'required' => true];

        //HIDDEN
        $this->form[] = ['label'=>'Estado', 'name'=>'estado_oxigenoterapia_id', 'type'=>'hidden', 'value'=>1];
        $this->form[] = ['label'=>'Stamp User', 'name'=>'stamp_user', 'type'=>'hidden', 'value'=>$myEmail];
        $this->form[] = ['label'=>'Nombre Apellido', 'name'=>'nombre_apellido', 'type'=>'hidden'];
        # END FORM DO NOT REMOVE THIS LINE

        /*
        | ----------------------------------------------------------------------
        | Sub Module
        | ----------------------------------------------------------------------
        | @label          = Label of action
        | @path           = Path of sub module
        | @foreign_key 	  = foreign key of sub table/module
        | @button_color   = Bootstrap Class (primary,success,warning,danger)
        | @button_icon    = Font Awesome Class
        | @parent_columns = Sparate with comma, e.g : name,created_at
        |
        */
        $this->sub_module = array();

        /*
        | ----------------------------------------------------------------------
        | Add More Action Button / Menu
        | ----------------------------------------------------------------------
        | @label       = Label of action
        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
        | @icon        = Font awesome class icon. e.g : fa fa-bars
        | @color 	   = Default is primary. (primary, warning, succecss, info)
        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
        |
        */
        $this->addaction = array();
        $this->addaction[] = ['label'=>'Ver Materiales', 'url'=>'/admin/oxigenoterapia/materiales/[id]','button_color'=>'info','button_icon'=>'fa fa-list', 'showIf'=>'[estado_oxigenoterapia_id] >= 1', 'target'=>'_blank'];
        $this->addaction[] = ['label'=>'Autorizar', 'url'=>'/admin/oxigenoterapia/autorizar/[id]','button_color'=>'success','button_icon'=>'fa fa-check', 'showIf'=>'[estado_oxigenoterapia_id] == 1', 'confirmation'=>true];
        $this->addaction[] = ['label'=>'Realizar Préstamo', 'url'=>'/admin/prestamo_oxigenoterapia/add?pedido_id=[id]','button_color'=>'primary','button_icon'=>'fa fa-handshake-o', 'showIf'=>'[estado_oxigenoterapia_id] == 2'];
        $this->addaction[] = ['label'=>'Ver Préstamo', 'url'=>'/admin/prestamo-oxigenoterapia/ver-prestamo/[id]','button_color'=>'info','button_icon'=>'fa fa-eye', 'showIf'=>'[estado_oxigenoterapia_id] >= 3'];
        $this->addaction[] = ['label'=>'Renovar', 'url'=>'/admin/oxigenoterapia/renovar/[id]','button_color'=>'warning','button_icon'=>'fa fa-refresh', 'showIf'=>'[estado_oxigenoterapia_id] == 3', 'confirmation'=>true];
        $this->addaction[] = ['label'=>'Finalizar', 'url'=>'/admin/oxigenoterapia/finalizar/[id]','button_color'=>'danger','button_icon'=>'fa fa-stop', 'showIf'=>'[estado_oxigenoterapia_id] == 3', 'confirmation'=>true];
        $this->addaction[] = ['label'=>'Imprimir Documentos', 'url'=>'/admin/oxigenoterapia/imprimir/[id]','button_color'=>'warning','button_icon'=>'fa fa-print', 'target'=>'_blank'];

        /*
        | ----------------------------------------------------------------------
        | Add More Button Selected
        | ----------------------------------------------------------------------
        | @label       = Label of action
        | @icon 	   = Icon from fontawesome
        | @name 	   = Name of button
        | Then about the action, you should code at actionButtonSelected method
        |
        */
        $this->button_selected = array();

        /*
        | ----------------------------------------------------------------------
        | Add alert message to this module at overheader
        | ----------------------------------------------------------------------
        | @message = Text of message
        | @type    = warning,success,danger,info
        |
        */
        $this->alert = array();

        /*
        | ----------------------------------------------------------------------
        | Add more button to header button
        | ----------------------------------------------------------------------
        | @label = Name of button
        | @url   = URL Target
        | @icon  = Icon from Awesome.
        |
        */
        $this->index_button = array();

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        | @condition = If condition. You may use field alias. E.g : [id] == 1
        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
        |
        */
        $this->table_row_color = array();

        /*
        | ----------------------------------------------------------------------
        | You may use this bellow array to add statistic at dashboard
        | ----------------------------------------------------------------------
        | @label, @count, @icon, @color
        |
        */
        $this->index_statistic = array();
        $this->index_statistic[] = ['label'=>'Total Solicitudes', 'count'=>$this->countSolicitudesTotal(), 'icon'=>'fa fa-inbox', 'color'=>'green'];
        $this->index_statistic[] = ['label'=>'Pendientes', 'count'=>$this->countSolicitudes(1), 'icon'=>'fa fa-clock-o', 'color'=>'yellow'];
        $this->index_statistic[] = ['label'=>'Autorizadas', 'count'=>$this->countSolicitudes(2), 'icon'=>'fa fa-check', 'color'=>'blue'];
        $this->index_statistic[] = ['label'=>'En Préstamo', 'count'=>$this->countSolicitudes(3), 'icon'=>'fa fa-handshake-o', 'color'=>'green'];
        $this->index_statistic[] = ['label'=>'Préstamos Activos', 'count'=>$this->countPrestamosActivos(), 'icon'=>'fa fa-users', 'color'=>'primary'];
        $this->index_statistic[] = ['label'=>'Préstamos Vencidos', 'count'=>$this->countPrestamosVencidos(), 'icon'=>'fa fa-exclamation-triangle', 'color'=>'red'];

        /*
        | ----------------------------------------------------------------------
        | Add javascript at body
        | ----------------------------------------------------------------------
        | javascript code in the variable
        | $this->script_js = "function() { ... }";
        |
        */
        $this->script_js = "
            $(document).ready(function() {
                console.log('Script de oxigenoterapia cargado');
                
                // Función para cargar datos adicionales del afiliado (edad, teléfono, email, zona)
                function cargarDatosAdicionalesAfiliado(afiliadoId) {
                    console.log('Cargando datos adicionales del afiliado ID:', afiliadoId);
                    
                    if(afiliadoId && afiliadoId !== '') {
                        $.ajax({
                            url: '/admin/oxigenoterapia/get-afiliado/' + afiliadoId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Datos adicionales del afiliado recibidos:', data);
                                
                                // Solo actualizar campos que no se llenan automáticamente con datamodal
                                $('input[name=\"edad\"]').val(data.edad || '');
                                $('select[name=\"zona_residencia\"]').val(data.zona_residencia || 'Centro');
                                $('input[name=\"tel_afiliado\"]').val(data.telefonos || '');
                                $('input[name=\"email\"]').val(data.email || '');
                                
                                console.log('Campos adicionales actualizados correctamente');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al cargar datos adicionales del afiliado:', error);
                            }
                        });
                    }
                }

                // Auto-completar datos adicionales del afiliado cuando se selecciona desde el datamodal
                $(document).on('change', '#afiliados_id', function() {
                    var afiliadoId = $(this).val();
                    console.log('Evento change en afiliados_id:', afiliadoId);
                    cargarDatosAdicionalesAfiliado(afiliadoId);
                });

                // Auto-completar datos del médico
                $(document).on('change', '#medicos_id', function() {
                    var medicoId = $(this).val();
                    console.log('Médico seleccionado:', medicoId);
                    
                    if(medicoId) {
                        $.ajax({
                            url: '/admin/oxigenoterapia/get-medico/' + medicoId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Datos del médico recibidos:', data);
                                $('input[name=\"tel_medico\"]').val(data.telefono || '');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al cargar datos del médico:', error);
                            }
                        });
                    } else {
                        $('input[name=\"tel_medico\"]').val('');
                    }
                });

                // Verificar si ya hay un afiliado seleccionado al cargar la página
                setTimeout(function() {
                    var afiliadoId = $('#afiliados_id').val();
                    if(afiliadoId) {
                        console.log('Afiliado ya seleccionado al cargar:', afiliadoId);
                        cargarDatosAdicionalesAfiliado(afiliadoId);
                    }
                }, 1000);

                // Función para llenar automáticamente el nombre del equipo en el child form
                function llenarNombreEquipo(equipoId) {
                    console.log('Llenando nombre del equipo ID:', equipoId);
                    
                    if(equipoId && equipoId !== '') {
                        $.ajax({
                            url: '/admin/oxigenoterapia/get-equipo/' + equipoId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Datos del equipo recibidos:', data);
                                
                                // Buscar el campo nombre_equipo en el child form actual
                                var nombreEquipoField = $('input[name*=\"nombre_equipo\"]').last();
                                console.log('Campo nombre_equipo encontrado:', nombreEquipoField.length);
                                
                                if(nombreEquipoField.length > 0) {
                                    nombreEquipoField.val(data.nombre_completo || '');
                                    console.log('Campo nombre_equipo actualizado:', data.nombre_completo);
                                } else {
                                    console.log('No se encontró el campo nombre_equipo');
                                    // Intentar con un selector más específico
                                    var alternativeField = $('input[name*=\"nombre_equipo\"]');
                                    console.log('Campos nombre_equipo alternativos:', alternativeField.length);
                                    if(alternativeField.length > 0) {
                                        alternativeField.val(data.nombre_completo || '');
                                        console.log('Campo nombre_equipo actualizado (alternativo):', data.nombre_completo);
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al cargar datos del equipo:', error);
                            }
                        });
                    }
                }

                // Evento para detectar cuando se selecciona un equipo en el child form
                $(document).on('change', 'select[name*=\"equipos_oxigenoterapia_id\"]', function() {
                    var equipoId = $(this).val();
                    console.log('Equipo seleccionado en child form:', equipoId);
                    llenarNombreEquipo(equipoId);
                });

                // Evento para detectar cuando se selecciona un equipo en el datamodal
                $(document).on('datamodal-selected', function(e, data) {
                    console.log('Datamodal seleccionado:', data);
                    
                    // Verificar si es un datamodal de equipos
                    if(data && data.equipos_oxigenoterapia_id) {
                        console.log('Equipo seleccionado desde datamodal:', data.equipos_oxigenoterapia_id);
                        llenarNombreEquipo(data.equipos_oxigenoterapia_id);
                    }
                });

                // Evento adicional para detectar cambios en el datamodal
                $(document).on('change', 'input[name*=\"equipos_oxigenoterapia_id\"]', function() {
                    var equipoId = $(this).val();
                    console.log('Equipo seleccionado (input change):', equipoId);
                    if(equipoId) {
                        setTimeout(function() {
                            llenarNombreEquipo(equipoId);
                        }, 500);
                    }
                });

                // Evento para detectar cuando se completa la selección del datamodal
                $(document).on('click', '.datamodal-select', function() {
                    console.log('Datamodal select clicked');
                    setTimeout(function() {
                        var equipoId = $('input[name*=\"equipos_oxigenoterapia_id\"]').last().val();
                        console.log('Equipo ID después del click:', equipoId);
                        if(equipoId) {
                            llenarNombreEquipo(equipoId);
                        }
                    }, 1000);
                });
            });
        ";

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code before index table
        | ----------------------------------------------------------------------
        | html code to display it before index table
        | $this->pre_index_html = "<p>test</p>";
        |
        */
        $this->pre_index_html = "<div class='row'>
        <div class='col-md-12'>
        <div class='panel panel-default'>
        <div class='panel-heading'>
        <div class='panel-title'><i class='fa fa-search'></i> Filtros rápidos</div>
        </div>
        <div class='panel-body'>
        <div class='row'>
        <div class='col-md-12'>
        <div class='fc-button-group'>
        <button type='button' class='btn' style='background-color: lightcoral !important; color: white !important;' onclick='window.location.href = \"?q=\"'>VER TODAS (".$this->countSolicitudesTotal() .")</button>
        <button type='button' class='btn' style='background-color: gold !important;' onclick='window.location.href = \"?q=PENDIENTE\"'>Pendientes (". $this->countSolicitudes(1) .")</button>
        <button type='button' class='btn' style='background-color: #0d6aad !important; color: white !important;' onclick='window.location.href = \"?q=AUTORIZADO\"'>Autorizadas (". $this->countSolicitudes(2) .")</button>
        <button type='button' class='btn' style='background-color: green !important; color: white !important;' onclick='window.location.href = \"?q=EN_PRESTAMO\"'>En Préstamo (". $this->countSolicitudes(3) .")</button>
        <button type='button' class='btn' style='background-color: orange !important; color: white !important;' onclick='window.location.href = \"?q=RENOVADO\"'>Renovados (". $this->countSolicitudes(4) .")</button>
        <button type='button' class='btn' style='background-color: red !important; color: white !important;' onclick='window.location.href = \"?q=RECHAZADO\"'>Rechazadas (". $this->countSolicitudes(6) .")</button>
        <hr>
        </div>
        </div>
        </div>
       <div class='row'>
       ". $this->returnState($this->getState($_GET['q'] ?? '')) ."
       </div>
        ";

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code after index table
        | ----------------------------------------------------------------------
        | html code to display it after index table
        | $this->post_index_html = "<p>test</p>";
        |
        */
        $this->post_index_html = null;

        /*
        | ----------------------------------------------------------------------
        | Include Javascript File
        | ----------------------------------------------------------------------
        | URL of your javascript each array
        | $this->load_js[] = asset("myfile.js");
        |
        */
        $this->load_js = array();

        /*
        | ----------------------------------------------------------------------
        | Add css style at body
        | ----------------------------------------------------------------------
        | css code in the variable
        | $this->style_css = ".style{....}";
        |
        */
        $this->style_css = NULL;

        /*
        | ----------------------------------------------------------------------
        | Include css File
        | ----------------------------------------------------------------------
        | URL of your css each array
        | $this->load_css[] = asset("myfile.css");
        |
        */
        $this->load_css = array();
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for button selected
    | ----------------------------------------------------------------------
    | @id_selected = the id selected
    | @button_name = the name of button
    |
    */
    public function actionButtonSelected($id_selected,$button_name) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate query of index result
    | ----------------------------------------------------------------------
    | @query = current sql query
    |
    */
    public function hook_query_index(&$query) {
        if(isset($_GET['q']) && $_GET['q'] != '') {
            $estado_id = $this->getState($_GET['q']);
            if($estado_id != 0) {
                $query->where('estado_oxigenoterapia_id', $estado_id);
            }
        }
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate row of index table html
    | ----------------------------------------------------------------------
    |
    */
    public function hook_row_index($column_index,&$column_value) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before add data is execute
    | ----------------------------------------------------------------------
    | @arr
    |
    */
    public function hook_before_add(&$postdata) {
        // Asegurar que el nombre_apellido se llene correctamente
        if (isset($postdata['afiliados_id']) && !empty($postdata['afiliados_id'])) {
            $afiliado = Afiliados::find($postdata['afiliados_id']);
            if ($afiliado) {
                $postdata['nombre_apellido'] = $afiliado->apeynombres;
                $postdata['nro_afiliado'] = $afiliado->nroAfiliado;
                $postdata['documento'] = $afiliado->documento;
            }
        }
        
        // Llenar automáticamente el nombre del equipo si se está agregando un material
        if (isset($postdata['equipos_oxigenoterapia_id']) && !empty($postdata['equipos_oxigenoterapia_id'])) {
            $equipo = EquiposOxigenoterapia::find($postdata['equipos_oxigenoterapia_id']);
            if ($equipo) {
                $postdata['nombre_equipo'] = $equipo->nombre_completo;
            }
        }
        
        // Validar y procesar fechas
        if (isset($postdata['fecha_prescripcion']) && !empty($postdata['fecha_prescripcion'])) {
            // Asegurar formato correcto para MySQL
            $fecha_prescripcion = \Carbon\Carbon::parse($postdata['fecha_prescripcion'])->format('Y-m-d');
            $postdata['fecha_prescripcion'] = $fecha_prescripcion;
        } else {
            // Si no se proporciona fecha de prescripción, usar fecha actual
            $postdata['fecha_prescripcion'] = date('Y-m-d');
        }
        
        if (isset($postdata['fecha_vencimiento']) && !empty($postdata['fecha_vencimiento'])) {
            // Asegurar formato correcto para MySQL
            $fecha_vencimiento = \Carbon\Carbon::parse($postdata['fecha_vencimiento'])->format('Y-m-d');
            $postdata['fecha_vencimiento'] = $fecha_vencimiento;
        } else {
            // Si no se proporciona fecha de vencimiento, usar fecha actual + 30 días
            $postdata['fecha_vencimiento'] = date('Y-m-d', strtotime('+30 days'));
        }
        
        // Asegurar que el stamp_user esté presente
        if (!isset($postdata['stamp_user']) || empty($postdata['stamp_user'])) {
            $postdata['stamp_user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
        }
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after add public static function called
    | ----------------------------------------------------------------------
    | @id = last insert id
    |
    */
    public function hook_after_add($id) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before update data is execute
    | ----------------------------------------------------------------------
    | @postdata = input post data
    | @id       = current id
    |
    */
    public function hook_before_edit(&$postdata,$id) {
        // Asegurar que el nombre_apellido se llene correctamente
        if (isset($postdata['afiliados_id']) && !empty($postdata['afiliados_id'])) {
            $afiliado = Afiliados::find($postdata['afiliados_id']);
            if ($afiliado) {
                $postdata['nombre_apellido'] = $afiliado->apeynombres;
                $postdata['nro_afiliado'] = $afiliado->nroAfiliado;
                $postdata['documento'] = $afiliado->documento;
            }
        }
        
        // Llenar automáticamente el nombre del equipo si se está editando un material
        if (isset($postdata['equipos_oxigenoterapia_id']) && !empty($postdata['equipos_oxigenoterapia_id'])) {
            $equipo = EquiposOxigenoterapia::find($postdata['equipos_oxigenoterapia_id']);
            if ($equipo) {
                $postdata['nombre_equipo'] = $equipo->nombre_completo;
            }
        }
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after edit public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hook_after_edit($id) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command before delete public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hook_before_delete($id) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after delete public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hook_after_delete($id) {
        //Your code here
    }

    // Métodos adicionales para el flujo de oxigenoterapia
    public function getAfiliado($id) {
        $afiliado = Afiliados::find($id);
        
        if ($afiliado) {
            // Usar el método getDatosCompletos del modelo
            $data = $afiliado->getDatosCompletos();
            
            return response()->json($data);
        }
        
        return response()->json(['error' => 'Afiliado no encontrado'], 404);
    }

    public function getMedico($id) {
        $medico = Medicos::find($id);
        
        if ($medico) {
            $data = [
                'nombremedico' => $medico->nombremedico,
                'telefono' => $medico->telefono,
                'email' => $medico->email,
                'especialidad' => $medico->especialidad
            ];
            
            return response()->json($data);
        }
        
        return response()->json(['error' => 'Médico no encontrado'], 404);
    }

    public function getEquipo($id) {
        $equipo = EquiposOxigenoterapia::find($id);
        
        if ($equipo) {
            $data = [
                'id' => $equipo->id,
                'codigo_equipo' => $equipo->codigo_equipo,
                'nombre_equipo' => $equipo->nombre_equipo,
                'nombre_completo' => $equipo->nombre_completo,
                'marca' => $equipo->marca,
                'modelo' => $equipo->modelo,
                'tipo_equipo' => $equipo->tipo_equipo,
                'estado_equipo' => $equipo->estado_equipo,
                'descripcion' => $equipo->descripcion
            ];
            
            return response()->json($data);
        }
        
        return response()->json(['error' => 'Equipo no encontrado'], 404);
    }

    public function autorizar($id) {
        \Log::info('Iniciando autorización para pedido ID: ' . $id);
        
        try {
            $pedido = PedidoOxigenoterapia::with('materiales')->find($id);
            
            if (!$pedido) {
                \Log::warning('Pedido no encontrado con ID: ' . $id);
                return redirect('/admin/pedido_oxigenoterapia')->with('error', 'Pedido no encontrado con ID: ' . $id);
            }
            
            \Log::info('Pedido encontrado: ' . $pedido->nro_solicitud . ' - Estado: ' . $pedido->estado_oxigenoterapia_id);
            
            // Verificar que el pedido esté en estado pendiente
            if ($pedido->estado_oxigenoterapia_id != 1) {
                \Log::warning('Intento de autorizar pedido en estado incorrecto: ' . $pedido->estado_oxigenoterapia_id);
                return redirect('/admin/pedido_oxigenoterapia')->with('error', 'Solo se pueden autorizar pedidos en estado PENDIENTE');
            }
            
            // Verificar que el pedido tenga materiales
            if ($pedido->materiales->count() == 0) {
                \Log::warning('Intento de autorizar pedido sin materiales: ' . $pedido->id);
                return redirect('/admin/pedido_oxigenoterapia')->with('error', 'No se puede autorizar un pedido sin materiales/equipos seleccionados');
            }
            
            \Log::info('Materiales encontrados: ' . $pedido->materiales->count());
            
            // Verificar que el usuario tenga permisos de Auditor Convenio
            $privilege = CRUDBooster::myPrivilegeId();
            \Log::info('Privilegio del usuario: ' . $privilege);
            
            if ($privilege != 1 && $privilege != 2) { // Ajustar según los IDs de privilegios de Auditor Convenio
                \Log::warning('Usuario sin permisos para autorizar: ' . $privilege);
                return redirect('/admin/pedido_oxigenoterapia')->with('error', 'No tienes permisos para autorizar pedidos');
            }
            
            $pedido->estado_oxigenoterapia_id = 2; // AUTORIZADO
            $pedido->save();

            \Log::info('Pedido autorizado exitosamente: ' . $pedido->nro_solicitud);
            return redirect('/admin/pedido_oxigenoterapia')->with('message', 'Solicitud autorizada correctamente por Auditor Convenio');
            
        } catch (\Exception $e) {
            \Log::error('Error al autorizar pedido: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect('/admin/pedido_oxigenoterapia')->with('error', 'Error al autorizar la solicitud: ' . $e->getMessage());
        }
    }

    public function prestamo($id) {
        $pedido = PedidoOxigenoterapia::with(['afiliado', 'medicos', 'clinica', 'materiales.equipo'])->findOrFail($id);
        
        // Verificar que el pedido esté autorizado
        if ($pedido->estado_oxigenoterapia_id != 2) {
            return redirect('/admin/pedido_oxigenoterapia')->with('error', 'Solo se pueden realizar préstamos de pedidos autorizados');
        }
        
        // Verificar que el usuario tenga permisos de Oficina Convenio
        $privilege = CRUDBooster::myPrivilegeId();
        if ($privilege != 1 && $privilege != 3) { // Ajustar según los IDs de privilegios de Oficina Convenio
            return redirect('/admin/pedido_oxigenoterapia')->with('error', 'No tienes permisos para realizar préstamos');
        }
        
        $equipos = EquiposOxigenoterapia::where('estado_equipo', 'DISPONIBLE')->get();
        
        return view('oxigenoterapia.prestamo', compact('pedido', 'equipos'));
    }

    public function verPrestamo($id) {
        $pedido = PedidoOxigenoterapia::with(['prestamoActivo.documentos', 'prestamoActivo.equipo'])->findOrFail($id);
        
        return view('oxigenoterapia.ver-prestamo', compact('pedido'));
    }

    public function renovar($id) {
        $pedido = PedidoOxigenoterapia::with(['prestamoActivo'])->findOrFail($id);
        
        return view('oxigenoterapia.renovar', compact('pedido'));
    }

    public function finalizar($id) {
        $pedido = PedidoOxigenoterapia::with(['prestamoActivo'])->findOrFail($id);
        
        return view('oxigenoterapia.finalizar', compact('pedido'));
    }

    public function materiales($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        $materiales = $pedido->materiales;
        return view('oxigenoterapia.materiales-popup', compact('pedido', 'materiales'));
    }

    public function realizarPrestamo($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        $materiales = $pedido->materiales;
        $nro_prestamo = $this->generatePrestamoNumber();
        return view('oxigenoterapia.realizar-prestamo', compact('pedido', 'materiales', 'nro_prestamo'));
    }

    public function storePrestamo(Request $request) {
        $validator = Validator::make($request->all(), [
            'pedido_id' => 'required|exists:pedido_oxigenoterapia,id',
            'nro_prestamo' => 'required|string|unique:prestamo_oxigenoterapia,nro_prestamo',
            'fecha_inicio' => 'required|date',
            'duracion_dias' => 'required|integer|min:1|max:365',
            'direccion_entrega' => 'required|string',
            'materiales' => 'required|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Crear el préstamo
            $prestamo = new PrestamoOxigenoterapia();
            $prestamo->nro_prestamo = $request->nro_prestamo;
            $prestamo->pedido_oxigenoterapia_id = $request->pedido_id;
            $prestamo->fecha_inicio = $request->fecha_inicio;
            $prestamo->fecha_fin = date('Y-m-d', strtotime($request->fecha_inicio . ' + ' . $request->duracion_dias . ' days'));
            $prestamo->duracion_dias = $request->duracion_dias;
            $prestamo->direccion_entrega = $request->direccion_entrega;
            $prestamo->observaciones = $request->observaciones_prestamo;
            $prestamo->estado_prestamo = 'ACTIVO';
            $prestamo->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $prestamo->save();

            // Procesar materiales del checklist
            foreach ($request->materiales as $materialId => $data) {
                $material = PedidoOxigenoterapiaMaterial::find($materialId);
                if ($material) {
                    $material->entregable = $data['entregable'];
                    $material->observaciones_entrega = $data['observaciones'] ?? null;
                    $material->save();
                }
            }

            // Actualizar estado del pedido
            $pedido = PedidoOxigenoterapia::find($request->pedido_id);
            $pedido->estado_oxigenoterapia_id = 3; // EN_PRESTAMO
            $pedido->save();

            DB::commit();

            return redirect('/admin/pedido_oxigenoterapia')
                           ->with('message', 'Préstamo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear el préstamo: ' . $e->getMessage());
        }
    }

    public function imprimirDocumentos($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        return view('oxigenoterapia.imprimir-documentos', compact('pedido'));
    }

    // Métodos para generar PDFs
    public function generarConsentimiento($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.consentimiento-oxigenoterapia', compact('pedido'));
        return $pdf->download('consentimiento_' . $pedido->nro_solicitud . '.pdf');
    }

    public function generarTerminos($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.terminos-oxigenoterapia', compact('pedido'));
        return $pdf->download('terminos_' . $pedido->nro_solicitud . '.pdf');
    }

    public function generarContrato($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.contrato-oxigenoterapia', compact('pedido'));
        return $pdf->download('contrato_' . $pedido->nro_solicitud . '.pdf');
    }

    public function generarInstrucciones($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.instrucciones-oxigenoterapia', compact('pedido'));
        return $pdf->download('instrucciones_' . $pedido->nro_solicitud . '.pdf');
    }

    public function generarChecklist($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.checklist-oxigenoterapia', compact('pedido'));
        return $pdf->download('checklist_' . $pedido->nro_solicitud . '.pdf');
    }

    public function generarResumen($id) {
        $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->findOrFail($id);
        
        $pdf = \PDF::loadView('pdf.resumen-oxigenoterapia', compact('pedido'));
        return $pdf->download('resumen_' . $pedido->nro_solicitud . '.pdf');
    }
} 