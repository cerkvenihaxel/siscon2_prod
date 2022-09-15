<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminEntrantesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "30";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = CRUDBooster::myPrivilegeId()== 1 ? true : false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "entrantes";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ['label'=>'Fecha de carga','name'=>'created_at',];
			$this->col[] = ["label"=>"Nombre y Apellido Afiliado","name"=>"afiliados_id","join"=>"afiliados,apeynombres"];
			$this->col[] = ["label"=>"Nro. Afiliado","name"=>"nroAfiliado"];
			$this->col[] = ["label"=>"Clínica","name"=>"clinicas_id","join"=>"clinicas,nombre"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Nro. Solicitud","name"=>"nrosolicitud"];
			$this->col[] = ["label"=>"Medico","name"=>"medicos_id","join"=>"medicos,nombremedico"];
			$this->col[] = ["label"=>"Estado Paciente","name"=>"estado_paciente_id","join"=>"estado_paciente,estado"];
			$this->col[] = ["label"=>"Estado Solicitud","name"=>"estado_solicitud_id","join"=>"estado_solicitud,estado"];
			$this->col[] = ["label"=>"Fecha Cirugia","name"=>"fecha_cirugia"];
			$this->col[] =["label"=>"Necesidad", "name"=>"necesidad","join"=>"necesidad,necesidad"];
			$this-> col[] =["label"=>"Grupo articulos", "name"=>"grupo_articulos","join"=>"grupos,des_grupo"];

			$this->col[] = ["label"=>"Días transcurridos", "name"=>"(DATEDIFF(CURDATE(), created_at)) as dias_transcurridos"];
//<<<<<<< HEAD
//			$this->col[] = ["label"=> "Usuario Carga", "name"=>"userId"];

//=======
//>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202
			# END COLUMNS DO NOT REMOVE THIS LINE


			function adminPrivilegeId(){
		
				$privilege = CRUDBooster::myPrivilegeId();
				if($privilege == 1 || $privilege == 17 || $privilege == 5){
					return false;
				}else{
					return true;
				}
			}

			function proveedorPrivilegeId(){
		
				$privilege = CRUDBooster::myPrivilegeId();
				if($privilege != 2 && $privilege != 3 && $privilege != 5 && $privilege != 6){
					return true;
				}else{
					return false;
				}
			}

				$id = DB::table('entrantes')->where('id', Request::get('id'))->value('id');
			function contadordeDias(){
				$fecha_de_carga = DB::table('entrantes')->where('id', Request::get('id'))->value('created_at');
				$fecha_actual = date('Y-m-d');
				$fecha_de_carga = $fecha_de_carga->created_at;
				$fecha_de_carga = date('Y-m-d', strtotime($fecha_de_carga));
				$fecha_de_carga = strtotime($fecha_de_carga);
				$fecha_actual = strtotime($fecha_actual);
				$diferencia = $fecha_actual - $fecha_de_carga;
				$dias = floor($diferencia / (60 * 60 * 24));
				return $dias;

			}
		
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
//<<<<<<< HEAD
//			$this->form[]= ['label'=>'Fecha de carga', 'name'=>'created_at','type'=>'datetime','validation'=>'required','width'=>'col-sm-10','required'=>true];
			//$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,nroAfiliado,documento,sexo,localidad','datamodal_size'=>'large','required'=>true];
			$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,documento,sexo,localidad','datamodal_select_to'=>'nroAfiliado:nroAfiliado','datamodal_size'=>'large'];
			$this->form[] = ['label'=>'Nro de Afiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true]; 
			$this->form[] = ['label'=>'Clínica','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre','required'=>true];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10','required'=>true];

			$this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'number','validation'=>'required|numeric','width'=>'col-sm-10','required'=>true];
			$this->form[] = ['label'=>'Estado Paciente','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,estado','required'=>true];
//<<<<<<< HEAD

			adminPrivilegeId() == true ? $IDMEDICO = DB::table('medicos')->where('nombremedico',CRUDBooster::myName())->value('id') : $IDMEDICO = "";

			$this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','required'=>true,'datatable'=>'estado_solicitud,estado','value'=>1, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10','required'=>true];
			$this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico','required'=>true, 'value'=>$IDMEDICO, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Teléfono médico', 'name'=>'tel_medico', 'type'=>'number','validation'=>'required|numeric','width'=>'col-sm-10','required'=>true];
			$this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','required'=>true,'readonly'=>'true','value'=>'APOS'.date('dmHis')];
			$this->form[] = ['label'=>'Necesidad', 'name'=>'necesidad', 'type'=>'select2', 'validation'=>'required', 'width'=>'col-sm-10','required'=>true, 'datatable'=>'necesidad,necesidad'];
			$this->form[] = ['label'=>'Especialidad', 'name'=>'grupo_articulos', 'type'=>'select2', 'validation'=>'required','required'=>true, 'width'=>'col-sm-10', 'datatable'=>'grupos,des_grupo'];

			//Solicitud autogenerada por el sistema
			$columns = [];
			$columns[] = ['label'=> 'Artículos solicitados', 'name'=>'articulos_id', 'type'=>'datamodal', 'datamodal_table'=>'articulos', 'datamodal_columns'=>'des_articulo,articuloId','datamodal_select_to'=>'grupo:grupo','datamodal_size'=>'large', 'required'=>true];
			$columns[] = ['label'=> 'Grupo', 'name'=>'grupo', 'type'=>'text', 'validation'=>'required|integer|min:0', 'disabled'=>'disabled', 'width'=>'col-sm-10', 'readonly'=>true];		
			$columns[] = ['label'=> 'Cantidad', 'name'=>'cantidad', 'type'=>'number', 'validation'=>'required|integer|min:0'];

			$this->form[] = ['label'=>'Detalles de la solicitud', 'name'=>'entrantes_detail', 'type'=>'child','table'=>'entrantes_detail', 'foreign_key'=>'entrantes_id', 'columns'=>$columns, 'width'=>'col-sm-10','required'=>true];
			
			//$this->form[] = ['label'=> 'Usuario Carga', 'name'=>'userId', 'type'=>'select', 'validation'=>'required|integer|min:0', 'width'=>'col-sm-10', 'datatable'=>'cms_users,name', 'value'=>CRUDBooster::myId(), 'readonly'=>true, 'disabled'=>'disabled'];
