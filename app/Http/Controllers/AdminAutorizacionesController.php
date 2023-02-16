<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminAutorizacionesController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_export = CRUDBooster::myPrivilegeId()== 1 ? true : false;;
			$this->table = "autorizaciones";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			function proveedorPrivilegeId(){
		
				$privilege = CRUDBooster::myPrivilegeId();
				if($privilege != 1 && $privilege != 2 && $privilege != 3 && $privilege != 5 && $privilege != 6 && $privilege != 17 && $privilege != 37){
					return true;
				}else{
					return false;
				}
			}	

			$this->col = [];
			$this->col [] = ["label"=>"Fecha creación","name"=>"created_at"];
			$this->col[] = ["label"=>"Autorizado","name"=>"autorizado"];
			$this->col[] = ["label"=>"Nombre y Apellido Afiliado","name"=>"afiliados_id","join"=>"afiliados,apeynombres"];
			$this->col[] = ["label"=>"Número de solicitud","name"=>"nrosolicitud"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Clínica","name"=>"clinicas_id","join"=>"clinicas,nombre"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Estado Paciente","name"=>"estado_paciente_id","join"=>"estado_paciente,estado"];
			$this->col[] = ["label"=>"Estado Solicitud","name"=>"estado_solicitud_id","join"=>"estado_solicitud,estado"];	

		# START COLUMNS DO NOT REMOVE THIS LINE
