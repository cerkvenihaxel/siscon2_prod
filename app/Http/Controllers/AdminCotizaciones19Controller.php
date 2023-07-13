<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminCotizaciones19Controller extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->table = "cotizaciones";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ['label'=>'Fecha de carga','name'=>'created_at'];
			$this->col[] = ['label'=>'Fecha de carga del médico','name'=>'fecha_solicitud'];
			$this->col[] = ["label"=>"Nombre y Apellido Afiliado","name"=>"afiliados_id","join"=>"afiliados,apeynombres"];
			$this->col[] = ["label"=>"Clínica","name"=>"clinicas_id","join"=>"clinicas,nombre"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Nro. Solicitud","name"=>"nrosolicitud"];
			$this->col[] = ["label"=>"Medico","name"=>"medicos_id","join"=>"medicos,nombremedico"];
			$this->col[] = ["label"=>"Estado Paciente","name"=>"estado_paciente_id","join"=>"estado_paciente,estado"];
			$this->col[] = ["label"=>"Estado Solicitud","name"=>"estado_solicitud_id","join"=>"estado_solicitud,estado"];
			$this->col[] = ["label"=>"Fecha Cirugia","name"=>"fecha_cirugia"];
			//$this->col[] =["label"=>"Necesidad", "name"=>"necesidad","join"=>"necesidad,necesidad"];
			$this->col[] = ["label"=>"Proveedor", "name"=>"proveedor"];
			$this->col[] = ["label"=>"Precio total", "name"=>"total"];
			$this->col[] = ["label"=>"Días transcurridos (desde la cotización)", "name"=>"(DATEDIFF(CURDATE(), created_at)) as dias_transcurridos2"];
			$this->col[] = ["label"=>"Días transcurridos (desde la carga) ", "name"=>"(DATEDIFF(CURDATE(), fecha_solicitud)) as dias_transcurridos"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			$url = $_GET['id'];
			$custom_element = view('articulosEntrantes')->render();

	function adminPrivilegeId(){

			$privilege = CRUDBooster::myPrivilegeId();
			if($privilege == 1 || $privilege == 17 || $privilege == 33 || $privilege == 34 || $privilege == 35 || $privilege == 28 || $privilege = 97 || $privilege = 56){
				return false;
			}else{
				return true;
			}
		}




			# START FORM DO NOT REMOVE THIS LINE

/*			$this->form = [];
			$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliados_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'afiliados','datamodal_columns'=>'apeynombres,documento,sexo,localidad','datamoda_columns_alias'=>'Nombre y Apellido, Documento, Sexo, Localidad','datamodal_size'=>'large', 'required'=>true];
			$this->form[] = ['label'=>'Clínica','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre'];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Estado Paciente','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,estado'];
			$this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,estado','value'=>2];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico'];
			$this->form[] = ['label'=>'Teléfono médico', 'name'=>'tel_medico', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Necesidad', 'name'=>'necesidad', 'type'=>'select2', 'validation'=>'required', 'width'=>'col-sm-10', 'datatable'=>'necesidad,necesidad'];
			//Solicitud autogenerada por el sistema

			$columns=[];
			$columns[] = ['label'=>'Artículos','name'=>'articulos_id','type'=>'datamodal', 'datamodal_table'=>'articulos', 'datamodal_columns'=>'des_articulo','datamodal_size'=>'large','datamodal_select_to'=>'marca:marca'];
			$columns[] = ['label'=> 'Cantidad', 'name'=>'cantidad', 'type'=>'number', 'validation'=>'required|integer|min:0'];
			$columns[] = ['label'=> 'Garantía (meses)', 'name'=>'garantia', 'type'=>'number', 'validation'=>'required|string|min:5|max:5000'];
			$columns[] = ['label'=> 'Precio', 'name'=>'precio', 'type'=>'number', 'validation'=>'required|integer|min:0'];
			$columns[] = ['label'=>'Procendencia', 'name'=>'procedencias_id', 'type'=>'select', 'validation'=>'required', 'width'=>'col-sm-10', 'datatable'=>'procedencias,procedencia'];

*/
			$AFILIADO = DB::table('entrantes')->where('id',$url)->value('afiliados_id');
			$CLINICA = DB::table('entrantes')->where('id',$url)->value('clinicas_id');


			$this->form = [];

			$this->form[] = ['label'=>'Número ID','name'=>'afiliados_id','type'=>'text','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'afiliados,apeynombres','datatable_ajax'=>false,'required'=>true, 'value'=>DB::table('entrantes')->where('id',$url)->value('afiliados_id'),'disabled'=>'disabled', 'readonly'=>true];
			$this->form[] = ['label'=>'Fecha de carga del médico', 'name'=>'fecha_solicitud', 'type'=>'text', 'validation'=>'required|date', 'width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id', $url)->value('created_at'), 'readonly'=>adminPrivilegeId() ];
			$this->form[] = ['label'=>'Nombre y Apellido Afiliado','name'=>'afiliadoName','type'=>'text','validation'=>'required|integer|min:0','width'=>'col-sm-10','required'=>true, 'value'=>DB::table('afiliados')->where('id',$AFILIADO)->value('apeynombres'),'disabled'=>'disabled', 'readonly'=>true];
            $this->form[] = ['label'=>'Documento','name'=>'documento','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly'=>true, 'value'=>DB::table('afiliados')->where('id',$AFILIADO)->value('documento')];

            $this->form[] = ['label'=>'Clínica','name'=>'clinicas_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre', 'value'=>DB::table('entrantes')->where('id',$url)->value('clinicas_id'), 'readonly'=>true, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('edad'), 'readonly'=>true];
			$this->form[] = ['label'=>'Telefono afiliado', 'name'=>'tel_afiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('tel_afiliado'),'readonly'=>true];
			$this->form[] = ['label'=>'Estado Paciente','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,estado', 'value'=>DB::table('entrantes')->where('id',$url)->value('estado_paciente_id'), 'readonly'=>true, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Estado Solicitud','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,estado','value'=>2, 'readonly'=>true, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('fecha_cirugia')];
			$this->form[] = ['label'=>'Médico Solicitante','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico', 'value'=>DB::table('entrantes')->where('id',$url)->value('medicos_id'), 'readonly'=>true, 'disabled'=>adminPrivilegeId()];
			$this->form[] = ['label'=>'Teléfono médico', 'name'=>'tel_medico', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('tel_medico'),'readonly'=>true];
			$this->form[] = ['label'=>'Número de Solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>DB::table('entrantes')->where('id',$url)->value('nrosolicitud'), 'readonly'=>true];

			$this->form[] = ['name'=>'custom_field','type'=>'custom','html'=>$custom_element,'width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Necesidad', 'name'=>'necesidad', 'type'=>'select2', 'validation'=>'required', 'width'=>'col-sm-10', 'datatable'=>'necesidad,necesidad'];
			//Solicitud autogenerada por el sistema


			$columns[] = ['label'=>'Artículos','name'=>'articulos_id','type'=>'datamodal', 'datamodal_table'=>'articulos', 'datamodal_columns'=>'des_articulo','datamodal_size'=>'large','datamodal_select_to'=>'marca:marca','required'=>true];
			$columns[] = ['label'=> 'Garantía (meses)', 'name'=>'garantia', 'type'=>'number', 'validation'=>'required|string|min:5|max:5000','required'=>true];

			$columns[] = ['label'=>'Precio Unitario','name'=>'precio_unitario','type'=>'text','validation'=>'required | gt:1', 'required'=>true, 'help'=>'Ingrese el precio unitario del artículo, si utiliza centavos, utilice el punto (.) como separador decimal'];
			$columns[] = ['label'=> 'Cantidad', 'name'=>'cantidad', 'type'=>'number', 'validation'=>'required|gt:1', 'required'=>true, 'help'=>'Ingrese la cantidad de artículos, al finalizar presione ENTER'];
			// SUB TOTAL
			$columns[] = ['label'=> 'Subtotal', 'name'=>'precio', 'type'=>'number', 'validation'=>'required|numeric|gt:0','required'=>true, 'formula'=>"[precio_unitario] * [cantidad]", 'readonly'=>adminPrivilegeId()];
			$columns[] = ['label'=>'Procendencia', 'name'=>'procedencias_id', 'type'=>'select', 'validation'=>'required', 'width'=>'col-sm-9', 'datatable'=>'procedencias,procedencia','required'=>true];
			$columns[] =['label'=>'Marca', 'name'=>'marca', 'type'=>'text', 'validation'=>'required', 'width'=>'col-sm-10','required'=>true];


			$this->form[] = ['label'=>'Detalles de la solicitud', 'name'=>'cotizaciones_detail', 'type'=>'child','table'=>'cotizaciones_detail', 'foreign_key'=>'entrantes_id', 'columns'=>$columns, 'width'=>'col-sm-10'];

			$this->form[] = ['label'=>'Observacion','name'=>'observaciones','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];

			//$this->form[] = ['label'=>'Archivo', 'name'=>'archivo','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];

			$this->form[] = ['label'=>'Archivo 1', 'name'=>'archivo','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 2', 'name'=>'archivo2','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 3', 'name'=>'archivo3','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
			$this->form[] = ['label'=>'Archivo 4', 'name'=>'archivo4','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];



			$this->form[] =['label'=>'Proveedor', 'name'=>'proveedor','readonly'=>adminPrivilegeId(), 'type'=>'text', 'width'=>'col-sm-10', 'value'=>CRUDBooster::myName()];
			$this->form[] = ['label'=>'Total','name'=>'total','type'=>'text','validation'=>'required|gt:1','width'=>'col-sm-10','prefix'=>'$' , 'readonly'=>true];
/*			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","join"=>"afiliados,id"];
			$this->col[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","join"=>"clinicas,id"];
			$this->col[] = ["label"=>"Edad","name"=>"edad"];
			$this->col[] = ["label"=>"Estado Paciente Id","name"=>"estado_paciente_id","join"=>"estado_paciente,id"];
			$this->col[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","join"=>"estado_solicitud,id"];
			$this->col[] = ["label"=>"Fecha Cirugia","name"=>"fecha_cirugia"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Afiliados Id','name'=>'afiliados_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'afiliados,id'];
			$this->form[] = ['label'=>'Clinicas Id','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,id'];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Estado Paciente Id','name'=>'estado_paciente_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_paciente,id'];
			$this->form[] = ['label'=>'Estado Solicitud Id','name'=>'estado_solicitud_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'estado_solicitud,id'];
			$this->form[] = ['label'=>'Fecha Cirugia','name'=>'fecha_cirugia','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Medicos Id','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,id'];
			$this->form[] = ['label'=>'Nrosolicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
*/			//$this->form = [];
			//$this->form[] = ["label"=>"Afiliados Id","name"=>"afiliados_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"afiliados,id"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Estado Paciente Id","name"=>"estado_paciente_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_paciente,id"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Fecha Cirugia","name"=>"fecha_cirugia","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
//			# OLD END FORM


			$allProveedores = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 30, 31, 32];

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

            $boolAudit = CRUDBooster::myPrivilegeId() == 17 && CRUDBooster::myId() == 69 ? true : false;

		    $this->sub_module[] = ['label'=>'Adjudicar', 'path'=>'adjudicaciones/add/?id[]=[id]','foreign_key'=>'entrantes_id','button_color'=>'warning','button_icon'=>'fa fa-check-circle-o','parent_columns'=>'nrosolicitud,fecha_cirugia,medicos_id,observaciones', 'showIf'=>$boolAudit];


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

			$this->addaction[] = ['label'=>'ANULAR','url'=>CRUDBooster::mainpath('set-status/5/[id]'),'icon'=>'fa fa-times','color'=>'danger','showIf'=>"$PRIVILEGIO== 1 || $PRIVILEGIO == 17", 'confirmation'=>true];
			$this->addaction[] = ['label'=>'Ver comparativa', 'url'=>('/comparativa?id=[id]'),'icon'=>'fa fa-search','color'=>'primary','showIf'=>"$PRIVILEGIO== 1 || $PRIVILEGIO == 17", 'confirmation'=>true];


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

			$(function() {

				setInterval(function() {
				var total = 0;
				$('#table-detallesdelasolicitud tbody .precio').each(function() {
					var amount = parseFloat($(this).text());
					total += amount;
				})
				total = total.toFixed(2);
				$('#total').val(total);

			}, 500);

		});

			";;


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
		$privilege = CRUDBooster::myPrivilegeId();

		if($privilege != 17 && $privilege != 1 && $privilege != 3 && $privilege !=37 && $privilege != 34  && $privilege != 35 && $privilege != 33){
		$query->where('proveedor',CRUDBooster::myName());
		}
		else if( $privilege == 37 || $privilege == 34 || $privilege == 35 || $privilege == 33){
			$query->where('stamp_user',CRUDBooster::myName());}

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
//			DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->update(['estado_solicitud_id'=>2]);
			$postdata['afiliados_id'] = DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->value('afiliados_id');
			$postdata['estado_solicitud_id'] = 2;
			$postdata['clinicas_id'] = DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->value('clinicas_id');
			$postdata['medicos_id'] = DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->value('medicos_id');
			$postdata['estado_paciente_id'] = DB::table('entrantes')->where('nrosolicitud',$postdata['nrosolicitud'])->value('estado_paciente_id');

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after add public static function called
	    | ----------------------------------------------------------------------
	    | @id = last insert id
	    |
	    */
	    public function hook_after_add($id) {

			$afiliadoId = DB::table('cotizaciones')->where('id',$id)->value('afiliados_id');
			$nroSolicitud = DB::table('cotizaciones')->where('id',$id)->value('nrosolicitud');
//			DB::table('entrantes')->where('nrosolicitud',$nroSolicitud)->update(['estado_solicitud_id'=>2]);
			DB::table('cotizaciones')->where('id',$id)->update(['afiliadoName'=>DB::table('afiliados')->where('id',$afiliadoId)->value('apeynombres')]);

			$stampName = CRUDBooster::myName();

			DB::table('cotizaciones')->where('id', $id)->update(array('stamp_user' => $stampName));

			$proveedorName = DB::table('cotizaciones')->where('id',$id)->value('proveedor');
			$proveedorCMS = DB::table('cms_users')->where('name', $proveedorName)->value('id');

			$medicoId = DB::table('cotizaciones')->where('id',$id)->value('medicos_id');
			$medicoName = DB::table('medicos')->where('id', $medicoId)->value('nombremedico');
			$medicoCMS = DB::table('cms_users')->where('name', $medicoName)->value('id');


			$config['content'] = "$proveedorName ha ingresado una nueva cotización";
			$config['to'] = CRUDBooster::adminPath('cotizaciones19/detail/'.$id);
			$config['id_cms_users'] = [1, $proveedorCMS, $medicoCMS, 178, 11, 32, 92, 8];

			$present['content'] = "NUEVA NOTIFICACIÓN ℹ️ Se habilitó el módulo de PRESENTACIÓN FINAL por favor revise el manual para cargar sus solicitudes previamente AUTORIZADAS.";
			$present['to'] = url('pdf/asistProveedor.pdf');
			//$present['id_cms_users'] = $allProveedores;
			CRUDBooster::sendNotification($config);
			//CRUDBooster::sendNotification($present);

	        //Your code here
//					$postdata['clinicas_id'] = DB::table('entrantes')->where('id',$url)->value('clinicas_id');
//			$postdata['medicos_id'] = DB::table('entrantes')->where('id',$url)->value('medicos_id');
//			$postdata['estado_paciente_id'] = DB::table('entrantes')->where('id',$url)->value('estado_paciente_id');

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
			$afiliadoId = DB::table('cotizaciones')->where('id',$id)->value('afiliados_id');
			DB::table('cotizaciones')->where('id',$id)->update(['afiliadoName'=>DB::table('afiliados')->where('id',$afiliadoId)->value('apeynombres')]);

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

		DB::table('cotizaciones')->where('id', $id)->delete();
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


	public function getSetStatus($status,$id) {
			DB::table('cotizaciones')->where('id',$id)->update(['estado_solicitud_id'=> $status]);
			//This will redirect back and gives a message

			$proveedorName = DB::table('cotizaciones')->where('nrosolicitud',Request::input('nrosolicitud'))->value('proveedor');
			$config['content'] = "Hola $proveedorName, su solicitud fue ANULADA. Revise el estado en sus solicitudes cotizadas.";


			$config['to'] = CRUDBooster::adminPath('cotizaciones19?q='.Request::input('nrosolicitud'));
			$id = DB::table('cms_users')->where('name',$proveedorName)->value('id');
			$config['id_cms_users'] = [$id];

			CRUDBooster::sendNotification($config);


			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"La solicitud fue anulada con éxito!","info");
		 }


	}
