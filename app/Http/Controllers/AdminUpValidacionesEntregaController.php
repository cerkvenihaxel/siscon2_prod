<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Models\UpValidacionEntrega;
use App\Models\UpConsumo;

class AdminUpValidacionesEntregaController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "medicamento_descripcion";
        $this->limit = "20";
        $this->orderby = "fecha_entrega,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = false; // Las validaciones se crean desde consumos
        $this->button_edit = false; // Solo lectura
        $this->button_delete = false; // No se pueden eliminar
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "up_validaciones_entrega";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Fecha Entrega", "name" => "fecha_entrega", "callback" => function($row) {
            return date('d/m/Y H:i', strtotime($row->fecha_entrega));
        }];
        $this->col[] = ["label" => "Afiliado", "name" => "afiliado_codigo"];
        $this->col[] = ["label" => "Nombre Completo", "name" => "afiliado_apellido", "callback" => function($row) {
            return $row->afiliado_apellido . ', ' . $row->afiliado_nombre;
        }];
        $this->col[] = ["label" => "Medicamento", "name" => "medicamento_descripcion", "callback" => function($row) {
            return '<strong>' . $row->medicamento_codigo . '</strong><br><small>' . 
                   (strlen($row->medicamento_descripcion) > 50 ? 
                    substr($row->medicamento_descripcion, 0, 50) . '...' : 
                    $row->medicamento_descripcion) . '</small>';
        }];
        $this->col[] = ["label" => "Cantidad", "name" => "cantidad", "callback" => function($row) {
            $badge = $row->entrega_completa ? 'success' : 'warning';
            $texto = $row->entrega_completa ? $row->cantidad : ($row->cantidad_entregada . '/' . $row->cantidad);
            return "<span class='badge badge-{$badge}'>{$texto}</span>";
        }];
        $this->col[] = ["label" => "Importe", "name" => "importe_autorizado", "callback" => function($row) {
            return '$' . number_format($row->importe_autorizado, 2, ',', '.');
        }];
        $this->col[] = ["label" => "IDAUT", "name" => "idaut", "callback" => function($row) {
            return $row->idaut ? "<code>{$row->idaut}</code>" : '<small class="text-muted">N/A</small>';
        }];
        $this->col[] = ["label" => "Farmacia", "name" => "farmacia_nombre", "callback" => function($row) {
            if ($row->farmacia_nombre) {
                return '<strong>' . $row->farmacia_codigo . '</strong><br><small>' . $row->farmacia_nombre . '</small>';
            }
            return '<small class="text-muted">No especificada</small>';
        }];
        $this->col[] = ["label" => "Usuario Entrega", "name" => "usuario_entrega"];
        $this->col[] = ["label" => "Estado", "name" => "entrega_completa", "callback" => function($row) {
            if ($row->entrega_completa) {
                return '<span class="badge badge-success">Completa</span>';
            } else {
                return '<span class="badge badge-warning">Parcial</span>';
            }
        }];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        // Solo vista de detalles, no formulario de edición
        # END FORM DO NOT REMOVE THIS LINE

        /*
        | ----------------------------------------------------------------------
        | Sub Module
        | ----------------------------------------------------------------------
        */
        $this->sub_module = array();

        /*
        | ----------------------------------------------------------------------
        | Add More Action Button / Menu
        | ----------------------------------------------------------------------
        */
        $this->addaction = array();

        // Ver Comprobante
        $this->addaction[] = [
            'label' => 'Ver Comprobante',
            'url' => CRUDBooster::mainpath('comprobante/[id]'),
            'icon' => 'fa fa-file-pdf-o',
            'color' => 'primary',
        ];

        // Ver Consumo Original
        $this->addaction[] = [
            'label' => 'Ver Consumo Original',
            'url' => CRUDBooster::adminPath('up_consumos/detail/[consumo_id]'),
            'icon' => 'fa fa-eye',
            'color' => 'info',
        ];

        /*
        | ----------------------------------------------------------------------
        | Add More Button Selected
        | ----------------------------------------------------------------------
        */
        $this->button_selected = array();

        /*
        | ----------------------------------------------------------------------
        | Add alert message to this module at overheader
        | ----------------------------------------------------------------------
        */
        $this->alert = array();

        /*
        | ----------------------------------------------------------------------
        | Add more button to header button
        | ----------------------------------------------------------------------
        */
        $this->index_button = array();
        $this->index_button[] = [
            'label' => 'Dashboard Farmacia',
            'url' => CRUDBooster::adminPath('up_consumos/dashboard-farmacia'),
            'icon' => 'fa fa-dashboard',
            'color' => 'success'
        ];
        $this->index_button[] = [
            'label' => 'Exportar Validaciones',
            'url' => CRUDBooster::adminPath('up_consumos/exportar-validaciones'),
            'icon' => 'fa fa-download',
            'color' => 'primary'
        ];

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        */
        $this->table_row_color = array();
        $this->table_row_color[] = ['condition' => "[entrega_completa] == 1", 'color' => 'success'];
        $this->table_row_color[] = ['condition' => "[entrega_completa] == 0", 'color' => 'warning'];

        /*
        | ----------------------------------------------------------------------
        | You may use this bellow array to add statistic at dashboard
        | ----------------------------------------------------------------------
        */
        $this->index_statistic = array();

        /*
        | ----------------------------------------------------------------------
        | Add javascript at body
        | ----------------------------------------------------------------------
        */
        $this->script_js = NULL;

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code before index table
        | ----------------------------------------------------------------------
        */
        $this->pre_index_html = "
            <div class='panel panel-primary'>
                <div class='panel-body'>
                    <h4><i class='fa fa-clipboard-check'></i> Validaciones de Entrega - Convenio UP</h4>
                    <p><strong>📋 Función:</strong> Registro completo de medicamentos entregados a afiliados de Unión Personal</p>
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class='small-box bg-green'>
                                <div class='inner'>
                                    <h3>" . UpValidacionEntrega::whereDate('fecha_entrega', today())->count() . "</h3>
                                    <p>Entregas Hoy</p>
                                </div>
                                <div class='icon'><i class='fa fa-calendar'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-blue'>
                                <div class='inner'>
                                    <h3>" . UpValidacionEntrega::whereMonth('fecha_entrega', now()->month)->count() . "</h3>
                                    <p>Entregas Este Mes</p>
                                </div>
                                <div class='icon'><i class='fa fa-calendar-o'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-purple'>
                                <div class='inner'>
                                    <h3>" . UpValidacionEntrega::where('entrega_completa', true)->count() . "</h3>
                                    <p>Entregas Completas</p>
                                </div>
                                <div class='icon'><i class='fa fa-check-circle'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-yellow'>
                                <div class='inner'>
                                    <h3>$" . number_format(UpValidacionEntrega::whereMonth('fecha_entrega', now()->month)->sum('importe_autorizado'), 0, ',', '.') . "</h3>
                                    <p>Importe Mes</p>
                                </div>
                                <div class='icon'><i class='fa fa-dollar'></i></div>
                            </div>
                        </div>
                    </div>
                    <p><strong>💡 Tip:</strong> Use los filtros para buscar por afiliado, farmacia o rango de fechas. Cada validación incluye trazabilidad completa.</p>
                </div>
            </div>
        ";

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code after index table
        | ----------------------------------------------------------------------
        */
        $this->post_index_html = null;

        /*
        | ----------------------------------------------------------------------
        | Include Javascript File
        | ----------------------------------------------------------------------
        */
        $this->load_js = array();

        /*
        | ----------------------------------------------------------------------
        | Add css style at body
        | ----------------------------------------------------------------------
        */
        $this->style_css = NULL;

        /*
        | ----------------------------------------------------------------------
        | Include css File
        | ----------------------------------------------------------------------
        */
        $this->load_css = array();
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for button selected
    | ----------------------------------------------------------------------
    */
    public function actionButtonSelected($id_selected, $button_name)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate query of index result
    | ----------------------------------------------------------------------
    */
    public function hook_query_index(&$query)
    {
        $query->with(['consumo']);
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate row of index table html
    | ----------------------------------------------------------------------
    */
    public function hook_row_index($column_index, &$column_value)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before add data is execute
    | ----------------------------------------------------------------------
    */
    public function hook_before_add(&$postdata)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after add public static function called
    | ----------------------------------------------------------------------
    */
    public function hook_after_add($id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before update data is execute
    | ----------------------------------------------------------------------
    */
    public function hook_before_edit(&$postdata, $id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after edit public static function called
    | ----------------------------------------------------------------------
    */
    public function hook_after_edit($id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command before delete public static function called
    | ----------------------------------------------------------------------
    */
    public function hook_before_delete($id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after delete public static function called
    | ----------------------------------------------------------------------
    */
    public function hook_after_delete($id)
    {
        //Your code here
    }

    /**
     * Método custom: Ver Comprobante de Validación
     */
    public function getComprobante($id)
    {
        $validacion = UpValidacionEntrega::with(['consumo', 'afiliado'])->findOrFail($id);
        
        $data = [];
        $data['page_title'] = 'Comprobante de Validación de Entrega';
        $data['validacion'] = $validacion;
        $data['consumo'] = $validacion->consumo;
        $data['numero_comprobante'] = $validacion->generarNumeroComprobante();
        
        return view('up_validaciones_entrega.comprobante', $data);
    }

    /**
     * Método custom: Estadísticas de Validaciones
     */
    public function getEstadisticas()
    {
        $data = [];
        $data['page_title'] = 'Estadísticas de Validaciones de Entrega';
        
        // Estadísticas generales
        $data['stats_generales'] = UpValidacionEntrega::estadisticas();
        
        // Estadísticas del mes actual
        $mesActual = now()->startOfMonth();
        $data['stats_mes'] = UpValidacionEntrega::estadisticas($mesActual, now());
        
        // Top farmacias
        $data['top_farmacias'] = UpValidacionEntrega::topFarmacias(10, $mesActual, now());
        
        // Entregas por día (últimos 30 días)
        $data['entregas_por_dia'] = UpValidacionEntrega::entregasPorDia(now()->subDays(30), now());
        
        return view('up_validaciones_entrega.estadisticas', $data);
    }

    /**
     * Hook para personalizar vista de detalle
     */
    public function hook_before_detail(&$id, &$result)
    {
        $validacion = UpValidacionEntrega::with(['consumo'])->find($id);
        
        if ($validacion) {
            $result['validacion_info'] = [
                'numero_comprobante' => $validacion->generarNumeroComprobante(),
                'es_entrega_completa' => $validacion->es_entrega_completa,
                'cantidad_pendiente' => $validacion->cantidad_pendiente,
                'consumo_original' => $validacion->consumo,
            ];
        }
    }
}