/*			$this->col = [];
			$this->col[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","join"=>"afiliados,id"];
			$this->col[] = ["label"=>"Archivo","name"=>"archivo"];
			$this->col[] = ["label"=>"Autorizado","name"=>"autorizado"];
			$this->col[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","join"=>"clinicas,id"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Estado Paciente Id","name"=>"estado_paciente_id","join"=>"estado_paciente,id"];
			$this->col[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","join"=>"estado_solicitud,id"];
			# END COLUMNS DO NOT REMOVE THIS LINE
*/
			$url = $_GET['id'];
			$custom_element = view('adjudicacionesSolicitud')->render();


			$this->form = [];
			$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,documento,sexo,localidad','datamoda_columns_alias'=>'Nombre y Apellido, Documento, Sexo, Localidad','datamodal_size'=>'large', 'required'=>true, 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('afiliados_id'), 'disabled'=>'disabled'];
			$this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('nrosolicitud'), 'readonly'=>true];
			$this->form[] = ['label'=>'Clínica','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('clinicas_id')];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('edad'), 'readonly'=>true];
			//$this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('tel_afiliado'),'readonly'=>true];
			$this->form[] = ['label'=>'Estado Paciente','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,estado', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('estado_paciente_id')];
			$this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,estado','value'=>4, 'readonly'=>true];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('fecha_cirugia')];
			$this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico', 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('medicos_id')];
//			$this->form[] = ['label'=>'Autorizado','name'=>'autorizado','type'=>'text','validation'=>'required|required','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Autorizado','name'=>'autorizado','type'=>'text','validation'=>'required|required','width'=>'col-sm-10' , 'value'=>DB::table('adjudicaciones')->where('id',$url)->value('adjudicatario'), 'readonly'=>true];			
			$this->form[] = ['name'=>'custom_field','type'=>'custom','html'=>$custom_element,'width'=>'col-sm-10'];

			$this->form[] = ['label'=>'Archivo', 'name'=>'archivo', 'type'=>'upload', 'validation'=>'max:3000', 'width'=>'col-sm-10', 'help'=>'Archivos soportados : JPG, JPEG, PNG, GIF, BMP'];
			$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			
			# START FORM DO NOT REMOVE THIS LINE
/*			$this->form = [];
			$this->form[] = ['label'=>'Afiliados Id','name'=>'afiliados_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'afiliados,id'];
			$this->form[] = ['label'=>'Archivo','name'=>'archivo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Autorizado','name'=>'autorizado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Clinicas Id','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,id'];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Estado Paciente Id','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,id'];
			$this->form[] = ['label'=>'Estado Solicitud Id','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,id'];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Medicos Id','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,id'];
			$this->form[] = ['label'=>'Nrosolicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE
*/
			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"afiliados,id"];
			//$this->form[] = ["label"=>"Archivo","name"=>"archivo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Autorizado","name"=>"autorizado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Estado Paciente Id","name"=>"estado_paciente_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_paciente,id"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Fecha Cirugia","name"=>"fecha_cirugia","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
	  	$this->sub_module[] = ['label'=>'Agregar a presentacion final', 'path'=>'presentacion/add/?id[]=[id]','foreign_key'=>'entrantes_id','button_color'=>'success','button_icon'=>'fa fa-paper-plane-o','parent_columns'=>'nrosolicitud,fecha_cirugia,medicos_id,observaciones'];


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

		$PRIVILEGIO=CRUDBooster::myPrivilegeId();

		$this->addaction[] = ['label'=>'ANULAR','url'=>CRUDBooster::mainpath('set-status/5/[id]'),'icon'=>'fa fa-times','color'=>'danger','showIf'=>"$PRIVILEGIO == 1", 'confirmation'=>true];

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
			if(proveedorPrivilegeId()){
				$proveedorName = CRUDBooster::myName();
				$query->where('autorizado', $proveedorName);
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
  	DB::table('adjudicaciones')->where('adjudicatario',$postdata['autorizado'])->where('nrosolicitud', $postdata['nrosolicitud'])->update(['estado_solicitud_id'=>4]);
	DB::table('cotizaciones')->where('proveedor',$postdata['autorizado'])->where('nrosolicitud', $postdata['nrosolicitud'])->update(['estado_solicitud_id'=>6]);
	DB::table('entrantes')->where('nrosolicitud', $postdata['nrosolicitud'])->update(['estado_solicitud_id'=>4]);


	DB::table('autorizaciones')->where('autorizado','!=', $postdata['autorizado'])->where('nrosolicitud',$postdata['nrosolicitud'])->delete();
//	DB::table('cotizaciones')->where('nrosolicitud',$postdata['nrosolicitud'])->update(['estado_solicitud_id'=>4]);
//	DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->update(['estado_solicitud_id'=>4]);
//	DB::table('adjudicaciones')->where('nrosolicitud',$postdata['nrosolicitud'])->update(['estado_solicitud_id'=>4]);

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

			$proveedorName = DB::table('cotizaciones')->where('nrosolicitud',Request::input('nrosolicitud'))->value('proveedor');
			$config['content'] = "Hola $proveedorName, su solicitud fue AUTORIZADA. Revise el estado en sus solicitudes cargadas y contáctese con el médico para pactar entregar del material.";
			
			
			$config['to'] = CRUDBooster::adminPath('cotizaciones19?q='.Request::input('nrosolicitud'));
			$id = DB::table('cms_users')->where('name',$proveedorName)->value('id');
			$config['id_cms_users'] = [$id, 178, 1, 11, 32, 92, 8];

			CRUDBooster::sendNotification($config);


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
			DB::table('autorizaciones')->where('id',$id)->update(['estado_solicitud_id'=> $status]);
			//This will redirect back and gives a message
		
$proveedorName = DB::table('cotizaciones')->where('nrosolicitud',Request::input('nrosolicitud'))->value('proveedor');
			$config['content'] = "Hola $proveedorName, su solicitud fue ANULADA. Revise el estado en sus solicitudes cotizadas.";
			
			
			$config['to'] = CRUDBooster::adminPath('cotizaciones19?q='.Request::input('nrosolicitud'));
			$id = DB::table('cms_users')->where('name',$proveedorName)->value('id');
			$config['id_cms_users'] = [$id];

			CRUDBooster::sendNotification($config);


	CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue anulada con éxito!","info");
		 }


	    //By the way, you can still create your own method in here... :) 


	}