//			$this->form[] = ['label'=> 'Usuario Carga', 'name'=>'userId', 'type'=>'text', 'validation'=>'required', 'width'=>'col-sm-10','value'=>CRUDBooster::myName(), 'readonly'=>true];
			$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','required'=>true];


			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ['label'=>'Afiliados Id','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres, documento, jurisdiccion, sexo','datamodal_size'=>'large'];
			//$this->form[] = ['label'=>'Clinicas Id','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,id'];
			//$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Estado Paciente Id','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,id'];
			//$this->form[] = ['label'=>'Estado Solicitud Id','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,id'];
			//$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Medicos Id','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,id'];
			//$this->form[] = ['label'=>'Nrosolicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
	        $this->sub_module = array();

		$this->sub_module[] = ['label'=>'Cotizaciones', 'path'=>'cotizaciones19/add/?id[]=[id]','foreign_key'=>'entrantes_id','button_color'=>'success','button_icon'=>'fa fa-shopping-cart','parent_columns'=>'nrosolicitud,fecha_cirugia,medicos_id,observaciones', 'showIf'=> contadorDeDias() > 3 ? "1":"0"];



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
//<<<<<<< HEAD
		$this->addaction[] = ['label'=>'URGENTE','url'=>'#','icon'=>'fa fa-exclamation-triangle','color'=>'danger', 'showIf'=>'[necesidad] == "1"'];
		$this->addaction[] = ['label'=>'PROGRAMADA','url'=>'#','icon'=>'fa fa-calendar-check-o','color'=>'warning', 'showIf'=>'[necesidad] == "2"'];
		

		$PRIVILEGIO=CRUDBooster::myPrivilegeId();
		$this->addaction[] = ['label'=>'AUDITAR : SOLICITUD APROBADA','url'=>CRUDBooster::mainpath('set-status/8/[id]'),'icon'=>'fa fa-check','color'=>'success','showIf'=>"[estado_solicitud_id] == 1 && $PRIVILEGIO == 17", 'confirmation'=>true];
		$this->addaction[] = ['label'=>'AUDITAR : SOLICITUD RECHAZADA','url'=>CRUDBooster::mainpath('set-status/9/[id]'),'icon'=>'fa fa-times','color'=>'danger','showIf'=>"[estado_solicitud_id] == 1 && $PRIVILEGIO == 17", 'confirmation'=>true];
		//=======

			
//>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202

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
			$this->button_selected[] = ['label'=>'Activar','icon'=>'fa fa-check','name'=>'set_active'];


	                
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
//<<<<<<< HEAD
	        $this->table_row_color = array();     	          
			$this->table_row_color[] = ['condition'=>"[necesidad]==1","color"=>"danger"];
			$this->table_row_color[] = ['condition'=>"[necesidad]==2","color"=>"warning"];
			$this->table_row_color[] = ['condition'=>"[necesidad]==3","color"=>"info"];
//=======
	        $this->table_row_color = array();
			$this->table_row_color[] = ['condition'=>"[necesidad]==1","color"=>"danger"];
			$this->table_row_color[] = ['condition'=>"[necesidad]==2","color"=>"warning"];
			$this->table_row_color[] = ['condition'=>"[necesidad]==3","color"=>"info"];




//>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();
			//$this->index_statistic[] = ['label'=>'Data Total','count'=>DB::table('entrantes')->count(),'icon'=>'fa fa-check','color'=>'success'];




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
	        $this->pre_index_html = NULL;
	        
	        
	        
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
				DB::table('entrantes')->whereIn('id',$id_selected)->update(['estado_solicitud_id'=>3]);
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
	if(CRUDBooster::myPrivilegeId() == 6) {
			$medicoName = CRUDBooster::myName();
			$medicoId = DB::table('medicos')->where('nombremedico', $medicoName)->value('id');
			$query->where('medicos_id', $medicoId);
	if(CRUDBooster::myPrivilegeId() != 1 && CRUDBooster::myPrivilegeId() != 2 && CRUDBooster::myPrivilegeId() != 3 && CRUDBooster::myPrivilegeId() != 6 && CRUDBooster::myPrivilegeId() != 17) {
		$query->where('estado_solicitud_id', '<', 3);
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
			if(adminPrivilegeId()){
			$medicoName = CRUDBooster::myName();
			$medicoId = DB::table('medicos')->where('nombremedico', $medicoName)->value('id');
			DB::table('entrantes')->where('id', $id)->update(['medicos_id' => $medicoId]);
			}
			
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
		DB::table('entrantes')->where('id', $id)->delete();

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
			DB::table('entrantes')->where('id',$id)->update(['estado_solicitud_id'=> $status]);
			//This will redirect back and gives a message
			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue auditada con éxito!","info");
		 }

		





//<<<<<<< HEAD

	}
//=======
	
	    //By the way, you can still create your own method in here... :) 
//>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202

