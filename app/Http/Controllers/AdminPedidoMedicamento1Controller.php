<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\AfiliadosArticulos;
	

	class AdminPedidoMedicamento1Controller extends \crocodicstudio\crudbooster\controllers\CBController {

		function nroAfiliado(){
			$nroAfiliado = $_GET['nroAfiliado'];
			return $nroAfiliado;
		}

		function getMedicamentos(){
			$nroAfiliado = $_GET['nroAfiliado'];
			$patologia = $_GET['patologia'];
			if (empty($nroAfiliado) || empty($patologia)){
				return [];
			}
			else {
			$medicamentos = DB::table('afiliados_articulos')->where('nro_afiliado', $nroAfiliado)->where('patologias', $patologia)->get();
			foreach($medicamentos as $medicamento){
				$medicamento->articuloZafiro_id = DB::table('articulosZafiro')->where('id_articulo', $medicamento->id_articulo)->value('id');
			}
			return $medicamentos;
			}
		}

		function afiliadoID(){
			$nroAfiliado = $_GET['nroAfiliado'];
			$afiliado = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('id');
			return $afiliado;
		}

		
	    
		
		public function cbInit() {

			function adminPrivilegeId()
            {
                $privilege = CRUDBooster::myPrivilegeId();
                if ($privilege == 1 || $privilege == 17 || $privilege == 5) {
                    return false;
                } else {
                    return true;
                }
            }

			$afiliado = $this->nroAfiliado();
			$edad = '';
			if(!empty(DB::table('afiliados')->where('nroAfiliado', $afiliado)->value('fecha_nacimiento'))){
				$edad = date_diff(date_create(DB::table('afiliados')->where('nroAfiliado', $afiliado)->value('fecha_nacimiento')), date_create('today'))->y;
			}
			$medicamento = json_encode($this->getMedicamentos());
			$patologia = $_GET['patologia'];

			if(empty($patologia)){
				$patologia = '';
			}
			

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
			$this->button_export = false;
			$this->table = "pedido_medicamento";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
            $this->col[] = ["label" => "Fecha de carga", "name" => "created_at"];
            $this->col[] = ["label" => "Nombre y apellido afiliado", "name" => "afiliados_id", "join" => "afiliados,apeynombres"];
            $this->col[] = ["label" => "Nro Afiliado", "name" => "nroAfiliado"];
            $this->col[] = ["label" => "Clinica ", "name" => "clinicas_id", "join" => "clinicas,nombre"];
            $this->col[] = ["label" => "Número de solicitud", "name" => "nrosolicitud"];
            $this->col[] = ["label" => "Médico Solicitante", "name" => "medicos_id", "join" => "medicos,nombremedico"];
            $this->col[] = ["label" => "Estado solicitud", "name" => "estado_solicitud_id", "join" => "estado_solicitud,estado"];
            $this->col[] = ["label" => "Patología", "name" => "patologia", "join" => "patologias,nombre"];
            $this->col[] = ["label" => "Obra social", "name" => "obra_social"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
            $this->form[] = ['label' => 'Nombre y Apellido Afiliado', 'name' => 'afiliados_id', 'type' => 'datamodal', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datamodal_table' => 'afiliados', 'datamodal_columns' => 'apeynombres,nroAfiliado,documento,sexo,localidad', 'datamodal_select_to' => 'nroAfiliado:nroAfiliado,obra_social:obra_social', 'datamodal_size' => 'large', 'value' => $this->afiliadoID()];
            $this->form[] = ['label' => 'Nro de Afiliado', 'name' => 'nroAfiliado', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->nroAfiliado()];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'value'=> $edad];
            $this->form[] = ['label' => 'Número de Solicitud', 'name' => 'nrosolicitud', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'required' => true, 'readonly' => 'true', 'value' => 'APOS-MED-' . date('Ydm'). '-' . rand(0, 9999)];
            $this->form[] = ['label' => 'Institución', 'name' => 'clinicas_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'clinicas,nombre', 'required' => true];
            $this->form[] = ['label' => 'Médico Solicitante', 'name' => 'medicos_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'medicos,nombremedico', 'required' => true, 'value' => $IDMEDICO];
			$this->form[] = ['label'=>'Tel Medico','name'=>'tel_medico','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Elegir zona de retiro','name'=>'zona_residencia','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'dataenum' => 'Norte;Sur;Este;Oeste;Centro;Chamical;Chilecito;Famatina;Villa Unión'];
			$this->form[] = ['label'=>'Provincia','name'=>'provincia','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value' => 'La Rioja'];
			$this->form[] = ['label'=>'Tel Afiliado','name'=>'tel_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Email Afiliado','name'=>'email','type'=>'email','validation'=>'min:1|max:255|email|unique:pedido_medicamento','width'=>'col-sm-10','placeholder'=>'Introduce una dirección de correo electrónico válida'];
            $this->form[] = ['label' => 'Fecha Receta', 'name' => 'fecha_receta', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-10', 'value' => date('Y-m-d')];
			$this->form[] = ['label'=>'Receta prolongada','name'=>'postdatada','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10' , 'datatable' => 'postdatada,cantidad'];
			$this->form[] = ['label' => 'Estado Solicitud', 'name' => 'estado_solicitud_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'required' => true, 'datatable' => 'estado_solicitud,estado', 'value' => 8, 'disabled' => adminPrivilegeId()];
			$this->form[] = ['label' => 'Discapacidad', 'name' => 'discapacidad', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'required' => true, 'dataenum' => 'Si;No'];
			$this->form[] = ['label'=>'Patologia','name'=>'patologia','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'datatable' => 'patologias,nombre', 'value'=> $patologia];
			$this->form[] = ['label'=>'Obra Social','name'=>'obra_social','type'=>'hidden', 'value'=> 'APOS'];
			
			$columns = [];
			$columns[] = ['label'=>'Medicamento','name'=>'articuloZafiro_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados_articulos','datamodal_columns'=>'des_articulo,presentacion','datamodal_size'=>'large'];
			$columns[] = ['label'=>'Cantidad','name'=>'cantidad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];

			$this->form[] = ['label'=>'Detalles de la solicitud', 'name'=>'pedido_medicamento_detail', 'type'=>'child','table'=>'pedido_medicamento_detail', 'foreign_key'=>'pedido_medicamento_id', 'columns'=>$columns, 'width'=>'col-sm-10','required'=>true];
            $this->form[] = ['label' => 'Observaciones', 'name' => 'observaciones', 'type' => 'textarea', 'validation' => 'min:1|max:255', 'width' => 'col-sm-10'];
			$this->form[] = ['label'=>'Diagnostico','name'=>'diagnostico','type'=>'textarea','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			


			$this->form[] = ['label'=>'Archivo','name'=>'archivo','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo2','name'=>'archivo2','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo3','name'=>'archivo3','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo4','name'=>'archivo4','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"afiliados,id"];
			//$this->form[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Zona Residencia","name"=>"zona_residencia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Afiliado","name"=>"tel_afiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Email","name"=>"email","type"=>"email","required"=>TRUE,"validation"=>"required|min:1|max:255|email|unique:pedido_medicamento","placeholder"=>"Introduce una dirección de correo electrónico válida"];
			//$this->form[] = ["label"=>"Fecha Receta","name"=>"fecha_receta","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Postdatada","name"=>"postdatada","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Fecha Vencimiento","name"=>"fecha_vencimiento","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Tel Medico","name"=>"tel_medico","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Stamp User","name"=>"stamp_user","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Discapacidad","name"=>"discapacidad","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo","name"=>"archivo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo2","name"=>"archivo2","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo3","name"=>"archivo3","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo4","name"=>"archivo4","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Obra Social","name"=>"obra_social","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Provincia","name"=>"provincia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Patologia","name"=>"patologia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Diagnostico","name"=>"diagnostico","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Ant Postdatada","name"=>"ant_postdatada","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Ant Est Sol","name"=>"ant_est_sol","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Renovaciones","name"=>"renovaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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

		$PRIVILEGIO = CRUDBooster::myPrivilegeId();
	        $this->sub_module = array();
			$this->sub_module[] = ['label'=>'Enviar pedido', 'path'=>'cotizacion_convenio_1/add/?id[]=[id]','foreign_key'=>'pedido_medicamento_id','button_color'=>'success','button_icon'=>'fa fa-shopping-cart', 'showIf'=> $PRIVILEGIO == 1];


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
			$this->addaction[] = ['label'=>'Autorizar','url'=>CRUDBooster::mainpath('set-status/4/[id]'),'icon'=>'fa fa-check','color'=>'success','showIf'=>"[estado_solicitud_id] == 8", 'confirmation'=>true];
			$this->addaction[] = ['label'=>'Rechazar','url'=>CRUDBooster::mainpath('set-status/5/[id]'),'icon'=>'fa fa-check','color'=>'danger','showIf'=>"[estado_solicitud_id] == 8", 'confirmation'=>true];


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
			$this->script_js = "

		
			$(document).ready(function () {
				console.log('EJECUTANDO');
				var medicamentos = " . $medicamento . ";

				console.log(medicamentos);
				let table = document.getElementById('table-detallesdelasolicitud');
				var rowCount = table.rows.length; // Obtén el número de filas en la tabla

				if (medicamentos.length > 1) {
					// Si hay más de una fila de medicamentos, oculta la fila con la clase 'trNull'
					$('.trNull').hide();
				} else {
					// Si no, muestra la fila con la clase 'trNull'
					$('.trNull').show();
				}
				
				for (var i = 0; i < medicamentos.length; i++) {
					let medicamento = medicamentos[i];
			
					// Create a new row
					let row = table.insertRow();
			
					// Add the td elements for each column in the row
					var td1 = document.createElement('td');
					td1.className = 'articuloZafiro_id';
					var label = document.createElement('span');
					label.className = 'td-label';
					label.textContent = medicamento.des_articulo;
					var input1 = document.createElement('input');
					input1.type = 'hidden';
					input1.name = 'detallesdelasolicitud-articuloZafiro_id[]';
					input1.value = medicamento.articuloZafiro_id;
			
					var td2 = document.createElement('td');
					td2.className = 'cantidad';
					td2.textContent = medicamento.cantidad;
					var input2 = document.createElement('input');
					input2.type = 'hidden';
					input2.name = 'detallesdelasolicitud-cantidad[]';
					input2.value = medicamento.cantidad;
			
					var td3 = document.createElement('td');
					var editLink = document.createElement('a');
					editLink.href = '#panel-form-detallesdelasolicitud';
					editLink.onclick = function () { editRowdetallesdelasolicitud(this); };
					editLink.className = 'btn btn-warning btn-xs';
					var editIcon = document.createElement('i');
					editIcon.className = 'fa fa-pencil';
					editLink.appendChild(editIcon);
			
					var deleteLink = document.createElement('a');
					deleteLink.href = 'javascript:void(0)';
					deleteLink.onclick = function () { deleteRowdetallesdelasolicitud(this); };
					deleteLink.className = 'btn btn-danger btn-xs';
					var deleteIcon = document.createElement('i');
					deleteIcon.className = 'fa fa-trash';
					deleteLink.appendChild(deleteIcon);
			
					td1.appendChild(label);
					td1.appendChild(input1);
					td2.appendChild(input2);
					td3.appendChild(editLink);
					td3.appendChild(document.createTextNode(' '));
					td3.appendChild(deleteLink);
			
					// Append the td elements to the new row
					row.appendChild(td1);
					row.appendChild(td2);
					row.appendChild(td3);
				}
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

			if($status == 4){
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue autorizada con éxito!","info");
			}
			if($status == 5){
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue anulada con éxito!","info");
			}
		 }


	    //By the way, you can still create your own method in here... :) 


	}
