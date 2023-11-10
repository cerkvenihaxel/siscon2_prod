<?php namespace App\Http\Controllers;
    
	use App\Models\PedidoC;
	use App\Models\PedidoMedicamento;
	use App\Models\CotizacionConvenio;
	use App\Models\CotizacionConvenioDetail;
	use App\Models\ArticulosZafiro;
	use App\Models\LinPedido;
	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminCotizacionConvenio1Controller extends \crocodicstudio\crudbooster\controllers\CBController {

		function getDatos(){
			$pedido_medicamento_id = $_GET['id'];
			$pedido = DB::table('pedido_medicamento')->where('id', $pedido_medicamento_id)->get();
			
			return $pedido;
		}

		function getMedicamentos(){
			$pedido_medicamento_detail = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $_GET['id'])->get();
			
			$articuloZafiro = [];
			foreach($pedido_medicamento_detail as $k => $pedido){
				$articuloZafiro[$k] = DB::table('articulosZafiro')->where('id', $pedido->articuloZafiro_id)->get();
				$id_articulo = DB::table('articulosZafiro')->where('id', $pedido->articuloZafiro_id)->value('id_articulo');
				$articuloZafiro[$k]['cantidad'] = $pedido->cantidad;
				
				if(empty(DB::table('banda_descuentos')->where('id_articulo', $id_articulo)->value('banda_descuento'))){
					$articuloZafiro[$k]['banda_descuento'] = '';
				
				}
				else{
					$articuloZafiro[$k]['banda_descuento'] = DB::table('banda_descuentos')->where('id_articulo', $id_articulo)->value('banda_descuento');
				}

				if(empty(DB::table('banda_descuentos')->where('id_articulo', $id_articulo)->value('laboratorio')))
					$articuloZafiro[$k]['laboratorio'] = '';
				else
					$articuloZafiro[$k]['laboratorio'] = DB::table('banda_descuentos')->where('id_articulo', $id_articulo)->value('laboratorio');

			}
			

			return $articuloZafiro;
		}

		private function generatePedidoNumber()
        {
            $lastPedido = PedidoC::latest()->first();

            if ($lastPedido) {
                $lastNumber = substr($lastPedido->id_pedido, 7); // Suponiendo que el número de pedido siempre comienza con "PC-"
                $newNumber = str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT); // Incrementa el número y rellena con ceros a la izquierda
            } else {
                $newNumber = '00000001'; // Si no hay pedidos anteriores, comienza desde el número 1
            }

            return 'PE0090-' . $newNumber;
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
			$this->button_add = true;
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
			$this->col[] = ["label"=>"Fecha de creación","name"=>"created_at"];
			$this->col[] = ["label"=>"Nombre Afiliado","name"=>"nombreyapellido"];
			$this->col[] = ["label"=>"Nro Afiliado","name"=>"nroAfiliado"];
			$this->col[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud"];
			$this->col[] = ["label"=>"Estado solicitud", "name"=>"estado_solicitud_id", "join"=>"estado_solicitud,estado"];
			$this->col[] = ["label"=>"Estado pedido", "name"=>"estado_pedido_id", "join"=>"estado_pedido,estado"];
			$this->col[] = ["label"=>"Número pedido", "name"=>"id_pedido"];
			$this->col[] = ["label"=>"Nro remito" , "name"=>"nro_remito"];
			$this->col[] = ["label"=>"Punto retiro", "name"=>"punto_retiro_id", "join"=>"punto_retiro,nombre"];
			# END COLUMNS DO NOT REMOVE THIS LINE
			$pedido_medicamento = $this->getDatos();
			$medicamento = $this->getMedicamentos();
			$myEmail = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');

			$nombreyapellido = DB::table('afiliados')->where('nroAfiliado', $pedido_medicamento[0]->nroAfiliado)->value('apeynombres');
			$documento = DB::table('afiliados')->where('id', $pedido_medicamento[0]->afiliados_id)->value('documento');
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Nombre y apellido','name'=>'nombreyapellido','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$nombreyapellido];
			$this->form[] = ['label'=>'Documento','name'=>'documento','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10','value'=>$documento];
			$this->form[] = ['label'=>'Nro Afiliado','name'=>'nroAfiliado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->nroAfiliado];
			$this->form[] = ['label'=>'Edad','name'=>'edad','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->edad];
			$this->form[] = ['label'=>'Nro solicitud','name'=>'nrosolicitud','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->nrosolicitud];
			$this->form[] = ['label'=>'Clinicas Id','name'=>'clinicas_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'clinicas,nombre', 'value'=>$pedido_medicamento[0]->clinicas_id];
			$this->form[] = ['label'=>'Medicos Id','name'=>'medicos_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'medicos,nombremedico', 'value'=>$pedido_medicamento[0]->medicos_id];
			$this->form[] = ['label'=>'Tel Afiliado','name'=>'tel_afiliado','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->tel_afiliado];
			$this->form[] = ['label'=>'Zona Residencia','name'=>'zona_residencia','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->zona_residencia];
			$this->form[] = ['label'=>'Punto Retiro','name'=>'punto_retiro_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'punto_retiro,nombre'];
			
			//HIDDEN 
			$this->form[] = ['label'=>'Tel Medico','name'=>'tel_medico','type'=>'hidden','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$pedido_medicamento[0]->tel_medico];
			$this->form[] = ['label'=>'Email','name'=>'email','type'=>'hidden','value'=>$pedido_medicamento[0]->email];
			$this->form[] = ['label'=>'Fecha Receta','name'=>'fecha_receta','type'=>'hidden','value'=>$pedido_medicamento[0]->fecha_receta];
			$this->form[] = ['label'=>'Postdatada','name'=>'postdatada','type'=>'hidden','value'=>$pedido_medicamento[0]->postdatada];
			$this->form[] = ['label'=>'Estado solicitud', 'name'=>'estado_solicitud_id', 'type'=>'hidden', 'value'=>11];
//			$this->form[] = ['label'=>'Estado pedido', 'name'=>'estado_pedido_id', 'type'=>'hidden', 'value'=>3];
			$this->form[] = ['label'=>'Proveedor', 'name'=>'proveedor', 'type'=>'hidden', 'value'=>'Global Médica'];
			$this->form[] = ['label'=>'Stamp User', 'name'=>'stamp_user', 'type'=>'hidden', 'value'=>$myEmail];
			$this->form[] = ['label'=>'Discapacidad', 'name'=>'discapacidad', 'type'=>'hidden', 'value'=>$pedido_medicamento[0]->discapacidad];
			$this->form[] = ['label'=>'Pedido ID', 'name'=>'id_pedido', 'type'=>'hidden', 'value'=>$this->generatePedidoNumber()];
			
			//END HIDDEN FORMS


			$columns = [];
			$columns[] = ['label'=>'Articulo','name'=>'articuloZafiro_id','type'=>'text','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'readonly'=>'true'];
			$columns[] = ['label'=>'Presentacion','name'=>'presentacion', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Laboratorio', 'name'=>'laboratorio', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Precio', 'name'=>'precio', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Cantidad', 'name'=>'cantidad', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Descuento', 'name'=>'descuento', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$columns[] = ['label'=>'Total' , 'name'=>'total', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];

			$this->form[] = ['label'=>'Detalles de la solicitud','name'=>'cotizacion_convenio_detail','type'=>'child','columns'=>$columns,'table'=>'cotizacion_convenio_detail','foreign_key'=>'cotizacion_convenio_id', 'required' => true];
			
			$this->form[] = ['label'=>'Archivo','name'=>'archivo','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo2','name'=>'archivo2','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo3','name'=>'archivo3','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo4','name'=>'archivo4','type'=>'upload','validation'=>'min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Custom Field','name'=>'custom_field','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
			$this->addaction[] = ['label'=>'Imprimir pedido', 'url'=>'/generarPDF_convenio/[id]','button_color'=>'warning','button_icon'=>'fa fa-print', 'target'=>'_blank'];




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
			$this->index_statistic[] = ['label'=>'Total de pedidos', 'count'=>DB::table('pedido_medicamento')->count(), 'icon'=>'fa fa-inbox', 'color'=>'green'];
			$this->index_statistic[] = ['label'=>'Artritis', 'count'=>DB::table('pedido_medicamento')->where('patologia', 1)->count(), 'color'=>'red'];
			$this->index_statistic[] = ['label'=>'Diabetes', 'count'=>DB::table('pedido_medicamento')->where('patologia', 2)->count(),  'color'=>'blue'];
			$this->index_statistic[] = ['label'=>'Fibrosis', 'count'=>DB::table('pedido_medicamento')->where('patologia', 3)->count(),  'color'=>'red'];
			$this->index_statistic[] = ['label'=>'Hemodiálisis', 'count'=>DB::table('pedido_medicamento')->where('patologia', 7)->count(),  'color'=>'red'];
			$this->index_statistic[] = ['label'=>'Oncología', 'count'=>DB::table('pedido_medicamento')->where('patologia', 9)->count(),  'color'=>'blue'];



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
				var medicamentos = " . json_encode($medicamento) . ";
				console.log(medicamentos);
				let table = document.getElementById('table-detallesdelasolicitud');

				if(table){

				while (table.rows.length > 0) {
					table.deleteRow(0);
				}}

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

					var td0 = document.createElement('td');
					td0.className = 'articuloZafiro_id';
					td0.textContent = medicamento[0].id;
					var input0 = document.createElement('input');
					input0.type = 'hidden';
					input0.name = 'detallesdelasolicitud-articuloZafiro_id[]';
					input0.value = medicamento[0].id;

			
					// Add the td elements for each column in the row
					var td1 = document.createElement('td');
					td1.className = 'presentacion';
					var label = document.createElement('span');
					label.className = 'td-label';
					label.textContent = medicamento[0].presentacion_completa;
					var input1 = document.createElement('input');
					input1.type = 'hidden';
					input1.name = 'detallesdelasolicitud-presentacion[]';
					input1.value = medicamento[0].presentacion_completa;
			
					var td2 = document.createElement('td');
					td2.className = 'laboratorio';
					td2.textContent = medicamento.laboratorio;
					var input2 = document.createElement('input');
					input2.type = 'hidden';
					input2.name = 'detallesdelasolicitud-cantidad[]';
					input2.value = medicamento.laboratorio;

					var td3 = document.createElement('td');
					td3.className = 'precio';
					td3.textContent = medicamento[0].pcio_vta_siva.toFixed(2).replace(',', '.');
					var input3 = document.createElement('input');
					input3.type = 'hidden';
					input3.name = 'detallesdelasolicitud-precio[]';
					input3.value = medicamento[0].pcio_vta_siva.toFixed(2).replace(',', '.');

					var td4 = document.createElement('td');
					td4.className = 'cantidad';
					td4.textContent = medicamento.cantidad;
					var input4 = document.createElement('input');
					input4.type = 'hidden';
					input4.name = 'detallesdelasolicitud-cantidad[]';
					input4.value = medicamento.cantidad;

					var td5 = document.createElement('td');
					td5.className = 'descuento';
					td5.textContent = medicamento.banda_descuento;
					var input5 = document.createElement('input');
					input5.type = 'hidden';
					input5.name = 'detallesdelasolicitud-descuento[]';
					input5.value = medicamento.banda_descuento;

					//CALCULO DEL TOTAL 
					var total = 0;
					total = (medicamento[0].pcio_vta_siva * medicamento.cantidad) - (medicamento[0].pcio_vta_siva * medicamento.cantidad * medicamento.banda_descuento / 100);
					total = total.toFixed(2).replace(',', '.');

					var td6 = document.createElement('td');
					td6.className = 'total';
					td6.textContent = total;
					var input6 = document.createElement('input');
					input6.type = 'hidden';
					input6.name = 'detallesdelasolicitud-total[]';
					input6.value = total;
			
			
					var td7 = document.createElement('td');
					var editLink = document.createElement('a');
					editLink.href = '#panel-form-detallesdelasolicitud';
					editLink.onclick = function () { editRowdetallesdelasolicitud(this); };
					editLink.className = 'btn btn-warning btn-xs';
					var editIcon = document.createElement('i');
					editIcon.className = 'fa fa-pencil';
					editLink.appendChild(editIcon);
			
					var deleteLink = document.createElement('a');
					deleteLink.href = '#;';
					deleteLink.onclick = function () { deleteRowdetallesdelasolicitud(this); };
					deleteLink.className = 'btn btn-danger btn-xs';
					var deleteIcon = document.createElement('i');
					deleteIcon.className = 'fa fa-trash';
					deleteLink.appendChild(deleteIcon);
			
					// Append the td elements to the new row
					td0.appendChild(input0);
					td1.appendChild(label);
					td1.appendChild(input1);
					td2.appendChild(input2);
					td3.appendChild(input3);
					td4.appendChild(input4);
					td5.appendChild(input5);
					td6.appendChild(input6);
					td7.appendChild(editLink);
					td7.appendChild(document.createTextNode(' '));
					td7.appendChild(deleteLink);
			
					// Append the td elements to the new row
					row.appendChild(td0);
					row.appendChild(td1);
					row.appendChild(td2);
					row.appendChild(td3);
					row.appendChild(td4);
					row.appendChild(td5);
					row.appendChild(td6);
					row.appendChild(td7);

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
	        $nroSolicitud = DB::table('cotizacion_convenio')->where('id', $id)->value('nrosolicitud');
			PedidoMedicamento::where('nrosolicitud', $nroSolicitud)->update(['estado_solicitud_id' => 11]);
			//$this->enviarPedidoSingular($id);
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

		public function enviarPedidoSingular($id){

			DB::table('cotizacion_convenio')->where('id', $id)->update(['estado_pedido_id' => 5]);
			$nroSolicitud = DB::table('cotizacion_convenio')->where('id', $id)->value('nrosolicitud');
			$observaciones = CotizacionConvenio::where('id', $id)->value('observaciones');
			$nroAfiliado = CotizacionConvenio::where('id', $id)->value('nroAfiliado');
	
			$id_solicitud = $id;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');
			$id_empresa = 2;
			$id_pedido = $numero;
			$fecha_pedido = date('Y-m-d H:i:s');
			$origen_id_sucursal = 99;
			$id_punto = DB::table('cotizacion_convenio')->where('id', $id_solicitud)->value('punto_retiro_id');
			$id_cliente = DB::table('punto_retiro')->where('id', $id_punto)->value('id_cliente');
	
			$linpedidos = DB::table('cotizacion_convenio_detail')->where('cotizacion_convenio_id', $id_solicitud)->get();
	
			$lin_pedidos = [];
	
			foreach ($linpedidos as $key => $linpedido) {
	
				$articuloID = $linpedido->articuloZafiro_id;
				$numeroArticulo =  DB::table('articulosZafiro')->where('id_articulo', $articuloID)->value('id_articulo');
				$lin_pedidos[] = [
					'created_at' => $fecha_pedido,
					'updated_at' => $fecha_pedido,
					'id_pedido' => $id_pedido,
					'item' => $key+1,
					'id_articulo' => $numeroArticulo,
					'cantidad' => $linpedido->cantidad,
					'des_articulo' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('des_articulo'),
					'presentacion' => DB::table('articulosZafiro')->where('id_articulo', $numeroArticulo)->value('presentacion'),
					'pcio_vta_unisiva' => $linpedido->precio,
					'pcio_iva_comsiva' => $linpedido->total,
				];
			}
	
			$objeto = [
				[
					"id" => 1,
					"created_at" => $created_at,
					"updated_at" => $created_at,
					"id_empresa" => $id_empresa,
					"id_pedido" => $id_pedido,
					"estado_pedido" => "EM",
					"fecha_pedido" => $fecha_pedido,
					"_origen_id_sucursal" => $origen_id_sucursal,
					"id_cliente" => $id_cliente,
					"lin_pedido" => $lin_pedidos
	
				]
			];
	
	
			$pedido = new PedidoC();
			$pedido->created_at = $created_at;
			$pedido->updated_at = $updated_at;
			$pedido->id_empresa = $id_empresa;
			$pedido->id_pedido = $id_pedido;
			$pedido->fecha_pedido = $fecha_pedido;
			$pedido->estado_pedido = 'EM'; // Estado "EM" = "Enviado a Mostrador
			$pedido->_origen_id_sucursal = $origen_id_sucursal;
			$pedido->id_cliente = $id_cliente; // Valor va cambiando conforme el cliente
			$pedido->observaciones = $observaciones;
			$pedido->nrosolicitud = $nroSolicitud;
			$pedido->nroAfiliado = $nroAfiliado;
			$pedido->save();
	
	// Insertar en la tabla lin_pedido
			foreach ($objeto[0]['lin_pedido'] as $linpedido) {
				$linPedido = new LinPedido();
				$linPedido->created_at = $linpedido['created_at'];
				$linPedido->updated_at = $linpedido['updated_at'];
				$linPedido->id_pedido = $linpedido['id_pedido'];
				$linPedido->item = $linpedido['item'];
				$linPedido->id_articulo = $linpedido['id_articulo'];
				$linPedido->cantidad = $linpedido['cantidad'];
				$linPedido->des_articulo = $linpedido['des_articulo'];
				$linPedido->presentacion = $linpedido['presentacion'];
				$linPedido->pcio_vta_uni_siva = $linpedido['pcio_vta_unisiva'];
				$linPedido->pcio_com_uni_siva = $linpedido['pcio_iva_comsiva'];
				$linPedido->save();
			}
	
	
			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"El pedido fue cargado con éxito!","success");
	
		}



	    //By the way, you can still create your own method in here... :) 


	}
