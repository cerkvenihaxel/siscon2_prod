<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminPresentacionController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->table = "presentacion";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# GLOBAL VARIABLES 
/*  */
			$url = $_GET['id']; // THIS VARIABLE GETS THE ID OF THE LIST


			function getNombre($url){
				$nro = DB::table('autorizaciones')->where('id',$url)->value('afiliados_id');
				$nombre = DB::table('afiliados')->where('id',$nro)->value('apeynombres');
				return $nombre;
			}

			function getNroAfiliado($url){
				$nro = DB::table('autorizaciones')->where('id',$url)->value('afiliados_id');
				$numeroAfiliado = DB::table('afiliados')->where('id',$nro)->value('nroAfiliado');
				return $numeroAfiliado;
			}

			function getNroSolicitud($url){
				$nro = DB::table('autorizaciones')->where('id',$url)->value('nroSolicitud');
				return $nro;
			}

			function getProveedor($url){
				$nombre = DB::table('autorizaciones')->where('id',$url)->value('autorizado');
				return $nombre;
			}
			
			function getMedicoPrestador($url){
				$nro = DB::table('autorizaciones')->where('id',$url)->value('medicos_id');
				$medico = DB::table('medicos')->where('id',$nro)->value('nombremedico');
				return $medico;
			}

			function getInstitucion($url){
				$nro = DB::table('autorizaciones')->where('id',$url)->value('clinicas_id');
				$institucion = DB::table('clinicas')->where('id',$nro)->value('nombre');
				return $institucion;
			}
			
			function getFechaCirugia($url){
				$fecha = DB::table('autorizaciones')->where('id',$url)->value('fecha_cirugia');
				return $fecha;
			}

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Nombre Afiliado","name"=>"nombreAfiliado"];
			$this->col[] = ["label"=>"Número Afiliado","name"=>"nroAfiliado"];
			$this->col[] = ["label"=>"Número Solicitud","name"=>"nroSolicitud"];
			$this->col[] = ["label"=>"Proveedor","name"=>"proveedor"];
			$this->col[] = ["label"=>"Material Entregado","name"=>"materialEntregado"];
			$this->col[] = ["label"=>"Médico Prestador","name"=>"medicoPrestador"];
			$this->col[] = ["label"=>"Institucion","name"=>"institucion"];
			$this->col[] = ["label"=>"Nro Remito","name"=>"nroRemito"];
			$this->col[] = ["label"=>"Nro Factura","name"=>"nroFactura"];
			$this->col[] = ["label"=>"Cantidad","name"=>"cantidad"];
			$this->col[] = ["label"=>"Stickers","name"=>"stickers"];
			$this->col[] = ["label"=>"Foja Quirúrgica","name"=>"fojaQuirurgica"];
			$this->col[] = ["label"=>"Fecha Cirugia","name"=>"fechaCirugia"];
			$this->col[] = ["label"=>"Fecha Entrega","name"=>"fechaEntrega"];
			$this->col[] = ["label"=>"Precio Total","name"=>"precioTotal"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Nombre Afiliado','name'=>'nombreAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getNombre($url)];
			$this->form[] = ['label'=>'Nro Afiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getNroAfiliado($url)];
			$this->form[] = ['label'=>'Nro Solicitud','name'=>'nroSolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getNroSolicitud($url)];
			$this->form[] = ['label'=>'Proveedor', 'name' => 'proveedor', 'type' => 'text', 'validation' => 'required|min:0', 'width' => 'col-sm-10', 'value'=> getProveedor($url)];
			$this->form[] = ['label'=>'Material Entregado','name'=>'materialEntregado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Medico Prestador','name'=>'medicoPrestador','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getMedicoPrestador($url)];
			$this->form[] = ['label'=>'Institucion','name'=>'institucion','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getInstitucion($url)];
			$this->form[] = ['label'=>'Nro Remito','name'=>'nroRemito','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Nro Factura','name'=>'nroFactura','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Cantidad','name'=>'cantidad','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fechaCirugia','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=> getFechaCirugia($url)];
			$this->form[] = ['label'=>'Fecha Entrega','name'=>'fechaEntrega','type'=>'date','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Stickers','name'=>'stickers','type'=>'select','dataenum'=>'SI;NO','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Foja Quirurgica','name'=>'fojaQuirurgica','type'=>'select','dataenum'=>'SI;NO','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Precio Total','name'=>'precioTotal','type'=>'money','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Cantidad","name"=>"cantidad","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"FechaCirugia","name"=>"fechaCirugia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"FechaEntrega","name"=>"fechaEntrega","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"FojaQuirurgica","name"=>"fojaQuirurgica","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Institucion","name"=>"institucion","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"MaterialEntregado","name"=>"materialEntregado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"MedicoPrestador","name"=>"medicoPrestador","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NombreAfiliado","name"=>"nombreAfiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroFactura","name"=>"nroFactura","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroRemito","name"=>"nroRemito","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroSolicitud","name"=>"nroSolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"PrecioTotal","name"=>"precioTotal","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Stickers","name"=>"stickers","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
	
	DB::table('entrantes')->where('nrosolicitud', Request::input('nroSolicitud'))->update(['estado_solicitud_id'=>6]);


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
