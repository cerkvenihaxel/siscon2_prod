<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminPedidoMedicamento35Controller extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			function adminPrivilegeId(){

				$privilege = CRUDBooster::myPrivilegeId();
				if($privilege == 1 || $privilege == 17 || $privilege == 5){
					return false;
				}else{
					return true;
				}}

            function convenioPrivilegeId(){

                $privilege = CRUDBooster::myPrivilegeId();
                if($privilege != 38 && $privilege != 40 && $privilege != 41){
                    return true;
                }else{
                    return false;
                }
            }

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "";
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
			$this->button_export = false;
			$this->table = "pedido_medicamento";
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
			$this->col[] = ["label"=>"Patología", "name"=>"patologia", "join"=>"patologias,nombre"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			adminPrivilegeId() == true ? $IDMEDICO = DB::table('medicos')->where('nombremedico',CRUDBooster::myName())->value('id') : $IDMEDICO = "";


			//Prueba

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,nroAfiliado,documento,sexo,localidad','datamodal_select_to'=>'nroAfiliado:nroAfiliado,obra_social:obra_social','datamodal_size'=>'large'];
			$this->form[] = ['label'=>'Nro de Afiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true];
			$this->form[] = ['label'=>'Obra social', 'name'=>'obra_social', 'type'=>'text', 'validation'=>'min:1|max:255', 'width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','required'=>true,'readonly'=>'true','value'=>'APOS-MED-'.date('dmHis')];
			$this->form[] = ['label'=>'Institución','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre','required'=>true];
			$this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico','required'=>true, 'value'=>$IDMEDICO];
			$this->form[] = ['label'=>'Zona Residencia','name'=>'zona_residencia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'dataenum'=>'Norte;Sur;Este;Oeste;Centro;Interior'];
			$this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','required'=>true];
			$this->form[] = ['label'=>'Email','name'=>'email','type'=>'text','validation'=>'min:1|max:255|email|','width'=>'col-sm-10','placeholder'=>'Introduce una dirección de correo electrónico válida'];
			$this->form[] = ['label'=>'Provincia', 'name'=>'provincia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'provincias,nombre', 'required'=>true];
			$this->form[] = ['label'=>'Fecha Receta','name'=>'fecha_receta','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>date('Y-m-d')];
			$this->form[] = ['label'=>'Receta Post-datada','name'=>'postdatada','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'postdatada,cantidad'];
			$this->form[] = ['label'=>'Fecha primer vencimiento','name'=>'fecha_vencimiento','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>date('Y-m-d', strtotime('+1 month'))];
			$this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','required'=>true,'datatable'=>'estado_solicitud,estado','value'=>1, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Teléfono médico', 'name'=>'tel_medico', 'type'=>'text','validation'=>'required','width'=>'col-sm-10','required'=>true];
			$this->form[] = ['label'=>'Patologías','name'=>'patologia','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'datatable'=>'patologias,nombre','required'=>true];
			$this->form[] = ['label'=>'Discapacidad', 'name'=>'discapacidad', 'type'=>'select', 'validation'=>'required', 'width'=>'col-sm-10','required'=>true, 'dataenum'=>'Si;No'];


			$columns = [];
			$columns[] = ['label'=> 'Medicamentos solicitados', 'name'=>'articuloZafiro_id', 'type'=>'datamodal', 'datamodal_table'=>'articulosZafiro', 'validation'=>'required','datamodal_columns_alias'=>'Monodroga, Descripción del artículo, Presentación, ID ARTÍCULO','datamodal_columns'=>'des_monodroga,des_articulo,presentacion,id_articulo','datamodal_size'=>'large', 'datamodal_where'=>'id_familia = "01"', 'AND', 'id_familia= "14"', 'datamodal_select_to'=>'presentacion_completa:presentacion','required'=>true];
			$columns [] = ['label'=> 'Presentación', 'name'=>'presentacion', 'type'=>'text', 'readonly'=>true];
			$columns[] = ['label'=> 'Cantidad', 'name'=>'cantidad', 'type'=>'number', 'validation'=>'required|integer|min:0', 'required'=>true];

			$this->form[] = ['label'=>'Detalles de la solicitud', 'name'=>'pedido_medicamento_detail', 'type'=>'child','table'=>'pedido_medicamento_detail', 'foreign_key'=>'pedido_medicamento_id', 'columns'=>$columns, 'width'=>'col-sm-10','required'=>true];

			$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];

			$this->form[] = ['label'=>'Archivo 1', 'name'=>'archivo','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 2', 'name'=>'archivo2','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 3', 'name'=>'archivo3','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 4', 'name'=>'archivo4','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];

			$this->form[] = ['label'=>'Usuario de carga','name'=>'stamp_user','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email'), 'readonly'=>true];

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"afiliados,id"];
			//$this->form[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Zona Residencia","name"=>"zona_residencia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Afiliado","name"=>"tel_afiliado","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Email","name"=>"email","type"=>"email","required"=>TRUE,"validation"=>"required|min:1|max:255|email|unique:pedido_medicamento","placeholder"=>"Introduce una dirección de correo electrónico válida"];
			//$this->form[] = ["label"=>"Fecha Receta","name"=>"fecha_receta","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Postdatada","name"=>"postdatada","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Fecha Vencimiento","name"=>"fecha_vencimiento","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Tel Medico","name"=>"tel_medico","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Stamp User","name"=>"stamp_user","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Discapacidad","name"=>"discapacidad","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo","name"=>"archivo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo2","name"=>"archivo2","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo3","name"=>"archivo3","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo4","name"=>"archivo4","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			# OLD END FORM

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

		$PRIVILEGIO[] = CRUDBooster::myPrivilegeId();
	        $this->sub_module = array();

            $this->sub_module[] = ['label'=>'Autorizar solicitud', 'path'=>'convenio_oficina_os/add/?id[]=[id]','foreign_key'=>'pedido_medicamento_id','button_color'=>'success','button_icon'=>'fa fa-check','parent_columns'=>'nrosolicitud,fecha_cirugia,medicos_id,observaciones','showIf'=>"[estado_solicitud_id] == 8 && ".convenioPrivilegeId() == true ];



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

		$this->addaction[] = ['label'=>'AUDITAR : SOLICITUD APROBADA','url'=>CRUDBooster::mainpath('set-status/8/[id]'),'icon'=>'fa fa-check','color'=>'success','showIf'=>"[estado_solicitud_id] == 1 &&".convenioPrivilegeId() == true, 'confirmation'=>true];
		$this->addaction[] = ['label'=>'AUDITAR : SOLICITUD RECHAZADA','url'=>CRUDBooster::mainpath('set-status/9/[id]'),'icon'=>'fa fa-times','color'=>'danger','showIf'=>"[estado_solicitud_id] == 1 &&".convenioPrivilegeId() == true, 'confirmation'=>true];
		$this->addaction[] = ['label'=>'Anular solicitud','url'=>CRUDBooster::mainpath('set-status/5/[id]'),'icon'=>'fa fa-check','color'=>'danger','showIf'=>"[estado_solicitud_id] == 8 &&".convenioPrivilegeId() == true, 'confirmation'=>true];

		/*

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
	        $this->script_js = NULL;


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
	        //Your code here
	$convenio_oficina = DB::table('convenio_oficina_os')->get();
	if(CRUDBooster::myPrivilegeId() == 6) {
		$medicoName = CRUDBooster::myName();
		$medicoId = DB::table('medicos')->where('nombremedico', $medicoName)->value('id');
		$query->where('medicos_id', $medicoId);
	}

	if(CRUDBooster::isSuperadmin() == false) {
foreach ($convenio_oficina as $co) {
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
            $postdata['estado_solicitud_id']=1;


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

	public function getSetStatus($status,$id) {
			DB::table('pedido_medicamento')->where('id',$id)->update(['estado_solicitud_id'=> $status]);
			//This will redirect back and gives a message

			$medicoName = DB::table('pedido_medicamento')->where('id',$id)->value('medicos_id');
			$medicoId = DB::table('medicos')->where('id', $medicoName)->value('nombremedico');
			$config['content'] = "Hola $medicoId, su solicitud fue auditada. Revise el estado en sus solicitudes cargadas.";
			$id = DB::table('cms_users')->where('name',$medicoId)->value('id');
			$config['id_cms_users'] = [$id];
			CRUDBooster::sendNotification($config);



			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue auditada con éxito!","info");
		 }

		 public function getSetDate($date, $id){

			DB::table('entrantes')->where('id',$id)->update(['fecha_expiracion'=> $date]);

			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La fecha de expiración fue modificada con éxito!","info");
		 }

	    //By the way, you can still create your own method in here... :)


	}
