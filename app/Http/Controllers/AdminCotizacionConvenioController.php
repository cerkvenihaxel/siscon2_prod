<?php namespace App\Http\Controllers;

	use App\Models\LinPedido;
    use App\Models\PedidoC;
    use App\Models\CotizacionConvenio;
    use Barryvdh\DomPDF\PDF;
    use Illuminate\Support\Facades\Redirect;
    use Session;
	use Request;
	use DB;
	use CRUDBooster;


    class AdminCotizacionConvenioController extends \crocodicstudio\crudbooster\controllers\CBController {

        private $url;
        private $nroSolicitud;
        private $nroAfiliado;
        private $numeroID;
        private $nombreyapellido;
        private $documento;
        private $edad;
        private $articulos;
        private $tel_afiliado;
        private $email;
        private $medicos_id;
        private $clinicas_id;
        private $discapacidad;
        private $fecha_receta;
        private $postdatada;
        private $fecha_vencimiento;
        private $zona_residencia;
        private $proveedor;
        private $medicacion;

        private $medicamentosRequiredId;
        private $stamp_user;



        public function __construct(Request $request) {
            $this->url = $_GET['id'];
            $this->numeroID = DB::table('convenio_oficina_os')->where('id', $this->url)->value('afiliados_id');
            $this->nombreyapellido = DB::table('convenio_oficina_os')->where('id', $this->url)->value('afiliados_id');
            $this->nombreyapellido = DB::table('afiliados')->where('id', $this->nombreyapellido)->value('apeynombres');

            $this->nroSolicitud = DB::table('convenio_oficina_os')->where('id', $this->url)->value('nrosolicitud');
            $this->nroAfiliado = DB::table('convenio_oficina_os')->where('id', $this->url)->value('nroAfiliado');
            $this->documento = DB::table('afiliados')->where('id', $this->numeroID)->value('documento');
            $this->edad = DB::table('convenio_oficina_os')->where('id', $this->url)->value('edad');
            $this->tel_afiliado = DB::table('convenio_oficina_os')->where('id', $this->url)->value('tel_afiliado');
            $this->email = DB::table('convenio_oficina_os')->where('id', $this->url)->value('email');
            $this->medicos_id = DB::table('convenio_oficina_os')->where('id', $this->url)->value('medicos_id');
            $this->clinicas_id = DB::table('convenio_oficina_os')->where('id', $this->url)->value('clinicas_id');
            $this->discapacidad = DB::table('convenio_oficina_os')->where('id', $this->url)->value('discapacidad');
            $this->fecha_receta = DB::table('convenio_oficina_os')->where('id', $this->url)->value('fecha_receta');
            $this->postdatada = DB::table('convenio_oficina_os')->where('id', $this->url)->value('postdatada');
            $this->postdatada = DB::table('postdatada')->where('id', $this->postdatada)->value('cantidad');
            $this->fecha_vencimiento = DB::table('convenio_oficina_os')->where('id', $this->url)->value('fecha_vencimiento');
            $this->zona_residencia = DB::table('convenio_oficina_os')->where('id', $this->url)->value('zona_residencia');
            $this->proveedorID = 2; //DB::table('convenio_oficina_os')->where('id', $this->url)->value('proveedor');
            $this->proveedor = DB::table('proveedores_convenio')->where('id', $this->proveedorID)->value('nombre');

            $this->medicamentosRequiredId = DB::table('pedido_medicamento')->where('nrosolicitud', $this->nroSolicitud)->value('id');
            $this->medicamentosRequired = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $this->medicamentosRequiredId)->get();

            $this->stamp_user = CRUDBooster::myName();
            $this->stamp_user = DB::table('cms_users')->where('name', $this->stamp_user)->value('email');

            $articulos_ids = [];
            foreach ($this->medicamentosRequired as $value) {
                $articulos_ids[] = $value->articuloZafiro_id;
            }

            $this->medicacion = DB::table('articulosZafiro')->whereIn('id', $articulos_ids)->get();
        }


	    public function cbInit()
        {

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
            $this->table = "cotizacion_convenio";
            # END CONFIGURATION DO NOT REMOVE THIS LINE

            # START COLUMNS DO NOT REMOVE THIS LINE
            $this->col = [];
            $this->col[] = ['label' => 'Fecha de carga', 'name' => 'created_at'];
            $this->col[] = ["label" => "Nombre y Apellido Afiliado", "name" => "nombreyapellido"];
            $this->col[] = ["label" => "Documento", "name" => "documento"];
            $this->col[] = ["label" => "Medico", "name" => "medicos_id", "join" => "medicos,nombremedico"];
            $this->col[] = ["label" => "Nro. Solicitud", "name" => "nrosolicitud"];
            $this->col[] = ["label" => "Proveedor", "name" => "proveedor"];
            $this->col[] = ["label" => "Estado Solicitud", "name" => "estado_solicitud_id", "join" => "estado_solicitud,estado"];
            $this->col[] = ["label"=> "Estado del pedido", "name"=> "estado_pedido_id", "join"=> "estado_pedido,estado"];
            $this->col[] = ["label"=>"ID Pedido", "name"=>"id_pedido"];
            # END COLUMNS DO NOT REMOVE THIS LINE

            $custom_element = view('articulosEntrantesCotMed')->render();
            # START FORM DO NOT REMOVE THIS LINE
            $this->form = [];
            $this->form[] = ['label' => 'Número ID', 'name' => 'numeroID', 'type' => 'text', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'required' => true, 'readonly' => true, 'value' => DB::table('convenio_oficina_os')->where('id', $this->url)->value('afiliados_id')];
            $this->form[] = ['label' => 'Nombre y Apellido Afiliado', 'name' => 'nombreyapellido', 'type' => 'text', 'validation' => 'required|min:0', 'width' => 'col-sm-10', 'required' => true, 'readonly' => true, 'value' => $this->nombreyapellido];
            $this->form[] = ['label' => 'NroAfiliado', 'name' => 'nroAfiliado', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->nroAfiliado];
            $this->form[] = ['label' => 'Documento', 'name' => 'documento', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->documento];
            $this->form[] = ['label' => 'Edad', 'name' => 'edad', 'type' => 'number', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->edad];
            $this->form[] = ['label' => 'Telefono afiliado', 'name' => 'tel_afiliado', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->tel_afiliado];
            $this->form[] = ['label' => 'Email', 'name' => 'email', 'type' => 'text', 'validation' => 'min:1|max:255|', 'width' => 'col-sm-10', 'placeholder' => 'Introduce una dirección de correo electrónico válida','value' => $this->email];
            $this->form[] = ['label' => 'Medicos Id', 'name' => 'medicos_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'medicos,nombremedico', 'readonly' => true, 'value' => $this->medicos_id];
            $this->form[] = ['label' => 'Clinicas Id', 'name' => 'clinicas_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'clinicas,nombre', 'readonly' => true, 'value' => $this->clinicas_id];
            $this->form[] = ['label' => 'Discapacidad', 'name' => 'discapacidad', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->discapacidad];
            $this->form[] = ['label' => 'Nrosolicitud', 'name' => 'nrosolicitud', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => 'readonly', 'value' => $this->nroSolicitud];
            $this->form[] = ['label' => 'Fecha Receta', 'name' => 'fecha_receta', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->fecha_receta];
            $this->form[] = ['label' => 'Postdatada', 'name' => 'postdatada', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->postdatada];
            $this->form[] = ['label' => 'Fecha Vencimiento', 'name' => 'fecha_vencimiento', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-10', 'readonly' => true, 'value' => $this->fecha_vencimiento];
            $this->form[] = ['label' => 'Zona Residencia', 'name' => 'zona_residencia', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'value' => $this->zona_residencia, 'readonly' => true];

            $this->form[] = ['label' => 'Fecha Entrega', 'name' => 'fecha_entrega', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-10', 'placeholder' => 'Introduce una fecha de entrega'];

            $this->form[] = ['label' => 'Estado Pedido Id', 'name' => 'estado_pedido_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'estado_pedido,estado', 'value' => 3];
            $this->form[] = ['label' => 'Estado Solicitud Id', 'name' => 'estado_solicitud_id', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'datatable' => 'estado_solicitud,estado', 'value' => 6];

            $this->form[] = ['name'=>'custom_field','type'=>'custom','html'=>$custom_element,'width'=>'col-sm-10'];

            $columns = [];
            $columns[] = ['label'=> 'Medicamentos solicitados', 'name'=>'articuloZafiro_id', 'type'=>'datamodal', 'datamodal_table'=>'articulosZafiro', 'validation'=>'required', 'datamodal_columns_alias'=>'Monodroga, Descripción del artículo, Presentación, ID ARTÍCULO', 'datamodal_columns'=>'des_monodroga,des_articulo,presentacion,id_articulo', 'datamodal_size'=>'large', 'datamodal_where'=>'id_familia = "01"', 'AND', 'id_familia= "14"', 'datamodal_select_to'=>'presentacion:presentacion','required'=>true];
            $columns [] = ['label'=> 'Presentación', 'name'=>'presentacion', 'type'=>'text', 'readonly'=>true];
            $columns[] = ['label'=> 'Cantidad', 'name'=>'cantidad', 'type'=>'number', 'validation'=>'required|integer|min:0', 'required'=>true];
            $columns[] = ['label'=>'Laboratorio', 'name'=>'laboratorio', 'type'=>'text', 'validation'=>'required|min:1|max:255', 'required'=>true];
            $columns[] = ['label'=>'Precio', 'name'=>'precio', 'type'=>'text', 'validation'=>'required|integer|min:0', 'required'=>true];
            $columns[] = ['label'=>'Descuento (%)', 'name'=>'descuento', 'type'=>'number', 'validation'=>'required|integer|min:0', 'required'=>true];
            $columns[] = ['label'=>'Total', 'name'=>'total', 'type'=>'text', 'validation'=>'required|integer|min:0', 'required'=>true];
            $this->form[] = ['label'=>'Detalles de la solicitud', 'name'=>'cotizacion_convenio_detail', 'type'=>'child','table'=>'cotizacion_convenio_detail', 'foreign_key'=>'cotizacion_convenio_id', 'columns'=>$columns, 'width'=>'col-sm-10','required'=>true];


            $this->form[] = ['label' => 'Proveedor', 'name' => 'proveedor', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'value'=> $this->proveedor, 'readonly' => true];
            $this->form[] = ['label' => 'Punto de Retiro', 'name' => 'punto_retiro_id', 'type'=>'datamodal', 'datamodal_table'=>'punto_retiro', 'datamodal_columns'=> 'nombre', 'datamodal_columns_alias'=>'Nombre', 'datamodal_size'=>'large', 'width'=>'col-sm-10', 'datamodal_where' =>'proveedor_convenio_id='.$this->proveedorID, 'datamodal_select_to'=>'direccion:direccion_retiro,localidad:localidad_retiro,telefono:tel_retiro'];
            $this->form[] = ['label'=>'Direccion de entrega','name'=>'direccion_retiro','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Localidad de retiro','name'=>'localidad_retiro','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Telefono de punto de retiro','name'=>'tel_retiro','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Observaciones','name'=>'observaciones','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Archivo 1', 'name'=>'archivo','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 2', 'name'=>'archivo2','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 3', 'name'=>'archivo3','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];
            $this->form[] = ['label'=>'Archivo 4', 'name'=>'archivo4','type'=>'upload', 'help'=>'Archivos soportados PDF JPEG DOCX'];


            $this->form[] = ['label'=>'Stamp User','name'=>'stamp_user','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10', 'disabled'=>'disabled', 'readonly'=>true, 'value'=>DB::table('cms_users')->where('id',CRUDBooster::myId())->value('email')];

            # END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Archivo","name"=>"archivo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo2","name"=>"archivo2","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo3","name"=>"archivo3","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Archivo4","name"=>"archivo4","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Clinicas Id","name"=>"clinicas_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"clinicas,id"];
			//$this->form[] = ["label"=>"Discapacidad","name"=>"discapacidad","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Documento","name"=>"documento","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Edad","name"=>"edad","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Email","name"=>"email","type"=>"email","required"=>TRUE,"validation"=>"required|min:1|max:255|email|unique:cotizacion_convenio","placeholder"=>"Introduce una dirección de correo electrónico válida"];
			//$this->form[] = ["label"=>"Estado Pedido Id","name"=>"estado_pedido_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_pedido,id"];
			//$this->form[] = ["label"=>"Estado Solicitud Id","name"=>"estado_solicitud_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"estado_solicitud,id"];
			//$this->form[] = ["label"=>"Fecha Carga","name"=>"fecha_carga","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Fecha Entrega","name"=>"fecha_entrega","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Fecha Receta","name"=>"fecha_receta","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Fecha Vencimiento","name"=>"fecha_vencimiento","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Medicos Id","name"=>"medicos_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"medicos,id"];
			//$this->form[] = ["label"=>"Nombreyapellido","name"=>"nombreyapellido","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NroAfiliado","name"=>"nroAfiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Nrosolicitud","name"=>"nrosolicitud","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"NumeroID","name"=>"numeroID","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Observaciones","name"=>"observaciones","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Postdatada","name"=>"postdatada","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Proveedor","name"=>"proveedor","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Punto Retiro Id","name"=>"punto_retiro_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"punto_retiro,id"];
			//$this->form[] = ["label"=>"Stamp User","name"=>"stamp_user","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Afiliado","name"=>"tel_afiliado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Tel Medico","name"=>"tel_medico","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Zona Residencia","name"=>"zona_residencia","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
            $this->addaction[] = ['label'=>'Enviar pedido a depósito', 'url'=>('/linpedido_objeto/[id]'),'icon'=>'fa fa-send','color'=>'success', 'confirmation'=>true, 'showIf'=>"[estado_pedido_id] == 3"];
            $this->addaction[] = ['label'=>'Imprimir pedido', 'url'=>('/generarPDF_convenio/[id]'),'icon'=>'fa fa-print','color'=>'warning', 'confirmation'=>true, 'showIf'=>"[estado_pedido_id] == 5"];




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



	     function calcularTotal() {
  // Obtener los valores de precio y descuento

  const cantidad = parseFloat(document.getElementById('detallesdelasolicitudcantidad').value);
  const precio = parseFloat(document.getElementById('detallesdelasolicitudprecio').value);
  const descuento = parseFloat(document.getElementById('detallesdelasolicituddescuento').value);

  // Calcular el total
  const total = cantidad * precio * (1 - descuento / 100);

  // Actualizar el valor del campo total
  document.getElementById('detallesdelasolicitudtotal').value = total.toFixed(2);
}

setInterval(calcularTotal, 500);


 function addRow() {

    var medicamentos = ".$this->medicacion.";
    var cantidades = ".$this->medicamentosRequired.";
    // Get a reference to the table and insert a new row at the end
    let table = document.getElementById('table-detallesdelasolicitud');


//For function for medicamentos variable loop

for (var i = 0; i < medicamentos.length; i++) {

    let row = table.insertRow();

    var medicamentoId = medicamentos[i].id;
    var cantidadEncontrada = null;

    // Buscar la cantidad correspondiente al medicamento actual
    for (var j = 0; j < cantidades.length; j++) {
        if (cantidades[j].articuloZafiro_id === medicamentoId) {
            cantidadEncontrada = cantidades[j];
            break;
        }
    }

    // Add the td elements for each column in the row
    var td1 = document.createElement('td');
    td1.className = 'articuloZafiro_id';
    var label = document.createElement('span');
    label.className = 'td-label';
    label.textContent = medicamentos[i].des_monodroga;
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'detallesdelasolicitud-articuloZafiro_id[]';
    input.value = medicamentos[i].id;




    var td2 = document.createElement('td');
    td2.className = 'presentacion';
    td2.textContent = medicamentos[i].presentacion_completa;
    var input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'detallesdelasolicitud-presentacion[]';
    input2.value = medicamentos[i].presentacion_completa;

    if (cantidadEncontrada !== null) {
        var td3 = document.createElement('td');
        td3.className = 'cantidad';
        td3.textContent = cantidadEncontrada.cantidad;
        var input3 = document.createElement('input');
        input3.type = 'hidden';
        input3.name = 'detallesdelasolicitud-cantidad[]';
        input3.value = cantidadEncontrada.cantidad;
        td3.appendChild(input3);
        row.appendChild(td3);
    } else {
        // Si no se encuentra la cantidad, agregar una celda vacía
        var td3 = document.createElement('td');
        td3.className = 'cantidad';
        td3.textContent = '';
        row.appendChild(td3);
    }

    var td5 = document.createElement('td');
    td5.className = 'laboratorio';
    td5.textContent = ' ';
    var input5 = document.createElement('input');
    input5.type = 'hidden';
    input5.name = 'detallesdelasolicitud-laboratorio[]';
    input5.value = ' ';

    var td6 = document.createElement('td');
    td6.className = 'precio';
    td6.textContent = ' ';
    var input6 = document.createElement('input');
    input6.type = 'hidden';
    input6.name = 'detallesdelasolicitud-precio[]';
    input6.value = ' ';

    var td7 = document.createElement('td');
    td7.className = 'descuento';
    td7.textContent = ' ';
    var input7 = document.createElement('input');
    input7.type = 'hidden';
    input7.name = 'detallesdelasolicitud-descuento[]';
    input7.value = ' ';

    var td8 = document.createElement('td');
    td8.className = 'total';
    td8.textContent = ' ';
    var input8 = document.createElement('input');
    input8.type = 'hidden';
    input8.name = 'detallesdelasolicitud-total[]';
    input8.value = ' ';



    var td4 = document.createElement('td');
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
    td1.appendChild(input);
    td2.appendChild(input2);
    td3.appendChild(input3);
    td5.appendChild(input5);
    td6.appendChild(input6);
    td7.appendChild(input7);
    td8.appendChild(input8);
    td4.appendChild(editLink);
    td4.appendChild(document.createTextNode(' '));
    td4.appendChild(deleteLink);
     // Append the td elements to the new row
    row.appendChild(td1);
    row.appendChild(td2);
    row.appendChild(td3);
    row.appendChild(td5);
    row.appendChild(td6);
    row.appendChild(td7);
    row.appendChild(td8);
    row.appendChild(td4);

}
}
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

        public function enviarPedidoSingular($id){
            $numero = $this->generatePedidoNumber();

            DB::table('cotizacion_convenio')->where('id', $id)->update(['id_pedido' => $numero]);

            DB::table('cotizacion_convenio')->where('id', $id)->update(['estado_pedido_id' => 5]);
            $nroSolicitud = DB::table('cotizacion_convenio')->where('id', $id)->value('nrosolicitud');
            $observaciones = DB::table('cotizacion_convenio')->where('nrosolicitud', $nroSolicitud)->value('observaciones');
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
                $articuloID = DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('id_articulo');
                $numeroArticulo =  $newNumber = str_pad($articuloID, 10, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda

                $lin_pedidos[] = [
                    'created_at' => $fecha_pedido,
                    'updated_at' => $fecha_pedido,
                    'id_pedido' => $id_pedido,
                    'item' => $key+1,
                    'id_articulo' => $numeroArticulo,
                    'cantidad' => $linpedido->cantidad,
                    'des_articulo' => DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('des_articulo'),
                    'presentacion' => DB::table('articulosZafiro')->where('id', $linpedido->articuloZafiro_id)->value('presentacion'),
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

            return Redirect::back();
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





	    //By the way, you can still create your own method in here... :)


	}
