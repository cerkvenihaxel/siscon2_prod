<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminValidacionFarmaciaCompleto1Controller extends \crocodicstudio\crudbooster\controllers\CBController {


        function countSolicitudes($i){
            $total = DB::table('cotizacion_convenio')->where('estado_solicitud_id', $i)->count();
            return $total;
        }

        function countSolicitudesTotal(){
            $total = DB::table('cotizacion_convenio')->count();
            return $total;
        }

        function getState($q){
            if($q == 'ENTRANTE'){
                return 1;
            }
            if($q == 'APROBADA'){
                return 8;
            }
            if($q == 'AUTORIZADA'){
                return 4;
            }
            if($q == 'PROCESADO'){
                return [11];
            }
            if($q == 'ENTREGADO'){
                return [13];
            }
            if($q == 'RECHAZADO'){
                return [10, 14];
            }
            if($q == 'PENDIENTE'){
                return [17];
            }
            else{
                return 0;
            }
        }

        function returnState($value){
            if ($value != 0) {
                return "<h4 style='text-align: left; padding-left: 1rem;'> Solicitudes : ". $_GET['q'] ."   - Cantidad de resultados: " . DB::table('cotizacion_convenio')->whereIn('estado_solicitud_id', $this->getState($_GET['q']))->count() . "</h4>";
            }
            else {
                return  "<h4 style='text-align: left; padding-left: 1rem;'> Solicitudes : VER TODAS   - Cantidad de resultados: " . DB::table('cotizacion_convenio')->count() . "</h4>";

            }
        }
	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "cotizacion_convenio";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Fecha Carga","name"=>"created_at"];
                        $this->col[] = ["label"=>"Actualizado en ", "name"=>"updated_at"];
			$this->col[] = ["label"=>"Nombre y apellido","name"=>"nombreyapellido"];
			$this->col[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado"];
			$this->col[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud"];
            $this->col[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","join"=>"clinicas,nombre"];
            $this->col[] = ["label"=>"Medicos Id","name"=>"medicos_id","join"=>"medicos,nombremedico"];
            $this->col[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","join"=>"estado_solicitud,estado"];
            $this->col[] = ["label"=>"Estado Pedido Id","name"=>"estado_pedido_id","join"=>"estado_pedido,estado"];
            $this->col[] = ["label"=>"Zona Residencia","name"=>"zona_residencia"];
            $this->col[] = ["label"=>"Punto Retiro","name"=>"punto_retiro_id","join"=>"punto_retiro,nombre"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Nombre y apellido','name'=>'nombreyapellido','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
			$this->form[] = ['label'=>'Documento','name'=>'documento','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
			$this->form[] = ['label'=>'NroAfiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
			$this->form[] = ['label'=>'Nrosolicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
			$this->form[] = ['label'=>'Zona Residencia','name'=>'zona_residencia','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
			$this->form[] = ['label'=>'Tel Afiliado','name'=>'tel_afiliado','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Email','name'=>'email','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10','placeholder'=>'Introduce una dirección de correo electrónico válida'];
			$this->form[] = ['label'=>'Fecha Entrega','name'=>'fecha_entrega','type'=>'date','validation'=>'required|date','width'=>'col-sm-10', 'value' => date('Y-m-d'), 'readonly' => true];

            $columns = [];
            $columns[] = ['label'=>'Presentacion','name'=>'presentacion', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'readonly' => true];
            $columns[] = ['label'=>'Cantidad Aprobada', 'name'=>'cantidad', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'readonly' => true];
            $columns[] = ['label'=>'Cantidad Entregada', 'name'=>'cantidad_entregada', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Cantidad Pendiente', 'name'=>'cantidad_pendiente', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'readonly' => true];
            $columns[] = ['label'=>'Observación', 'name'=>'observacion', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];


            $this->form[] = ['label'=>'Detalles de la solicitud','name'=>'cotizacion_convenio_detail','type'=>'child','columns'=>$columns,'table'=>'cotizacion_convenio_detail','foreign_key'=>'cotizacion_convenio_id', 'required' => true];


            $this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];

            # END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"NumeroID","name"=>"numeroID","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Fecha Carga","name"=>"fecha_carga","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Nombreyapellido","name"=>"nombreyapellido","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Documento","name"=>"documento","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Zona Residencia","name"=>"zona_residencia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Afiliado","name"=>"tel_afiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Email","name"=>"email","type"=>"email","required"=>TRUE,"validation"=>"required|min:1|max:255|email|unique:cotizacion_convenio","placeholder"=>"Introduce una dirección de correo electrónico válida"];
			//$this->form[] = ["label"=>"Fecha Receta","name"=>"fecha_receta","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Postdatada","name"=>"postdatada","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Fecha Vencimiento","name"=>"fecha_vencimiento","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Fecha Entrega","name"=>"fecha_entrega","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Estado Pedido Id","name"=>"estado_pedido_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_pedido,id"];
			//$this->form[] = ["label"=>"Punto Retiro Id","name"=>"punto_retiro_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"punto_retiro,id"];
			//$this->form[] = ["label"=>"Tel Medico","name"=>"tel_medico","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Proveedor","name"=>"proveedor","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Stamp User","name"=>"stamp_user","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Discapacidad","name"=>"discapacidad","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo","name"=>"archivo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo2","name"=>"archivo2","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo3","name"=>"archivo3","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo4","name"=>"archivo4","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Direccion Retiro","name"=>"direccion_retiro","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Zona Retiro","name"=>"zona_retiro","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Localidad Retiro","name"=>"localidad_retiro","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Retiro","name"=>"tel_retiro","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Custom Field","name"=>"custom_field","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Pedido","name"=>"id_pedido","type"=>"select2","required"=>TRUE,"validation"=>"required|min:1|max:255","datatable"=>"pedido,id"];
			//$this->form[] = ["label"=>"Nro Factura","name"=>"nro_factura","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Nro Remito","name"=>"nro_remito","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
            $this->addaction[] = ['label'=>'Imprimir acuse de recibo', 'url'=>'/generarPDF_farmacia/[id]','color'=>'warning','icon'=>'fa fa-check-square', 'target'=>'_blank', 'showIf'=>'[estado_solicitud_id] == 13'];
	        $this->addaction[] = ['label'=>'Validar', 'url'=>'/admin/validacion_farmacia_completo/edit/[id]','color'=>'success','icon'=>'fa fa-check-square', 'target'=>'_blank', 'parent_columns'=>'estado_solicitud_id', 'showIf'=>'[estado_solicitud_id] != 13'];
            $this->addaction[] = ['label'=>'Imprimir pedido', 'url'=>'/printPDF_convenio/[id]','color'=>'warning','icon'=>'fa fa-check-square', 'target'=>'_blank', 'showIf'=>'[estado_solicitud_id] == 11'];
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
	            var cantidadAprobadaInput = document.getElementById('detallesdelasolicitudcantidad');
             var cantidadEntregadaInput = document.getElementById('detallesdelasolicitudcantidad_entregada');
            var cantidadPendienteInput = document.getElementById('detallesdelasolicitudcantidad_pendiente');

    // Agrega un evento de cambio al campo de cantidad entregada
    cantidadEntregadaInput.addEventListener('input', function() {
        // Convierte los valores a números
        var cantidadAprobada = parseFloat(cantidadAprobadaInput.value) || 0;
        var cantidadEntregada = parseFloat(cantidadEntregadaInput.value) || 0;

        // Calcula la cantidad pendiente
        var cantidadPendiente = cantidadAprobada - cantidadEntregada;

        // Actualiza el campo de cantidad pendiente
        cantidadPendienteInput.value = cantidadPendiente;
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
            $this->pre_index_html = "<div class='row '>
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
            <button type='button' class='btn' style='background-color: #0d6aad !important; color: white !important;' onclick='window.location.href = \"?q=PROCESADO\"'>Por Validar (". $this->countSolicitudes(11) .")</button>
            <button type='button' class='btn' style='background-color: green !important; color: white !important;' onclick='window.location.href = \"?q=ENTREGADO\"'>Validadas | Entregadas (". $this->countSolicitudes(13) .")</button>
            <button type='button' class='btn' style='background-color: gold !important;' onclick='window.location.href = \"?q=PENDIENTE\"'>Pendiente(". $this->countSolicitudes(17) .")</button>
            <button type='button' class='btn' style='background-color: red !important; color: white !important;' onclick='window.location.href = \"?q=RECHAZADO\"'>Rechazadas (". $this->countSolicitudes(14) .")</button>
            <hr>
            </div>
            </div>
            </div>
           <div class='row'>
           ". $this->returnState($this->getState($_GET['q'])) ."
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
	        //Your code here
            $privilegio = CRUDBooster::myPrivilegeId();
            $user = DB::table('cms_users')->where('id',CRUDBooster::myId())->value('name');
            $punto_retiro = DB::table('punto_retiro')->where('nombre',$user)->value('id');

            if($privilegio == 45){
                //$query->where('estado_solicitud_id', 11);
                //$query->where('estado_pedido_id', 5);
                $query->where('punto_retiro_id', $punto_retiro);
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
            if(DB::table('cotizacion_convenio_detail')->where('cotizacion_convenio_id', $id)->where('cantidad_pendiente', '>', 0)->count() > 0) {
                DB::table('cotizacion_convenio')->where('id', $id)->update(['estado_solicitud_id' => 17, 'estado_pedido_id' => 7]);
            }
            else {
                DB::table('cotizacion_convenio')->where('id', $id)->update(['estado_solicitud_id' => 13, 'estado_pedido_id' => 1]);
            }

            $validacion = DB::table('cotizacion_convenio')->where('id', $id)->get();
            DB::table('validaciones_farmacias')->insert([
                'cotizacion_convenio_id' => $id,
                'farmacia_id' => CRUDBooster::myId(),
                'estado_solicitud_id' => $validacion[0]->estado_solicitud_id,
                'estado_pedido_id' => $validacion[0]->estado_pedido_id,
                'nro_solicitud' => $validacion[0]->nrosolicitud,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
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
