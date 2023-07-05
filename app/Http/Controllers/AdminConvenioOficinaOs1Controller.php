<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminConvenioOficinaOs1Controller extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
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
			$this->table = "convenio_oficina_os";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
            $this->col = [];
            $this->col[] = ["label"=>"Fecha de carga","name"=>"created_at" ];
            $this->col[] = ["label"=>"Nombre y apellido afiliado","name"=>"afiliados_id","join"=>"afiliados,apeynombres" ];
            $this->col[] = ["label"=>"Nro Afiliado","name"=>"nroAfiliado"];
            $this->col[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","join"=>"clinicas,nombre" ];
            $this->col[] = ["label"=>"Número de solicitud","name"=>"nrosolicitud" ];
            $this->col[] = ["label"=>"Médico Solicitante","name"=>"medicos_id","join"=>"medicos,nombremedico" ];
            $this->col[] = ["label"=>"Estado solicitud","name"=>"estado_solicitud_id","join"=>"estado_solicitud,estado" ];
            $this->col[] = ["label"=>"Proveedor Seleccionado","name"=>"proveedor","join"=>"proveedores_convenio,nombre" ];
            # END COLUMNS DO NOT REMOVE THIS LINE

			$url = $_GET['id'];
			$custom_element = view('articulosEntrantesMed')->render();




            # START FORM DO NOT REMOVE THIS LINE
            $this->form = [];
            $this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,nroAfiliado,documento,sexo,localidad','datamodal_select_to'=>'nroAfiliado:nroAfiliado,obra_social:obra_social','datamodal_size'=>'large', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('afiliados_id')];
            $this->form[] = ['label'=>'Nro de Afiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('nroAfiliado')];
            $this->form[] = ['label'=>'Obra social', 'name'=>'obra_social', 'type'=>'text', 'validation'=>'min:1|max:255', 'width'=>'col-sm-10', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('obra_social')];
            $this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('edad')];
            $this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','required'=>true,'readonly'=>'true', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('nrosolicitud')];
            $this->form[] = ['label'=>'Institución','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre','required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('clinicas_id')];
            $this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico','required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('medicos_id')];
            $this->form[] = ['label'=>'Zona Residencia','name'=>'zona_residencia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'dataenum'=>'Norte;Sur;Este;Oeste;Centro;Interior', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('zona_residencia')];
            $this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'number','validation'=>'required|numeric','width'=>'col-sm-10','required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('tel_afiliado')];
            $this->form[] = ['label'=>'Email','name'=>'email','type'=>'text','validation'=>'required|min:1|max:255|email|','width'=>'col-sm-10','placeholder'=>'Introduce una dirección de correo electrónico válida', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('email')];
            $this->form[] = ['label'=>'Provincia', 'name'=>'provincia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'provincias,nombre', 'required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('provincia')];
            $this->form[] = ['label'=>'Fecha Receta','name'=>'fecha_receta','type'=>'date','validation'=>'required|date','width'=>'col-sm-10','value'=>DB::table('pedido_medicamento')->where('id',$url)->value('fecha_receta')];
            $this->form[] = ['label'=>'Receta Post-datada','name'=>'postdatada','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'postdatada,cantidad', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('postdatada')];
            $this->form[] = ['label'=>'Fecha primer vencimiento','name'=>'fecha_vencimiento','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('fecha_vencimiento')];
            $this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','required'=>true,'datatable'=>'estado_solicitud,estado','value'=>3];
            $this->form[] = ['label'=>'Teléfono médico', 'name'=>'tel_medico', 'type'=>'number','validation'=>'required|numeric','width'=>'col-sm-10','required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('tel_medico')];
            $this->form[] = ['label'=>'Patologías','name'=>'patologia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'datatable'=>'patologias,nombre','required'=>true, 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('patologia')];
            $this->form[] = ['label'=>'Discapacidad', 'name'=>'discapacidad', 'type'=>'select', 'validation'=>'required', 'width'=>'col-sm-10','required'=>true, 'dataenum'=>'Si;No', 'value'=>DB::table('pedido_medicamento')->where('id',$url)->value('discapacidad')];

	        $this->form[] = ['name'=>'custom_field','type'=>'custom','html'=>$custom_element,'width'=>'col-sm-10'];

            $this->form[] = ['label'=>'Proveedor seleccionado','name'=>'proveedor','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'proveedores_convenio,nombre','required'=>true];
            $this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];

            $this->form[] = ['label'=>'Archivo 1', 'name'=>'archivo','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 2', 'name'=>'archivo2','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 3', 'name'=>'archivo3','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 4', 'name'=>'archivo4','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];

            $this->form[] = ['label'=>'Usuario de carga','name'=>'stamp_user','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email'), 'readonly'=>true];

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

		    $PRIVILEGIO = CRUDBooster::myPrivilegeId();
	        $this->sub_module = array();
            $this->sub_module[] = ['label'=>'Generar pedido', 'path'=>'cotizacion_convenio/add/?id[]=[id]','foreign_key'=>'convenio_oficina_os_id','button_color'=>'success','button_icon'=>'fa fa-check', 'showIf'=>"$PRIVILEGIO == 38"];


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
            $this->addaction[] = ['label'=>'Ver medicación requerida','url'=>'/medicacion_requerida/[id]','icon'=>'fa fa-eye','target'=>'_blank','color'=>'warning'];


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
            $this->button_selected[] = ['label'=>'Set Active','icon'=>'fa fa-check','name'=>'set_active'];

            /*
            | ----------------------------------------------------------------------
            | Add alert message to this module at overheader
            | ----------------------------------------------------------------------
            | @message = Text of message
            | @type    = warning,success,danger,info
            |
            */
	        $this->alert        = array();



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



	        /*
	        | ----------------------------------------------------------------------
	        | Add javascript at body
	        | ----------------------------------------------------------------------
	        | javascript code in the variable
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = null;

            /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code before index table
	        | ----------------------------------------------------------------------
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;


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

            if($button_name == 'set_active') {
                DB::table('convenio_oficina_os')->whereIn('id',$id_selected)->update(['estado_solicitud_id'=>6]);
            }

	    }



	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate query of index result
	    | ----------------------------------------------------------------------
	    | @query = current sql query
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
            $cotizacion = DB::table('cotizacion_convenio')->get();

            if(CRUDBooster::isSuperadmin() == false) {
                foreach ($cotizacion as $co) {
                    $query->where('nrosolicitud', '!=', $co->nrosolicitud);
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
            $nroSolicitud = DB::table('convenio_oficina_os')->where('id', $id)->value('nrosolicitud');
            DB::table('pedido_medicamento')->where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 3]);


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



	    //By the way, you can still create your own method in here... :)


	}
