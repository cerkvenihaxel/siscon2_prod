<?php namespace App\Http\Controllers;

use App\Models\EquiposOxigenoterapia;
use Session;
use Request;
use DB;
use CRUDBooster;

class AdminEquiposOxigenoterapiaController extends \crocodicstudio\crudbooster\controllers\CBController {

    public function cbInit() {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "codigo_equipo";
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
        $this->table = "equipos_oxigenoterapia";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label"=>"Código","name"=>"codigo_equipo"];
        $this->col[] = ["label"=>"Nombre","name"=>"nombre_equipo"];
        $this->col[] = ["label"=>"Marca","name"=>"marca"];
        $this->col[] = ["label"=>"Modelo","name"=>"modelo"];
        $this->col[] = ["label"=>"Nro Serie","name"=>"nro_serie"];
        $this->col[] = ["label"=>"Tipo","name"=>"tipo_equipo"];
        $this->col[] = ["label"=>"Estado","name"=>"estado_equipo"];
        $this->col[] = ["label"=>"Fecha Adquisición","name"=>"fecha_adquisicion"];
        $this->col[] = ["label"=>"Próximo Mantenimiento","name"=>"fecha_proximo_mantenimiento"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        $myEmail = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ['label'=>'Código del Equipo','name'=>'codigo_equipo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Nombre del Equipo','name'=>'nombre_equipo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Marca','name'=>'marca','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Modelo','name'=>'modelo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Número de Serie','name'=>'nro_serie','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Tipo de Equipo','name'=>'tipo_equipo','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10','dataenum'=>'CONCENTRADOR;TANQUE;MASCARILLA;CÁNULA;OTRO'];
        $this->form[] = ['label'=>'Descripción','name'=>'descripcion','type'=>'textarea','validation'=>'min:1|max:1000','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Estado del Equipo','name'=>'estado_equipo','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10','dataenum'=>'DISPONIBLE;EN_USO;MANTENIMIENTO;RETIRADO'];
        $this->form[] = ['label'=>'Fecha de Adquisición','name'=>'fecha_adquisicion','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Fecha Último Mantenimiento','name'=>'fecha_ultimo_mantenimiento','type'=>'date','validation'=>'nullable|date','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Fecha Próximo Mantenimiento','name'=>'fecha_proximo_mantenimiento','type'=>'date','validation'=>'nullable|date','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:1000','width'=>'col-sm-10'];

        //HIDDEN
        $this->form[] = ['label'=>'Stamp User', 'name'=>'stamp_user', 'type'=>'hidden', 'value'=>$myEmail];
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
        $this->addaction[] = ['label'=>'Ver Préstamos', 'url'=>'/admin/equipos-oxigenoterapia/prestamos/[id]','button_color'=>'info','button_icon'=>'fa fa-list'];
        $this->addaction[] = ['label'=>'Registrar Mantenimiento', 'url'=>'/admin/equipos-oxigenoterapia/mantenimiento/[id]','button_color'=>'warning','button_icon'=>'fa fa-wrench', 'showIf'=>'[estado_equipo] == "DISPONIBLE"'];

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
        $this->table_row_color[] = ["condition"=>"[estado_equipo] == 'EN_USO'","color"=>"success"];
        $this->table_row_color[] = ["condition"=>"[estado_equipo] == 'MANTENIMIENTO'","color"=>"warning"];
        $this->table_row_color[] = ["condition"=>"[estado_equipo] == 'RETIRADO'","color"=>"danger"];

        /*
        | ----------------------------------------------------------------------
        | You may use this bellow array to add statistic at dashboard
        | ----------------------------------------------------------------------
        | @label, @count, @icon, @color
        |
        */
        $this->index_statistic = array();
        $this->index_statistic[] = ['label'=>'Total Equipos', 'count'=>DB::table('equipos_oxigenoterapia')->count(), 'icon'=>'fa fa-cogs', 'color'=>'primary'];
        $this->index_statistic[] = ['label'=>'Disponibles', 'count'=>DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'DISPONIBLE')->count(), 'icon'=>'fa fa-check', 'color'=>'success'];
        $this->index_statistic[] = ['label'=>'En Uso', 'count'=>DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'EN_USO')->count(), 'icon'=>'fa fa-users', 'color'=>'info'];
        $this->index_statistic[] = ['label'=>'Mantenimiento', 'count'=>DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'MANTENIMIENTO')->count(), 'icon'=>'fa fa-wrench', 'color'=>'warning'];
        $this->index_statistic[] = ['label'=>'Retirados', 'count'=>DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'RETIRADO')->count(), 'icon'=>'fa fa-times', 'color'=>'danger'];

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
                // Auto-generar código de equipo
                $('#nombre_equipo, #marca, #modelo').on('change', function() {
                    var nombre = $('#nombre_equipo').val();
                    var marca = $('#marca').val();
                    var modelo = $('#modelo').val();
                    
                    if (nombre && marca && modelo) {
                        var codigo = 'OXI-' + marca.substring(0, 3).toUpperCase() + '-' + modelo.substring(0, 3).toUpperCase();
                        $('#codigo_equipo').val(codigo);
                    }
                });

                // Validar fecha de próximo mantenimiento
                $('#fecha_proximo_mantenimiento').change(function() {
                    var fechaUltimo = $('#fecha_ultimo_mantenimiento').val();
                    var fechaProximo = $(this).val();
                    
                    if (fechaUltimo && fechaProximo && fechaProximo <= fechaUltimo) {
                        alert('La fecha de próximo mantenimiento debe ser posterior a la fecha del último mantenimiento');
                        $(this).val('');
                    }
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
        <div class='panel-title'><i class='fa fa-filter'></i> Filtros rápidos</div>
        </div>
        <div class='panel-body'>
        <div class='row'>
        <div class='col-md-12'>
        <div class='fc-button-group'>
        <button type='button' class='btn' style='background-color: lightcoral !important; color: white !important;' onclick='window.location.href = \"?estado=\"'>TODOS (".DB::table('equipos_oxigenoterapia')->count() .")</button>
        <button type='button' class='btn' style='background-color: green !important; color: white !important;' onclick='window.location.href = \"?estado=DISPONIBLE\"'>Disponibles (". DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'DISPONIBLE')->count() .")</button>
        <button type='button' class='btn' style='background-color: #0d6aad !important; color: white !important;' onclick='window.location.href = \"?estado=EN_USO\"'>En Uso (". DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'EN_USO')->count() .")</button>
        <button type='button' class='btn' style='background-color: gold !important;' onclick='window.location.href = \"?estado=MANTENIMIENTO\"'>Mantenimiento (". DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'MANTENIMIENTO')->count() .")</button>
        <button type='button' class='btn' style='background-color: red !important; color: white !important;' onclick='window.location.href = \"?estado=RETIRADO\"'>Retirados (". DB::table('equipos_oxigenoterapia')->where('estado_equipo', 'RETIRADO')->count() .")</button>
        <hr>
        </div>
        </div>
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
        if(isset($_GET['estado']) && $_GET['estado'] != '') {
            $query->where('estado_equipo', $_GET['estado']);
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
        //Your code here
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
        //Your code here
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

    // Métodos adicionales
    public function prestamos($id) {
        $equipo = EquiposOxigenoterapia::findOrFail($id);
        $prestamos = DB::table('prestamo_oxigenoterapia')
                        ->where('nro_serie_equipo', $equipo->nro_serie)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('equipos.prestamos', compact('equipo', 'prestamos'));
    }

    public function mantenimiento($id) {
        $equipo = EquiposOxigenoterapia::findOrFail($id);
        
        return view('equipos.mantenimiento', compact('equipo'));
    }
} 