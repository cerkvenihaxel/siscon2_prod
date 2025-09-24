<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Models\PedidoOxigenoterapia;
use App\Models\EquiposOxigenoterapia;
use App\Models\PrestamoOxigenoterapia;
use App\Models\PedidoOxigenoterapiaMaterial;
use App\Models\DocumentosOxigenoterapia;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminPrestamoOxigenoterapiaController extends \crocodicstudio\crudbooster\controllers\CBController {

    public function cbInit() {

        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "nro_prestamo";
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
        $this->table = "prestamo_oxigenoterapia";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label"=>"Nro Préstamo","name"=>"nro_prestamo"];
        $this->col[] = ["label"=>"Pedido","name"=>"pedido_oxigenoterapia_id","join"=>"pedido_oxigenoterapia,nro_solicitud"];
        $this->col[] = ["label"=>"Afiliado","name"=>"pedido_oxigenoterapia_id","join"=>"pedido_oxigenoterapia,nombre_apellido"];
        $this->col[] = ["label"=>"Fecha Inicio","name"=>"fecha_inicio_prestamo"];
        $this->col[] = ["label"=>"Fecha Fin","name"=>"fecha_fin_prestamo"];
        $this->col[] = ["label"=>"Localidad","name"=>"localidad_entrega"];
        $this->col[] = ["label"=>"Estado","name"=>"estado_prestamo"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ['label'=>'Pedido','name'=>'pedido_oxigenoterapia_id','type'=>'select2','validation'=>'required|integer','width'=>'col-sm-10','datatable'=>'pedido_oxigenoterapia,nro_solicitud'];
        $this->form[] = ['label'=>'Nro Préstamo','name'=>'nro_prestamo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Fecha Inicio Préstamo','name'=>'fecha_inicio_prestamo','type'=>'datetime','validation'=>'required|date','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Fecha Fin Préstamo','name'=>'fecha_fin_prestamo','type'=>'datetime','validation'=>'required|date','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Dirección Entrega','name'=>'direccion_entrega','type'=>'textarea','validation'=>'required','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Equipo Entregado','name'=>'equipo_entregado','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Nro Serie Equipo','name'=>'nro_serie_equipo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
        $this->form[] = ['label'=>'Estado Préstamo','name'=>'estado_prestamo','type'=>'select','validation'=>'required','width'=>'col-sm-10','dataenum'=>'ACTIVO|Activo;RENOVADO|Renovado;FINALIZADO|Finalizado'];
        $this->form[] = ['label'=>'Stamp User','name'=>'stamp_user','type'=>'hidden','value'=>DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email')];
        # END FORM DO NOT REMOVE THIS LINE

        // Pre-rellenar datos si se recibe pedido_id
        if (Request::get('pedido_id')) {
            $pedido = PedidoOxigenoterapia::with(['materiales.equipo'])->find(Request::get('pedido_id'));
            if ($pedido) {
                $this->form[] = ['label'=>'Pedido','name'=>'pedido_oxigenoterapia_id','type'=>'hidden','value'=>$pedido->id];
                $this->form[] = ['label'=>'Nro Préstamo','name'=>'nro_prestamo','type'=>'text','value'=>$this->generatePrestamoNumber(),'readonly'=>true];
                
                // Obtener el primer equipo disponible
                $equipo = $pedido->materiales->first()->equipo ?? null;
                if ($equipo) {
                    $this->form[] = ['label'=>'Equipo Entregado','name'=>'equipo_entregado','type'=>'text','value'=>$equipo->nombre_equipo,'readonly'=>true];
                    $this->form[] = ['label'=>'Nro Serie Equipo','name'=>'nro_serie_equipo','type'=>'text','value'=>$equipo->nro_serie,'readonly'=>true];
                }
                
                $this->form[] = ['label'=>'Fecha Inicio Préstamo','name'=>'fecha_inicio_prestamo','type'=>'datetime','value'=>date('Y-m-d H:i:s'),'readonly'=>true];
                $this->form[] = ['label'=>'Fecha Fin Préstamo','name'=>'fecha_fin_prestamo','type'=>'datetime','value'=>date('Y-m-d H:i:s', strtotime('+30 days')),'readonly'=>true];
                $this->form[] = ['label'=>'Dirección Entrega','name'=>'direccion_entrega','type'=>'textarea','value'=>$pedido->direccion_entrega ?? ''];
                $this->form[] = ['label'=>'Estado Préstamo','name'=>'estado_prestamo','type'=>'hidden','value'=>'ACTIVO'];
            }
        }

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
        $this->addaction[] = ['label'=>'Ver Préstamo', 'url'=>'/admin/prestamo-oxigenoterapia/ver-prestamo/[id]','button_color'=>'info','button_icon'=>'fa fa-eye'];
        $this->addaction[] = ['label'=>'Renovar', 'url'=>'/admin/prestamo-oxigenoterapia/renovar/[id]','button_color'=>'success','button_icon'=>'fa fa-refresh', 'showIf'=>'[estado_prestamo] == "ACTIVO"'];
        $this->addaction[] = ['label'=>'Finalizar', 'url'=>'/admin/prestamo-oxigenoterapia/finalizar/[id]','button_color'=>'warning','button_icon'=>'fa fa-check', 'showIf'=>'[estado_prestamo] == "ACTIVO"'];
        $this->addaction[] = ['label'=>'Documentos', 'url'=>'/admin/prestamo-oxigenoterapia/documentos/[id]','button_color'=>'primary','button_icon'=>'fa fa-file-text'];

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
        $this->alert = array();

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
        | $this->post_index_html = null;
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
    | @column_index = index of column
    | @column_value = value of column
    | @record = record of row
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

    //By the way, you can still create your own method in here... :)
    
    private function generatePrestamoNumber()
    {
        $today = date('Ymd');
        $prefix = 'PREST-' . $today . '-';
        
        // Buscar el último número de préstamo del día actual
        $lastPrestamo = DB::table('prestamo_oxigenoterapia')
            ->where('nro_prestamo', 'like', $prefix . '%')
            ->orderBy('nro_prestamo', 'desc')
            ->first();

        if ($lastPrestamo) {
            // Extraer el número del último registro del día
            $lastNumber = (int)substr($lastPrestamo->nro_prestamo, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Si no hay registros del día, empezar con 0001
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
    
    public function verPrestamo($id)
    {
        // Usar Eloquent para cargar el préstamo con relaciones
        $prestamo = PrestamoOxigenoterapia::with(['pedidoOxigenoterapia.afiliado', 'pedidoOxigenoterapia.medicos', 'pedidoOxigenoterapia.clinica'])
            ->find($id);
            
        if (!$prestamo) {
            return redirect('/admin/prestamo_oxigenoterapia')->with('error', 'Préstamo no encontrado');
        }
        
        // Obtener materiales del pedido con relación a equipos
        $materiales = PedidoOxigenoterapiaMaterial::with('equipo')
            ->where('pedido_oxigenoterapia_id', $prestamo->pedido_oxigenoterapia_id)
            ->get();
        
        // Log para debugging
        \Log::info('Materiales cargados para ver préstamo ' . $id . ':', [
            'total_materiales' => $materiales->count(),
            'materiales' => $materiales->map(function($material) {
                return [
                    'id' => $material->id,
                    'nombre_equipo' => $material->nombre_equipo,
                    'entregable' => $material->entregable,
                    'equipo_id' => $material->equipos_oxigenoterapia_id,
                    'equipo_nombre' => $material->equipo ? $material->equipo->nombre_equipo : 'NO EQUIPO',
                    'equipo_serie' => $material->equipo ? $material->equipo->nro_serie : 'NO SERIE',
                    'equipo_codigo' => $material->equipo ? $material->equipo->codigo_equipo : 'NO CODIGO'
                ];
            })->toArray()
        ]);
        
        return view('prestamo_oxigenoterapia.ver_prestamo', compact('prestamo', 'materiales'));
    }

    /**
     * Renovar un préstamo activo
     */
    public function renovar($id)
    {
        $prestamo = PrestamoOxigenoterapia::findOrFail($id);
        
        if ($prestamo->estado_prestamo !== 'ACTIVO') {
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Solo se pueden renovar préstamos activos');
        }

        return view('prestamo_oxigenoterapia.renovar', compact('prestamo'));
    }

    /**
     * Procesar la renovación del préstamo
     */
    public function procesarRenovacion(HttpRequest $request, $id)
    {
        $request->validate([
            'duracion_renovacion' => 'required|integer|min:1|max:365',
            'observaciones_renovacion' => 'nullable|string|max:500'
        ]);

        $prestamo = PrestamoOxigenoterapia::findOrFail($id);
        
        if ($prestamo->estado_prestamo !== 'ACTIVO') {
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Solo se pueden renovar préstamos activos');
        }

        DB::beginTransaction();
        try {
            // Calcular nueva fecha de fin
            $nuevaFechaFin = date('Y-m-d', strtotime($prestamo->fecha_fin_prestamo . ' + ' . $request->duracion_renovacion . ' days'));
            
            // Actualizar el préstamo
            $prestamo->fecha_fin_prestamo = $nuevaFechaFin;
            $prestamo->estado_prestamo = 'RENOVADO';
            $prestamo->observaciones = $prestamo->observaciones . "\n\nRENOVACIÓN: " . $request->observaciones_renovacion;
            $prestamo->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $prestamo->save();

            // Actualizar estado del pedido
            $pedido = PedidoOxigenoterapia::find($prestamo->pedido_oxigenoterapia_id);
            if ($pedido) {
                $pedido->estado_oxigenoterapia_id = 4; // RENOVADO
                $pedido->save();
            }

            DB::commit();

            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('message', 'Préstamo renovado exitosamente hasta el ' . date('d/m/Y', strtotime($nuevaFechaFin)));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Error al renovar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Finalizar un préstamo activo
     */
    public function finalizar($id)
    {
        $prestamo = PrestamoOxigenoterapia::findOrFail($id);
        
        if ($prestamo->estado_prestamo !== 'ACTIVO' && $prestamo->estado_prestamo !== 'RENOVADO') {
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Solo se pueden finalizar préstamos activos o renovados');
        }

        return view('prestamo_oxigenoterapia.finalizar', compact('prestamo'));
    }

    /**
     * Procesar la finalización del préstamo
     */
    public function procesarFinalizacion(HttpRequest $request, $id)
    {
        $request->validate([
            'fecha_devolucion' => 'required|date',
            'observaciones_finalizacion' => 'nullable|string|max:500',
            'equipos_devueltos' => 'required|array',
            'equipos_devueltos.*' => 'boolean'
        ]);

        $prestamo = PrestamoOxigenoterapia::findOrFail($id);
        
        if ($prestamo->estado_prestamo !== 'ACTIVO' && $prestamo->estado_prestamo !== 'RENOVADO') {
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Solo se pueden finalizar préstamos activos o renovados');
        }

        DB::beginTransaction();
        try {
            // Actualizar el préstamo
            $prestamo->estado_prestamo = 'FINALIZADO';
            $prestamo->fecha_devolucion = $request->fecha_devolucion;
            $prestamo->observaciones = $prestamo->observaciones . "\n\nFINALIZACIÓN: " . $request->observaciones_finalizacion;
            $prestamo->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $prestamo->save();

            // Actualizar estado del pedido
            $pedido = PedidoOxigenoterapia::find($prestamo->pedido_oxigenoterapia_id);
            if ($pedido) {
                $pedido->estado_oxigenoterapia_id = 5; // FINALIZADO
                $pedido->save();
            }

            // Actualizar materiales devueltos
            foreach ($request->equipos_devueltos as $materialId => $devuelto) {
                $material = PedidoOxigenoterapiaMaterial::find($materialId);
                if ($material && $material->pedido_oxigenoterapia_id == $prestamo->pedido_oxigenoterapia_id) {
                    $material->devuelto = $devuelto;
                    $material->fecha_devolucion = $devuelto ? $request->fecha_devolucion : null;
                    $material->save();
                }
            }

            DB::commit();

            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('message', 'Préstamo finalizado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/admin/prestamo_oxigenoterapia')
                ->with('error', 'Error al finalizar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar documentos del préstamo
     */
    public function documentos($id)
    {
        $prestamo = PrestamoOxigenoterapia::with(['pedidoOxigenoterapia.afiliado', 'pedidoOxigenoterapia.medicos', 'pedidoOxigenoterapia.clinica'])
            ->findOrFail($id);

        // Obtener documentos existentes
        $documentos = DocumentosOxigenoterapia::where('prestamo_oxigenoterapia_id', $id)
            ->orWhere('pedido_oxigenoterapia_id', $prestamo->pedido_oxigenoterapia_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('prestamo_oxigenoterapia.documentos', compact('prestamo', 'documentos'));
    }

    /**
     * Subir documento firmado
     */
    public function subirDocumento(HttpRequest $request, $id)
    {
        $request->validate([
            'tipo_documento' => 'required|string|in:consentimiento,terminos,contrato,instrucciones,checklist,resumen',
            'archivo' => 'required|file|mimes:pdf|max:10240', // Máximo 10MB
            'observaciones' => 'nullable|string|max:500'
        ]);

        $prestamo = PrestamoOxigenoterapia::findOrFail($id);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = 'documentos/oxigenoterapia/' . $prestamo->pedido_oxigenoterapia_id . '/' . $nombreArchivo;

            // Crear directorio si no existe
            $directorio = storage_path('app/public/documentos/oxigenoterapia/' . $prestamo->pedido_oxigenoterapia_id);
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            // Guardar archivo
            Storage::disk('public')->put($rutaArchivo, File::get($archivo));

            // Crear registro en base de datos
            $documento = new DocumentosOxigenoterapia();
            $documento->pedido_oxigenoterapia_id = $prestamo->pedido_oxigenoterapia_id;
            $documento->prestamo_oxigenoterapia_id = $prestamo->id;
            $documento->tipo_documento = $request->tipo_documento;
            $documento->nombre_archivo = $archivo->getClientOriginalName();
            $documento->ruta_archivo = $rutaArchivo;
            $documento->mime_type = $archivo->getMimeType();
            $documento->tamaño_bytes = $archivo->getSize();
            $documento->observaciones = $request->observaciones;
            $documento->firmado = true;
            $documento->fecha_firma = now();
            $documento->firmado_por = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('name');
            $documento->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $documento->save();

            return redirect('/admin/prestamo-oxigenoterapia/documentos/' . $id)
                ->with('message', 'Documento subido exitosamente');

        } catch (\Exception $e) {
            return redirect('/admin/prestamo-oxigenoterapia/documentos/' . $id)
                ->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    /**
     * Descargar documento
     */
    public function descargarDocumento($documentoId)
    {
        $documento = DocumentosOxigenoterapia::findOrFail($documentoId);
        
        if (!Storage::disk('public')->exists($documento->ruta_archivo)) {
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }

        return Storage::disk('public')->download($documento->ruta_archivo, $documento->nombre_archivo);
    }

    /**
     * Ver documento en el navegador
     */
    public function verDocumento($documentoId)
    {
        $documento = DocumentosOxigenoterapia::findOrFail($documentoId);
        
        if (!Storage::disk('public')->exists($documento->ruta_archivo)) {
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }

        $archivo = Storage::disk('public')->get($documento->ruta_archivo);
        
        return response($archivo, 200, [
            'Content-Type' => $documento->mime_type,
            'Content-Disposition' => 'inline; filename="' . $documento->nombre_archivo . '"'
        ]);
    }

    /**
     * Eliminar documento
     */
    public function eliminarDocumento($documentoId)
    {
        $documento = DocumentosOxigenoterapia::findOrFail($documentoId);
        
        try {
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            
            // Eliminar registro de base de datos
            $documento->delete();
            
            return redirect()->back()->with('message', 'Documento eliminado exitosamente');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }
    
    public function getAdd() {
        if (Request::get('pedido_id')) {
            $pedido = PedidoOxigenoterapia::with(['materiales.equipo', 'afiliado', 'medicos', 'clinica'])->find(Request::get('pedido_id'));
            if ($pedido) {
                $nro_prestamo = $this->generatePrestamoNumber();
                $materiales = $pedido->materiales;
                
                // Log para debugging de materiales
                \Log::info('Materiales cargados para el pedido ' . $pedido->id . ':', [
                    'total_materiales' => $materiales->count(),
                    'materiales' => $materiales->map(function($material) {
                        return [
                            'id' => $material->id,
                            'nombre_equipo' => $material->nombre_equipo,
                            'equipo_id' => $material->equipos_oxigenoterapia_id,
                            'equipo_nombre' => $material->equipo ? $material->equipo->nombre_equipo : 'NO EQUIPO',
                            'equipo_serie' => $material->equipo ? $material->equipo->nro_serie : 'NO SERIE'
                        ];
                    })->toArray()
                ]);
                
                return view('prestamo_oxigenoterapia.add_with_checklist', compact('pedido', 'nro_prestamo', 'materiales'));
            }
        }
        
        return parent::getAdd();
    }
} 