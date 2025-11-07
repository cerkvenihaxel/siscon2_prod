<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Services\UnionPersonalSoapService;
use App\Models\UpSolicitud;
use App\Models\UpSolicitudItem;
use App\Models\AfiliadoConvenioUp;
use Illuminate\Support\Facades\Validator;

class AdminUpSolicitudesController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "nro_solicitud";
        $this->limit = "20";
        $this->orderby = "created_at,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = true;
        $this->button_edit = true;
        $this->button_delete = true;
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "up_solicitudes";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Nro. Solicitud", "name" => "nro_solicitud"];
        $this->col[] = ["label" => "Fecha", "name" => "fecha_solicitud", "callback" => function($row) {
            return $row->fecha_solicitud ? date('d/m/Y H:i', strtotime($row->fecha_solicitud)) : '-';
        }];
        $this->col[] = ["label" => "Afiliado", "name" => "codigo_afiliado"];
        $this->col[] = ["label" => "Estado", "name" => "estado", "callback" => function($row) {
            $badges = [
                'borrador' => 'default',
                'elegibilidad_ok' => 'info',
                'elegibilidad_error' => 'danger',
                'aprobada' => 'success',
                'rechazada' => 'danger',
                'anulada' => 'warning',
                'pendiente' => 'warning',
            ];
            $color = $badges[$row->estado] ?? 'default';
            $texto = strtoupper(str_replace('_', ' ', $row->estado));
            return "<span class='badge badge-{$color}'>{$texto}</span>";
        }];
        $this->col[] = ["label" => "ID Autorización", "name" => "idaut"];
        $this->col[] = ["label" => "ID Tran Aprobación", "name" => "idtran_aprobacion"];
        $this->col[] = ["label" => "Prestador", "name" => "prestador_nombre"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];

        // Número de solicitud (auto-generado)
        $this->form[] = [
            'label' => 'Nro. Solicitud',
            'name' => 'nro_solicitud',
            'type' => 'text',
            'readonly' => true,
            'value' => UpSolicitud::generarNroSolicitud(),
            'help' => 'Generado automáticamente',
        ];

        // Datos del Afiliado
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-user"></i> DATOS DEL AFILIADO</h4>',
            'name' => 'header_afiliado',
            'type' => 'header',
        ];

        $this->form[] = [
            'label' => 'Código de Afiliado',
            'name' => 'codigo_afiliado',
            'type' => 'text',
            'required' => true,
            'validation' => 'required|max:20',
            'placeholder' => 'Ej: 54715500',
        ];

        $this->form[] = [
            'label' => 'TOKEN (4 dígitos) - O dejar en blanco para usar Plan/VerCred',
            'name' => 'token_temp',
            'type' => 'text',
            'width' => 'col-sm-6',
            'placeholder' => '9999',
            'help' => 'TOKEN de credencial digital (opcional, vigencia 5 min)',
        ];

        $this->form[] = [
            'label' => 'Plan',
            'name' => 'plan_temp',
            'type' => 'text',
            'width' => 'col-sm-3',
            'placeholder' => 'Ej: 150',
            'help' => 'Solo si no usa TOKEN',
        ];

        $this->form[] = [
            'label' => 'Versión Credencial',
            'name' => 'vercred_temp',
            'type' => 'text',
            'width' => 'col-sm-3',
            'placeholder' => 'Ej: 45',
            'help' => 'Solo si no usa TOKEN',
        ];

        // Datos del Prestador
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-hospital-o"></i> DATOS DEL PRESTADOR</h4>',
            'name' => 'header_prestador',
            'type' => 'header',
        ];

        $this->form[] = [
            'label' => 'ID Prestador',
            'name' => 'prestador_id',
            'type' => 'text',
            'value' => config('union_personal.prestador_id'),
            'help' => 'ID del prestador en Unión Personal',
        ];

        $this->form[] = [
            'label' => 'Nombre Prestador',
            'name' => 'prestador_nombre',
            'type' => 'text',
            'placeholder' => 'Global Médica S.A.',
        ];

        $this->form[] = [
            'label' => 'Usuario SOAP',
            'name' => 'usrid',
            'type' => 'text',
            'width' => 'col-sm-6',
            'value' => config('union_personal.user_id'),
        ];

        $this->form[] = [
            'label' => 'Password SOAP',
            'name' => 'usrpass',
            'type' => 'password',
            'width' => 'col-sm-6',
            'value' => config('union_personal.user_pass'),
        ];

        // Prestaciones
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-medkit"></i> PRESTACIONES / MEDICAMENTOS</h4>',
            'name' => 'header_prestaciones',
            'type' => 'header',
        ];

        $columns = [];
        $columns[] = ['label' => 'Tipo', 'name' => 'tipo_prestacion', 'type' => 'select', 'options' => ['P' => 'Prestación', 'M' => 'Medicamento', 'D' => 'Derivación']];
        $columns[] = ['label' => 'Código', 'name' => 'cod_prestacion', 'type' => 'text'];
        $columns[] = ['label' => 'Descripción', 'name' => 'descripcion', 'type' => 'text'];
        $columns[] = ['label' => 'Cantidad', 'name' => 'cantidad', 'type' => 'number'];
        $columns[] = ['label' => 'Troquel', 'name' => 'troquel', 'type' => 'text'];
        $columns[] = ['label' => 'Cód. Barra', 'name' => 'cod_barra', 'type' => 'text'];

        $this->form[] = [
            'label' => 'Prestaciones',
            'name' => 'up_solicitud_items',
            'type' => 'child',
            'columns' => $columns,
            'table' => 'up_solicitud_items',
            'foreign_key' => 'solicitud_id',
            'min' => 1,
        ];

        // Estado y observaciones
        $this->form[] = [
            'label' => 'Estado',
            'name' => 'estado',
            'type' => 'select',
            'options' => [
                'borrador' => 'Borrador',
                'elegibilidad_ok' => 'Elegibilidad OK',
                'elegibilidad_error' => 'Elegibilidad Error',
                'aprobada' => 'Aprobada',
                'rechazada' => 'Rechazada',
                'anulada' => 'Anulada',
                'pendiente' => 'Pendiente',
            ],
            'value' => 'borrador',
        ];

        $this->form[] = [
            'label' => 'Observaciones',
            'name' => 'observaciones',
            'type' => 'textarea',
        ];

        $this->form[] = [
            'label' => 'Fecha Solicitud',
            'name' => 'fecha_solicitud',
            'type' => 'datetime',
            'value' => date('Y-m-d H:i:s'),
        ];

        // Campos ocultos para IDs SOAP
        $this->form[] = ['name' => 'msgid_elegibilidad', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idtran_elegibilidad', 'type' => 'hidden'];
        $this->form[] = ['name' => 'msgid_aprobacion', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idtran_aprobacion', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idaut', 'type' => 'hidden'];
        $this->form[] = ['name' => 'usuario_creador', 'type' => 'hidden', 'value' => CRUDBooster::myEmail()];

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
        $this->addaction[] = [
            'label' => 'Verificar Elegibilidad (ELG)',
            'url' => CRUDBooster::mainpath('verificar-elegibilidad/[id]'),
            'icon' => 'fa fa-check-circle',
            'color' => 'info',
            'showIf' => "[estado] == 'borrador' || [estado] == 'elegibilidad_error'",
        ];
        $this->addaction[] = [
            'label' => 'Aprobar Prestación (AP)',
            'url' => CRUDBooster::mainpath('aprobar-prestacion/[id]'),
            'icon' => 'fa fa-check',
            'color' => 'success',
            'showIf' => "[estado] == 'elegibilidad_ok'",
        ];
        $this->addaction[] = [
            'label' => 'Anular Transacción (ATR)',
            'url' => CRUDBooster::mainpath('anular-transaccion/[id]'),
            'icon' => 'fa fa-times',
            'color' => 'danger',
            'showIf' => "[estado] == 'aprobada' || [estado] == 'pendiente'",
        ];
        $this->addaction[] = [
            'label' => 'Ver Transacciones SOAP',
            'url' => CRUDBooster::mainpath('ver-transacciones/[id]'),
            'icon' => 'fa fa-list',
            'color' => 'primary',
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
            'label' => 'Ver Consumos Históricos',
            'url' => CRUDBooster::mainpath('../up_consumos'),
            'icon' => 'fa fa-history',
            'color' => 'default'
        ];

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        */
        $this->table_row_color = array();
        $this->table_row_color[] = ['condition' => "[estado] == 'aprobada'", 'color' => 'success'];
        $this->table_row_color[] = ['condition' => "[estado] == 'rechazada'", 'color' => 'danger'];
        $this->table_row_color[] = ['condition' => "[estado] == 'anulada'", 'color' => 'warning'];

        // JavaScript para flujo interactivo
        $this->script_js = "
            // Aquí se puede agregar JavaScript para validaciones client-side
            // y botones dinámicos de ELG/AP
        ";

        $this->pre_index_html = "
            <div class='panel panel-primary'>
                <div class='panel-body'>
                    <h4><i class='fa fa-file-text'></i> Solicitudes de Autorización - Unión Personal</h4>
                    <p>Gestión de solicitudes con flujo completo: <strong>ELG → AP → ATR</strong></p>
                    <p><strong>🌟 Los afiliados se crean automáticamente</strong> al verificar elegibilidad.</p>
                </div>
            </div>
        ";
    }

    /**
     * Hook antes de agregar
     */
    public function hook_before_add(&$postdata)
    {
        // Generar nro_solicitud si no existe
        if (empty($postdata['nro_solicitud'])) {
            $postdata['nro_solicitud'] = UpSolicitud::generarNroSolicitud();
        }

        // Guardar usuario creador
        $postdata['usuario_creador'] = CRUDBooster::myName();
    }

    /**
     * Método custom: Verificar Elegibilidad (ELG)
     */
    public function getVerificarElegibilidad($id)
    {
        if (!CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $solicitud = UpSolicitud::with('items')->findOrFail($id);

        // Preparar params para ELG
        $params = [
            'solicitud_id' => $solicitud->id,
            'afiliado_codigo' => $solicitud->codigo_afiliado,
            'prestador_id' => $solicitud->prestador_id,
            'usrid' => $solicitud->usrid,
            'usrpass' => $solicitud->usrpass,
            'prestaciones' => [],
        ];

        // Agregar prestaciones
        foreach ($solicitud->items as $item) {
            $params['prestaciones'][] = [
                'tipo' => $item->tipo_prestacion,
                'id' => $item->cod_prestacion,
                'cant' => $item->cantidad,
                'troquel' => $item->troquel,
                'codbarra' => $item->cod_barra,
            ];
        }

        // Ejecutar ELG 🌟 Autocrea afiliado si no existe
        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarELG($params);

        if ($resultado['success']) {
            // Actualizar solicitud
            $solicitud->update([
                'estado' => 'elegibilidad_ok',
                'idtran_elegibilidad' => $resultado['idtran'],
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad verificada exitosamente. Afiliado autocreado/actualizado. ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            $solicitud->update(['estado' => 'elegibilidad_error']);
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

        $solicitud = UpSolicitud::with('items')->findOrFail($id);

        // Preparar params para AP
        $params = [
            'solicitud_id' => $solicitud->id,
            'afiliado_codigo' => $solicitud->codigo_afiliado,
            'prestador_id' => $solicitud->prestador_id,
            'usrid' => $solicitud->usrid,
            'usrpass' => $solicitud->usrpass,
            'contexto_tipo' => 'A',
            'prestaciones' => [],
        ];

        // Agregar prestaciones
        foreach ($solicitud->items as $item) {
            $params['prestaciones'][] = [
                'tipo' => $item->tipo_prestacion,
                'id' => $item->cod_prestacion,
                'cant' => $item->cantidad,
                'troquel' => $item->troquel,
                'codbarra' => $item->cod_barra,
            ];
        }

        // Ejecutar AP
        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarAP($params);

        if ($resultado['success']) {
            // Actualizar solicitud
            $solicitud->update([
                'estado' => 'aprobada',
                'idtran_aprobacion' => $resultado['idtran'],
                'idaut' => $resultado['idaut'],
            ]);

            // Actualizar items con respuestas
            if (isset($resultado['data']['PR'])) {
                $items = $solicitud->items;
                foreach ($resultado['data']['PR'] as $index => $prData) {
                    if (isset($items[$index])) {
                        $items[$index]->actualizarDesdeRespuestaSOAP($prData);
                    }
                }
            }

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Prestación aprobada exitosamente. ID AUT: ' . $resultado['idaut'], 'success');
        } else {
            $solicitud->update(['estado' => 'rechazada']);
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en aprobación: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método custom: Anular Transacción (ATR)
     */
    public function getAnularTransaccion($id)
    {
        if (!CRUDBooster::isDelete() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $solicitud = UpSolicitud::findOrFail($id);

        if (empty($solicitud->idtran_aprobacion)) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'No hay transacción aprobada para anular', 'warning');
            return;
        }

        // Ejecutar ATR
        $params = [
            'solicitud_id' => $solicitud->id,
            'afiliado_codigo' => $solicitud->codigo_afiliado,
            'usrid' => $solicitud->usrid,
            'usrpass' => $solicitud->usrpass,
            'tipoidanul' => 'IDTRAN',
            'idanul' => $solicitud->idtran_aprobacion,
            'motivo' => 'Anulación desde sistema SISCON2',
        ];

        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarATR($params);

        if ($resultado['success']) {
            $solicitud->update([
                'estado' => 'anulada',
                'idtran_anulacion' => $resultado['idtran'],
            ]);

            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Transacción anulada exitosamente. ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error anulando transacción: ' . $resultado['message'], 'danger');
        }
    }

    /**
     * Método custom: Ver Transacciones SOAP
     */
    public function getVerTransacciones($id)
    {
        $solicitud = UpSolicitud::with('transacciones')->findOrFail($id);

        $data = [];
        $data['page_title'] = 'Transacciones SOAP - Solicitud: ' . $solicitud->nro_solicitud;
        $data['solicitud'] = $solicitud;

        return view('up_solicitudes.ver_transacciones', $data);
    }
}
