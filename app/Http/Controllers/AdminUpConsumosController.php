<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Services\UnionPersonalSoapService;
use App\Models\UpConsumo;
use App\Models\UpElegibilidad;
use App\Models\UpAutorizacionPrevia;
use App\Models\UpAnulacion;

class AdminUpConsumosController extends \crocodicstudio\crudbooster\controllers\CBController
{
    private $soapService;

    public function __construct()
    {
        parent::__construct();
        $this->soapService = new UnionPersonalSoapService();
    }

    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "desc";
        $this->limit = "20";
        $this->orderby = "fecha_tran,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = false; // Solo lectura
        $this->button_edit = false; // Solo lectura
        $this->button_delete = false; // Solo lectura
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "up_consumos";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Fecha", "name" => "fecha_tran", "callback" => function($row) {
            return date('d/m/Y H:i', strtotime($row->fecha_tran));
        }];
        $this->col[] = ["label" => "Afiliado", "name" => "afiliado"];
        $this->col[] = ["label" => "Apellido y Nombre", "name" => "apellidos", "callback" => function($row) {
            return $row->apellidos . ', ' . $row->nombres;
        }];
        $this->col[] = ["label" => "Plan", "name" => "nombre_modelo_plan"];
        $this->col[] = ["label" => "Prestación", "name" => "desc"];
        $this->col[] = ["label" => "Tipo", "name" => "tipo_pres", "callback" => function($row) {
            $tipos = ['P' => 'Prestación', 'M' => 'Medicamento', 'D' => 'Derivación'];
            return $tipos[$row->tipo_pres] ?? $row->tipo_pres;
        }];
        $this->col[] = ["label" => "Cant", "name" => "cant"];
        $this->col[] = ["label" => "Estado Flujo", "name" => "estado_flujo", "callback" => function($row) {
            $badges = [
                'pendiente' => 'default',
                'elegibilidad_ok' => 'info',
                'elegibilidad_no' => 'danger',
                'aprobado' => 'success',
                'rechazado' => 'danger',
                'entregado' => 'primary',
                'anulado' => 'warning',
            ];
            $color = $badges[$row->estado_flujo] ?? 'default';
            $texto = strtoupper(str_replace('_', ' ', $row->estado_flujo));
            return "<span class='badge badge-{$color}'>{$texto}</span>";
        }];
        $this->col[] = ["label" => "Observaciones", "name" => "observaciones_flujo", "callback" => function($row) {
            if (!empty($row->observaciones_flujo)) {
                $obs = strlen($row->observaciones_flujo) > 50 ? substr($row->observaciones_flujo, 0, 50) . '...' : $row->observaciones_flujo;
                return "<small class='text-muted'><i class='fa fa-comment'></i> " . htmlspecialchars($obs) . "</small>";
            }
            return '<small class="text-muted">Sin observaciones</small>';
        }];
        $this->col[] = ["label" => "Status CSV", "name" => "status", "callback" => function($row) {
            $badge = $row->status == 'OK' ? 'success' : ($row->status == 'NO' ? 'danger' : 'warning');
            return "<span class='label label-{$badge}'>{$row->status}</span>";
        }];
        $this->col[] = ["label" => "Importe Total", "name" => "imptot", "callback" => function($row) {
            return '$' . number_format($row->imptot, 2, ',', '.');
        }];
        $this->col[] = ["label" => "ID Autorización", "name" => "idaut"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        // Solo vista de detalles, no formulario de edición
        # END FORM DO NOT REMOVE THIS LINE

        # OLD START FORM
        # OLD END FORM

        /*
        | ----------------------------------------------------------------------
        | Sub Module
        | ----------------------------------------------------------------------
        | @label          = Label of action
        | @path           = Path of sub module
        | @foreign_key    = foreign key of sub module
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
        | @color       = Default is primary. (primary, warning, succecss, info)
        | @showIf      = If condition when action show. Use field alias. e.g : [id] == 1
        |
        */
        $this->addaction = array();

        // Acción: Consultar Elegibilidad (ELG)
        $this->addaction[] = [
            'label' => 'Consultar Elegibilidad',
            'url' => CRUDBooster::mainpath('consultar-elegibilidad/[id]'),
            'icon' => 'fa fa-user-check',
            'color' => 'info',
            'showIf' => "[estado_flujo] == 'pendiente' || [estado_flujo] == 'elegibilidad_no'",
            'confirmation' => true
        ];

        // Acción: Aprobar Prestación (AP)
        $this->addaction[] = [
            'label' => 'Aprobar Prestación',
            'url' => CRUDBooster::mainpath('aprobar-prestacion/[id]'),
            'icon' => 'fa fa-check-circle',
            'color' => 'success',
            'showIf' => "[estado_flujo] == 'elegibilidad_ok'",
            'confirmation' => true
        ];

        // Acción: Generar Validación de Entrega
        $this->addaction[] = [
            'label' => 'Generar Validación de Entrega',
            'url' => CRUDBooster::mainpath('generar-validacion-entrega/[id]'),
            'icon' => 'fa fa-clipboard-check',
            'color' => 'primary',
            'showIf' => "[estado_flujo] == 'aprobado'"
        ];

        // Acción: Ver Historial SOAP
        $this->addaction[] = [
            'label' => 'Ver Historial SOAP',
            'url' => CRUDBooster::mainpath('ver-historial-soap/[id]'),
            'icon' => 'fa fa-history',
            'color' => 'default'
        ];

        // Acción: Anular Transacción (ATR)
        $this->addaction[] = [
            'label' => 'Anular Transacción',
            'url' => CRUDBooster::mainpath('anular-transaccion/[id]'),
            'icon' => 'fa fa-times-circle',
            'color' => 'danger',
            'showIf' => "[estado_flujo] == 'aprobado' || [estado_flujo] == 'entregado'",
            'confirmation' => true
        ];

        /*
        | ----------------------------------------------------------------------
        | Add More Button Selected
        | ----------------------------------------------------------------------
        | @label       = Label of action
        | @icon        = Icon from fontawesome
        | @name        = Name of button
        */
        $this->button_selected = array();

        /*
        | ----------------------------------------------------------------------
        | Add alert message to this module at overheader
        $this->index_button[] = [
            'label' => 'Elegibilidad (ELG)',
            'url' => CRUDBooster::adminPath('up_elegibilidad'),
            'icon' => 'fa fa-check-circle',
            'color' => 'info'
        ];
        $this->index_button[] = [
            'label' => 'Autorización Previa (AP)',
            'url' => CRUDBooster::adminPath('up_autorizacion_previa'),
            'icon' => 'fa fa-check',
            'color' => 'success'
        ];
        $this->index_button[] = [
            'label' => 'Anulaciones (ATR)',
            'url' => CRUDBooster::adminPath('up_anulaciones'),
            'icon' => 'fa fa-times-circle',
            'color' => 'danger'
        ];
        $this->index_button[] = [
            'label' => 'Manual de Usuario',
            'url' => CRUDBooster::adminPath('manual-flujo-up'),
            'icon' => 'fa fa-book',
            'color' => 'info'
        ];
        $this->index_button[] = [
            'label' => 'Configurar Ambiente',
            'url' => CRUDBooster::adminPath('up_ambiente'),
            'icon' => 'fa fa-cogs',
            'color' => config('union_personal.ambiente') == 'test' ? 'success' : 'warning'
        ];        | ----------------------------------------------------------------------
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
        $this->index_button[] = [
            'label' => 'Nueva Solicitud',
            'url' => CRUDBooster::mainpath('../up_solicitudes/add'),
            'icon' => 'fa fa-plus',
            'color' => 'success'
        ];

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        | @condition = If condition. You may use field alias. E.g : [id] == 1
        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
        |
        */
        $this->table_row_color = array();
        $this->table_row_color[] = ['condition' => "[estado_flujo] == 'entregado'", 'color' => 'success'];
        $this->table_row_color[] = ['condition' => "[estado_flujo] == 'aprobado'", 'color' => 'info'];
        $this->table_row_color[] = ['condition' => "[estado_flujo] == 'elegibilidad_no' || [estado_flujo] == 'rechazado'", 'color' => 'danger'];
        $this->table_row_color[] = ['condition' => "[estado_flujo] == 'anulado'", 'color' => 'warning'];

        $this->pre_index_html = "
            <div class='panel panel-primary'>
                <div class='panel-body'>
                    <h4><i class='fa fa-exchange'></i> Flujo de Trabajo - Medicamentos Unión Personal</h4>
                    <p><strong>🔄 Flujo:</strong> Consumo Generado → Verificar Elegibilidad (ELG) → Aprobar (AP) → Marcar Entregado</p>
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class='small-box bg-gray'>
                                <div class='inner'>
                                    <p>1. Verificar Elegibilidad</p>
                                    <small>Consulta ELG a UP</small>
                                </div>
                                <div class='icon'><i class='fa fa-check-circle'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-aqua'>
                                <div class='inner'>
                                    <p>2. Aprobar Prestación</p>
                                    <small>Autorización AP</small>
                                </div>
                                <div class='icon'><i class='fa fa-check'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-green'>
                                <div class='inner'>
                                    <p>3. Marcar Entregado</p>
                                    <small>Farmacia confirma</small>
                                </div>
                                <div class='icon'><i class='fa fa-truck'></i></div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='small-box bg-purple'>
                                <div class='inner'>
                                    <p>4. Observaciones</p>
                                    <small>Notas del proceso</small>
                                </div>
                                <div class='icon'><i class='fa fa-comment'></i></div>
                            </div>
                        </div>
                    </div>
                    <p><strong>💡 Tip:</strong> Use los filtros para buscar por número de afiliado. Los botones de acción aparecen según el estado del consumo.</p>
                </div>
            </div>
            
            <div class='panel panel-info'>
                <div class='panel-heading'>
                    <h3 class='panel-title'><i class='fa fa-code'></i> API para Sistemas Externos</h3>
                </div>
                <div class='panel-body'>
                    <div class='row'>
                        <div class='col-md-6'>
                            <h5><strong>Endpoints Disponibles:</strong></h5>
                            <ul class='list-unstyled'>
                                <li><code>GET /api/up-consumos</code> - Listar consumos</li>
                                <li><code>GET /api/up-consumos/{id}</code> - Ver consumo específico</li>
                                <li><code>GET /api/up-consumos/stats</code> - Estadísticas</li>
                                <li><code>PUT /api/up-consumos/{id}/observaciones</code> - Actualizar observaciones</li>
                            </ul>
                        </div>
                        <div class='col-md-6'>
                            <h5><strong>Filtros Disponibles:</strong></h5>
                            <ul class='list-unstyled'>
                                <li><code>fecha_desde</code> - Filtrar desde fecha (Y-m-d)</li>
                                <li><code>fecha_hasta</code> - Filtrar hasta fecha (Y-m-d)</li>
                                <li><code>estado_flujo</code> - Filtrar por estado</li>
                                <li><code>codigo_afiliado</code> - Buscar por afiliado</li>
                                <li><code>per_page</code> - Elementos por página (default: 50)</li>
                            </ul>
                        </div>
                    </div>
                    <div class='alert alert-warning'>
                        <i class='fa fa-info-circle'></i> <strong>Ejemplo:</strong> 
                        <code>/api/up-consumos?fecha_desde=2024-10-01&fecha_hasta=2024-10-31&estado_flujo=pendiente</code>
                    </div>
                </div>
            </div>
        ";
    }

    public function hook_before_index(&$result)
    {
        //Your code here
    }

    public function hook_query_index(&$query)
    {
        //Your code here
    }

    public function hook_row_index($column_index, &$column_value)
    {
        //Your code here
    }

    /**
     * Método custom: Verificar Elegibilidad (ELG)
     */
    public function getVerificarElegibilidad($id)
    {
        if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $consumo = UpConsumo::findOrFail($id);

        if (!$consumo->puedeEjecutarELG()) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Este consumo no puede ejecutar ELG en su estado actual', 'warning');
            return;
        }

        $startTime = microtime(true);

        // Preparar params para ELG
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $consumo->afiliado,
            'plan' => $consumo->modelo_plan,
            'vercred' => null, // Se puede obtener de la tabla de afiliados si existe
            'prestador_id' => $consumo->cod_prestador ?? config('union_personal.prestador_id'),
            'usrid' => config('union_personal.user_id'),
            'usrpass' => config('union_personal.user_pass'),
            'verifid' => 'MANUAL',
            'prestaciones' => [[
                'tipo' => $consumo->tipo_pres,
                'id' => $consumo->cod_prestacion,
                'cant' => $consumo->cant,
            ]],
        ];

        // Ejecutar ELG
        $resultado = $this->soapService->ejecutarELG($params);

        // Crear registro de elegibilidad
        $elegibilidad = UpElegibilidad::crearDesdeRespuestaSOAP($params, $resultado);

        if ($resultado['success']) {
            // Actualizar consumo
            $consumo->update([
                'estado_flujo' => 'elegibilidad_ok',
                'elegibilidad_id' => $elegibilidad->id,
                'idtran_elegibilidad' => $resultado['idtran'],
                'fecha_elegibilidad' => now(),
                'usuario_elegibilidad' => CRUDBooster::myEmail(),
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad verificada exitosamente. ID TRAN: ' . $resultado['idtran'] . '. Ahora puede Aprobar la Prestación (AP).', 'success');
        } else {
            $consumo->update([
                'estado_flujo' => 'elegibilidad_no',
                'elegibilidad_id' => $elegibilidad->id,
                'fecha_elegibilidad' => now(),
                'usuario_elegibilidad' => CRUDBooster::myEmail(),
            ]);
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en elegibilidad: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método custom: Aprobar Prestación (AP)
     */
    public function getAprobarPrestacion($id)
    {
        if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $consumo = UpConsumo::findOrFail($id);

        if (!$consumo->puedeEjecutarAP()) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Debe verificar elegibilidad antes de aprobar', 'warning');
            return;
        }

        $startTime = microtime(true);

        // Preparar params para AP
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_AP_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $consumo->afiliado,
            'plan' => $consumo->modelo_plan,
            'prestador_id' => $consumo->cod_prestador ?? config('union_personal.prestador_id'),
            'usrid' => config('union_personal.user_id'),
            'usrpass' => config('union_personal.user_pass'),
            'contexto_tipo' => 'A',
            'fecha' => $consumo->fecha_tran->format('Y-m-d'),
            'prestaciones' => [[
                'tipo' => $consumo->tipo_pres,
                'id' => $consumo->cod_prestacion,
                'cant' => $consumo->cant,
            ]],
        ];

        // Ejecutar AP
        $resultado = $this->soapService->ejecutarAP($params);

        // Crear registro de autorización
        $autorizacion = UpAutorizacionPrevia::crearDesdeRespuestaSOAP($params, $resultado);

        if ($resultado['success']) {
            // Actualizar consumo
            $consumo->update([
                'estado_flujo' => 'aprobado',
                'autorizacion_id' => $autorizacion->id,
                'idtran_aprobacion' => $resultado['idtran'],
                'idaut' => $resultado['idaut'],
                'fecha_aprobacion' => now(),
                'usuario_aprobacion' => CRUDBooster::myEmail(),
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Prestación aprobada exitosamente. ID AUT: ' . $resultado['idaut'] . '. Ahora puede Validar la Entrega.', 'success');
        } else {
            $consumo->update([
                'estado_flujo' => 'rechazado',
                'autorizacion_id' => $autorizacion->id,
                'fecha_aprobacion' => now(),
                'usuario_aprobacion' => CRUDBooster::myEmail(),
            ]);
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en aprobación: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método custom: Validar Entrega
     */
    public function getValidarEntrega($id)
    /**
     * Hook para personalizar vista de detalle
     */
    public function hook_before_detail(&$id, &$result)
    {
        $consumo = UpConsumo::with(['elegibilidad', 'autorizacion', 'anulacion'])->find($id);
        
        if ($consumo) {
            $result['flujo_info'] = [
                'estado_actual' => $consumo->estado_flujo,
                'puede_elg' => $consumo->puedeEjecutarELG(),
                'puede_ap' => $consumo->puedeEjecutarAP(),
                'puede_entrega' => $consumo->puedeValidarEntrega(),
                'puede_anular' => $consumo->puedeAnular(),
                'elegibilidad' => $consumo->elegibilidad,
                'autorizacion' => $consumo->autorizacion,
                'anulacion' => $consumo->anulacion,
            ];
        }
    }

    /**
     * Método custom: Ver historial de transacciones SOAP
     */
    public function getVerHistorialSoap($id)
    {
        $consumo = UpConsumo::with(['elegibilidad', 'autorizacion', 'anulacion'])->findOrFail($id);
        
        $data = [];
        $data['page_title'] = 'Historial SOAP - Consumo: ' . $consumo->desc;
        $data['consumo'] = $consumo;
        
        return view('up_consumos.historial_soap', $data);
    }    {
        if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $consumo = UpConsumo::findOrFail($id);

        if (!$consumo->puedeValidarEntrega()) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'La prestación debe estar aprobada para validar entrega', 'warning');
            return;
        }

        // Actualizar consumo como entregado
        $consumo->update([
            'estado_flujo' => 'entregado',
            'fecha_entrega' => now(),
            'usuario_entrega' => CRUDBooster::myEmail(),
        ]);

        CRUDBooster::redirect(CRUDBooster::mainpath(), 'Entrega validada exitosamente. Flujo completado.', 'success');
    }

    /**
     * Método custom: Anular Transacción (ATR)
     */
    public function getAnularTransaccion($id)
    {
        if (!CRUDBooster::isDelete() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $consumo = UpConsumo::findOrFail($id);

        if (!$consumo->puedeAnular()) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Solo se pueden anular transacciones aprobadas o entregadas', 'warning');
            return;
        }

        if (empty($consumo->idtran_aprobacion)) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'No hay transacción aprobada para anular', 'warning');
            return;
        }

        // Redirigir a la nueva vista de anulación con datos precargados
        $params = [
            'afiliado' => $consumo->afiliado,
            'tipo_anulacion' => 'IDTRAN',
            'id_anulacion' => $consumo->idtran_aprobacion,
            'motivo' => 'Anulación desde Consumos UP - ' . $consumo->desc,
            'plan' => $consumo->modelo_plan,
            'consumo_id' => $consumo->id
        ];

        $queryString = http_build_query($params);
        return redirect('/admin/anulacion-up?' . $queryString);

        if ($resultado['success']) {
            // Actualizar consumo
            $consumo->update([
                'estado_flujo' => 'anulado',
                'anulacion_id' => $anulacion->id,
                'fecha_anulacion' => now(),
                'usuario_anulacion' => CRUDBooster::myEmail(),
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Transacción anulada exitosamente. ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error anulando transacción: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método custom: Marcar como Entregado (simplificado para farmacias)
     */
    public function getMarcarEntregado($id)
    {
        if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $consumo = UpConsumo::findOrFail($id);

        if ($consumo->estado_flujo !== 'aprobado') {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Solo se pueden marcar como entregados los consumos aprobados', 'warning');
            return;
        }

        $data = [];
        $data['page_title'] = 'Marcar como Entregado - ' . $consumo->desc;
        $data['consumo'] = $consumo;

        return view('up_consumos.marcar_entregado', $data);
    }

    /**
     * POST: Confirmar entrega
     */
    public function postMarcarEntregado()
    {
        $request = Request::instance();
        $id = $request->input('consumo_id');
        $observaciones = $request->input('observaciones', '');

        $consumo = UpConsumo::findOrFail($id);

        if ($consumo->estado_flujo !== 'aprobado') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden marcar como entregados los consumos aprobados'
            ], 400);
        }

        // Actualizar consumo como entregado
        $consumo->update([
            'estado_flujo' => 'entregado',
            'fecha_entrega' => now(),
            'usuario_entrega' => CRUDBooster::myEmail(),
            'observaciones_flujo' => $observaciones,
            'usuario_observaciones' => CRUDBooster::myEmail(),
            'fecha_observaciones' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Medicamento marcado como entregado exitosamente',
            'redirect' => CRUDBooster::mainpath()
        ]);
    }

    /**
     * Método custom: Gestionar Observaciones
     */
    public function getObservaciones($id)
    {
        $consumo = UpConsumo::findOrFail($id);

        $data = [];
        $data['page_title'] = 'Observaciones - ' . $consumo->desc;
        $data['consumo'] = $consumo;

        return view('up_consumos.observaciones', $data);
    }

    /**
     * POST: Actualizar observaciones
     */
    public function postObservaciones()
    {
        $request = Request::instance();
        $id = $request->input('consumo_id');
        $observaciones = $request->input('observaciones', '');

        $consumo = UpConsumo::findOrFail($id);

        $consumo->update([
            'observaciones_flujo' => $observaciones,
            'usuario_observaciones' => CRUDBooster::myEmail(),
            'fecha_observaciones' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Observaciones actualizadas correctamente',
            'redirect' => CRUDBooster::mainpath()
        ]);
    }

    /**
     * Método actualizado: Consultar Elegibilidad (ELG)
     */
    public function getConsultarElegibilidad($id)
    {
        $consumo = UpConsumo::findOrFail($id);
        
        if (!in_array($consumo->estado_flujo, ['pendiente', 'elegibilidad_no'])) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Este consumo no puede consultar elegibilidad', 'warning');
        }

        $startTime = microtime(true);
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $consumo->afiliado,
            'plan' => $consumo->modelo_plan,
            'prestaciones' => [[
                'tipo' => $consumo->tipo_pres,
                'id' => $consumo->cod_prestacion,
                'cant' => $consumo->cant,
            ]],
        ];

        $resultado = $this->soapService->ejecutarELG($params);
        
        if ($resultado['success']) {
            $consumo->update([
                'estado_flujo' => 'elegibilidad_ok',
                'fecha_elegibilidad' => now(),
                'usuario_elegibilidad' => CRUDBooster::myEmail(),
            ]);
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad confirmada. Puede aprobar la prestación.', 'success');
        } else {
            $consumo->update(['estado_flujo' => 'elegibilidad_no']);
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad rechazada: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método nuevo: Generar Validación de Entrega
     */
    public function getGenerarValidacionEntrega($id)
    {
        $consumo = UpConsumo::findOrFail($id);
        
        if ($consumo->estado_flujo !== 'aprobado') {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Solo se pueden generar validaciones para consumos aprobados', 'warning');
        }

        try {
            // Crear validación de entrega
            $validacion = \App\Models\UpValidacionEntrega::create([
                'consumo_id' => $consumo->id,
                'afiliado_codigo' => $consumo->afiliado,
                'afiliado_nombre' => $consumo->nombres,
                'afiliado_apellido' => $consumo->apellidos,
                'medicamento_codigo' => $consumo->cod_prestacion,
                'medicamento_descripcion' => $consumo->desc,
                'cantidad' => $consumo->cant,
                'importe_autorizado' => $consumo->imptot,
                'idaut' => $consumo->idaut,
                'fecha_entrega' => now(),
                'usuario_entrega' => CRUDBooster::myEmail(),
                'entrega_completa' => true,
            ]);

            $consumo->update([
                'estado_flujo' => 'entregado',
                'validacion_entrega_id' => $validacion->id,
                'fecha_validacion_entrega' => now(),
                'usuario_validacion_entrega' => CRUDBooster::myEmail(),
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Validación de entrega generada exitosamente. ID: ' . $validacion->id, 'success');
        } catch (\Exception $e) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error al generar validación: ' . $e->getMessage(), 'danger');
        }
    }
}
