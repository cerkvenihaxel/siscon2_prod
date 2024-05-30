<?php namespace App\Http\Controllers;

	use Illuminate\Support\Facades\Cache;
    use Session;
	use Request;
	use DB;
	use CRUDBooster;

	use App\Models\PedidoC;
	use App\Models\LinPedido;
	use App\Models\PedidoMedicamento;
	use App\Models\PedidoMedicamentoDetail;
	use App\Models\CotizacionConvenio;
	use App\Models\CotizacionConvenioDetail;
	use App\Models\Afiliados;
	use App\Models\ArticulosZafiro;

	class AdminPedidoMasivoController extends \crocodicstudio\crudbooster\controllers\CBController {

        function getMedicamentos($ids){
            $pedido_medicamento_detail = DB::table('pedido_medicamento_detail')
                ->whereIn('pedido_medicamento_id', $ids)
                ->get()
                ->groupBy('articuloZafiro_id');


            $articuloZafiro = [];
            foreach($pedido_medicamento_detail as $k => $pedidos){

                $pedido = $pedidos->first();

                // $articuloZafiro[$k] = DB::table('articulosZafiro')->where('id_articulo','LIKE', '%'.$pedido->articuloZafiro_id .'%')->get();
				$articuloZafiro[$k] = DB::table('articulosZafiro')->where('id','=', $pedido->articuloZafiro_id)->get();
                // $id_articulo = DB::table('articulosZafiro')->where('id_articulo','LIKE', '%'.$pedido->articuloZafiro_id .'%')->value('id_articulo');
                $id_articulo = DB::table('articulosZafiro')->where('id','=', $pedido->articuloZafiro_id)->value('id_articulo');

                if($id_articulo == null ){
                    $articuloZafiro[$k] = DB::table('articulosZafiro')->where('id_articulo','LIKE', '%'.$pedido->articuloZafiro_id .'%')->get();
                    $id_articulo = DB::table('articulosZafiro')->where('id_articulo','LIKE', '%'.$pedido->articuloZafiro_id .'%')->value('id_articulo');
                }


                $articuloZafiro[$k]['cantidad'] = $pedidos->sum('cantidad');

                if(empty(DB::table('banda_descuentos')->where('id_articulo', '=' ,$id_articulo)->value('banda_descuento'))){
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
			$this->button_export = false;
			$this->table = "pedido_masivo";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Fecha de creación","name"=>"created_at"];
			$this->col[] = ["label"=>"Punto Retiro Id","name"=>"punto_retiro_id","join"=>"punto_retiro,nombre"];
			$this->col[] = ["label"=>"Usuario de Carga","name"=>"stamp_user"];
			$this->col[] = ["label"=>"Número pedido", "name"=>"id_pedido"];




			# END COLUMNS DO NOT REMOVE THIS LINE

            $Id = CRUDBooster::myId();
            $stampUser = DB::table('cms_users')->where('id', $Id)->value('email');

            $ids = Cache::get('ids_cache_key');
            if($ids !== null){
                $medicamentos = $this->getMedicamentos($ids);
            }
            else{
//                CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"No tiene seleccionado pedidos","warning");
            }





            # START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
            $this->form[] = ['label'=>'Punto Retiro','name'=>'punto_retiro_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'punto_retiro,nombre'];
			$this->form[] = ['label'=>'Pedido ID', 'name'=>'id_pedido', 'type'=>'hidden', 'value'=>$this->generatePedidoNumber()];
			$this->form[] = ['label'=>'Usuario de carga','name'=>'stamp_user','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'value'=>$stampUser, 'readonly'=>true];

            $columns = [];
            $columns[] = ['label'=>'Articulo','name'=>'articuloZafiro_id','type'=>'text','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'readonly'=>'true'];
            $columns[] = ['label'=>'Presentacion','name'=>'presentacion', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Laboratorio', 'name'=>'laboratorio', 'type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Precio', 'name'=>'precio', 'type'=>'number','validation'=>'required|min:0','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Cantidad', 'name'=>'cantidad', 'type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Descuento', 'name'=>'descuento', 'type'=>'number','validation'=>'required|min:0','width'=>'col-sm-10'];
            $columns[] = ['label'=>'Total' , 'name'=>'total', 'type'=>'number','validation'=>'required|min:0','width'=>'col-sm-10', 'disabled'=>'true'];

            $this->form[] = ['label'=>'Detalles de la solicitud','name'=>'pedido_masivo_detail','type'=>'child','columns'=>$columns,'table'=>'pedido_masivo_detail','foreign_key'=>'pedido_masivo_id', 'required' => true];


			$this->form[] = ['label'=>'Archivo','name'=>'archivo','type'=>'upload','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo2','name'=>'archivo2','type'=>'upload','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo3','name'=>'archivo3','type'=>'upload','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Archivo4','name'=>'archivo4','type'=>'upload','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Punto Retiro Id","name"=>"punto_retiro_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"punto_retiro,id"];
			//$this->form[] = ["label"=>"Tel Medico","name"=>"tel_medico","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Stamp User","name"=>"stamp_user","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
	        $this->script_js = "


			// Obtener los campos relevantes
			var precioInput = document.getElementById('detallesdelasolicitudprecio');
			var cantidadInput = document.getElementById('detallesdelasolicitudcantidad');
			var descuentoInput = document.getElementById('detallesdelasolicituddescuento');
			var totalInput = document.getElementById('detallesdelasolicitudtotal');

			// Agregar event listeners para detectar cambios en los campos
			precioInput.addEventListener('input', calcularTotal);
			cantidadInput.addEventListener('input', calcularTotal);
			descuentoInput.addEventListener('input', calcularTotal);

			function calcularTotal() {
				// Obtener los valores de precio, cantidad y descuento
				var precio = parseFloat(precioInput.value) || 0;
				var cantidad = parseFloat(cantidadInput.value) || 0;
				var descuento = parseFloat(descuentoInput.value) || 0;

				// Calcular el total
				var total = (precio * cantidad) - (precio * cantidad * (descuento / 100));

				// Mostrar el total en el campo correspondiente
				totalInput.value = total.toFixed(2);
			}

			$(document).ready(function () {
				var medicamentos = " . json_encode($medicamentos) . ";
				console.log('Medicamentos: ');
				console.log(medicamentos);
				let table = document.getElementById('table-detallesdelasolicitud');

				var cantidadMedicamentos = Object.keys(medicamentos).length;
				console.log('Cantidad de medicamentos:');
				console.log(cantidadMedicamentos);
				
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

				// console.log('ENTARNDO AL FOR');
				for (i in medicamentos) {
					// console.log('Estoy dentro del for');
					let medicamento = medicamentos[i];
					// console.log(medicamento);

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
					input2.name = 'detallesdelasolicitud-laboratorio[]';
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
				// console.log('saliendo del for');
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
		/* editar esta función, tira el id de la solicitud que acabas de añadir */
	    public function hook_after_add($id) {
	        //Your code here
			$pedidoNew = DB::table('pedido_masivo')->where('id', $id)->get();
			$lin_pedido = DB::table('pedido_masivo_detail')->where('pedido_masivo_id', $id)->get();
			$pedidoNew = $pedidoNew[0];
			$id_empresa = 2;
			$origen_id_sucursal = 99;
			$id_punto = $pedidoNew->punto_retiro_id;
			$fecha_pedido = date('Y-m-d H:i:s');
			$id_cliente = DB::table('punto_retiro')->where('id', $id_punto)->value('id_cliente');	
			$id_pedido = $pedidoNew->id_pedido;
			$id_masivo = $pedidoNew->id;
			
			$id_selected = Cache::get('ids_cache_key');
			/*cambio de estado */

			foreach($id_selected as $id){
				DB::table('pedido_medicamento')->where('id', $id)->update(['estado_solicitud_id' => 11]);
			}


			$pedido = new PedidoC();
			$pedido->created_at = $pedidoNew->created_at;
			$pedido->updated_at = $pedidoNew->updated_at;
			$pedido->id_empresa = $id_empresa;
			$pedido->id_pedido = $pedidoNew->id_pedido;
			$pedido->fecha_pedido = $fecha_pedido;
			$pedido->estado_pedido = 'EM'; // Estado "EM" = "Enviado a Mostrador
			$pedido->_origen_id_sucursal = $origen_id_sucursal;
			$pedido->id_cliente = $id_cliente; // Valor va cambiando conforme el cliente
			$pedido->observaciones = $pedidoNew->observaciones;
			$pedido->nrosolicitud = 'Pedido Masivo';
			$pedido->save();

			// Insertar en la tabla lin_pedido
			foreach ($lin_pedido as $key => $linpedido) {
				$des_articulo = DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('des_articulo');
				$presentacion = DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('presentacion');
				$id_articulo =DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('id_articulo');
				$linPedido = new LinPedido();
				$linPedido->created_at = $linpedido->created_at;
				$linPedido->updated_at = $linpedido->updated_at;
				$linPedido->id_pedido = $pedidoNew->id_pedido;
				$linPedido->item = $key + 1;
				$linPedido->id_articulo = $id_articulo;
				$linPedido->cantidad = $linpedido->cantidad;
				$linPedido->des_articulo = $des_articulo;
				$linPedido->presentacion = $presentacion;
				$linPedido->pcio_vta_uni_siva = $linpedido->precio;
				$linPedido->pcio_com_uni_siva = $linpedido->total;
				$linPedido->save();
			}

			/*Cargar en cotizacion_convenio: */
			$this->singleRequestAdd($id_selected, $id_punto, $id_pedido, $id_masivo);

			Cache::flush();
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

		private function singleRequestAdd($id_selected, $id_punto, $id_pedido, $id_masivo){	
			$myID = CRUDBooster::myId();
			$stamp_user = DB::table('cms_users')->where('id', $myID)->value('email');							

			$pedido_medicamento = [];
			$pedido_medicamento_detail = [];

			foreach($id_selected as $key => $id){
				$pedido_medicamento[$key]	= PedidoMedicamento::where('id',$id)->get();
				$pedido_medicamento_detail[$key] = PedidoMedicamentoDetail::where('pedido_medicamento_id', $id)->get();
			}

			

			$cotizacion_convenio = [];
			$cotizacion_convenio_detail = [];
			foreach($pedido_medicamento as $key => $pm){
				$cotizacion_convenio[$key] = new CotizacionConvenio();
				$cotizacion_convenio[$key]->nombreyapellido = Afiliados::where('id', $pm[0]->afiliados_id)->value('apeynombres');
				$cotizacion_convenio[$key]->documento = Afiliados::where('id', $pm[0]->afiliados_id)->value('documento');
				$cotizacion_convenio[$key]->nroAfiliado = $pm[0]->nroAfiliado;
				$cotizacion_convenio[$key]->edad = $pm[0]->edad;
				$cotizacion_convenio[$key]->nrosolicitud = $pm[0]->nrosolicitud;
				$cotizacion_convenio[$key]->clinicas_id = $pm[0]->clinicas_id;
				$cotizacion_convenio[$key]->medicos_id = $pm[0]->medicos_id;
				$cotizacion_convenio[$key]->zona_residencia = $pm[0]->zona_residencia;
				$cotizacion_convenio[$key]->tel_afiliado = $pm[0]->tel_afiliado;
				$cotizacion_convenio[$key]->email = $pm[0]->email;
				$cotizacion_convenio[$key]->fecha_receta = $pm[0]->fecha_receta;
				$cotizacion_convenio[$key]->postdatada = DB::table('postdatada')->where('id',$pm[0]->postdatada)->value('cantidad');
				$cotizacion_convenio[$key]->fecha_vencimiento = $pm[0]->fecha_vencimiento;
				$cotizacion_convenio[$key]->estado_solicitud_id = 11;
				$cotizacion_convenio[$key]->estado_pedido_id = 5;
				$cotizacion_convenio[$key]->punto_retiro_id = $id_punto;
				$cotizacion_convenio[$key]->proveedor = 'Global Médica';
				$cotizacion_convenio[$key]->stamp_user = $stamp_user;
				$cotizacion_convenio[$key]->discapacidad = $pm[0]->discapacidad;
				$cotizacion_convenio[$key]->id_pedido = $id_pedido;
				$cotizacion_convenio[$key]->save();

				foreach($pedido_medicamento_detail[$key] as $pmd){
					$cotizacion_detail = new CotizacionConvenioDetail();
					$cotizacion_detail->cotizacion_convenio_id = $cotizacion_convenio[$key]->id;
					$cotizacion_detail->articuloZafiro_id = $pmd->articuloZafiro_id;
					$cotizacion_detail->laboratorio = DB::table('pedido_masivo_detail')->where('pedido_masivo_id', $id_masivo)->where('articuloZafiro_id', $pmd->articuloZafiro_id)->value('laboratorio');
					$cotizacion_detail->cantidad = $pmd->cantidad;
					$cotizacion_detail->presentacion = ArticulosZafiro::where('id', $pmd->articuloZafiro_id)->value('presentacion_completa');			
					$cotizacion_detail->precio = DB::table('pedido_masivo_detail')->where('pedido_masivo_id', $id_masivo)->where('articuloZafiro_id', $pmd->articuloZafiro_id)->value('precio');
					$cotizacion_detail->descuento = DB::table('pedido_masivo_detail')->where('pedido_masivo_id', $id_masivo)->where('articuloZafiro_id', $pmd->articuloZafiro_id)->value('descuento');
					$total_ccd = ($cotizacion_convenio_detail[$key]->cantidad * $cotizacion_convenio_detail[$key]->precio * (1 - $cotizacion_convenio_detail[$key]->descuento/100));
					$cotizacion_detail->total =  $total_ccd;
					$cotizacion_detail->save();
					$cotizacion_convenio_detail[$key][] = $cotizacion_detail;
				}
			}

		}
	}


