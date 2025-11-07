<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;

class AdminUpTransaccionesSoapController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "transaction_type";
        $this->limit = "20";
        $this->orderby = "created_at,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = false; // Solo lectura
        $this->button_edit = false; // Solo lectura
        $this->button_delete = true; // Permitir limpiar logs antiguos
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "up_transacciones_soap";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Fecha/Hora", "name" => "created_at", "callback" => function($row) {
            return date('d/m/Y H:i:s', strtotime($row->created_at));
        }];
        $this->col[] = ["label" => "Tipo", "name" => "transaction_type", "callback" => function($row) {
            $badges = [
                'ELG' => 'info',
                'AP' => 'success',
                'ATR' => 'warning',
                'TestWs' => 'default',
            ];
            $color = $badges[$row->transaction_type] ?? 'default';
            return "<span class='badge badge-{$color}'>{$row->transaction_type}</span>";
        }];
        $this->col[] = ["label" => "Afiliado", "name" => "afiliado_codigo"];
        $this->col[] = ["label" => "IDTRAN", "name" => "idtran"];
        $this->col[] = ["label" => "IDAUT", "name" => "idaut"];
        $this->col[] = ["label" => "Status", "name" => "status", "callback" => function($row) {
            $color = $row->status == 'OK' ? 'success' : ($row->status == 'ERROR' ? 'danger' : 'warning');
            return "<span class='badge badge-{$color}'>{$row->status}</span>";
        }];
        $this->col[] = ["label" => "Código Respuesta", "name" => "response_code"];
        $this->col[] = ["label" => "Tiempo (ms)", "name" => "execution_time_ms"];
        $this->col[] = ["label" => "Solicitud ID", "name" => "solicitud_id", "join" => "up_solicitudes,nro_solicitud"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        // Solo vista de detalles
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
            'label' => 'Ver XML Request',
            'url' => CRUDBooster::mainpath('ver-request-xml/[id]'),
            'icon' => 'fa fa-code',
            'color' => 'info',
            'showIf' => "[request_xml] != ''",
        ];
        $this->addaction[] = [
            'label' => 'Ver XML Response',
            'url' => CRUDBooster::mainpath('ver-response-xml/[id]'),
            'icon' => 'fa fa-code',
            'color' => 'success',
            'showIf' => "[response_xml] != ''",
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

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        */
        $this->table_row_color = array();
        $this->table_row_color[] = ['condition' => "[status] == 'OK'", 'color' => 'success'];
        $this->table_row_color[] = ['condition' => "[status] == 'ERROR'", 'color' => 'danger'];

        $this->pre_index_html = "
            <div class='panel panel-info'>
                <div class='panel-body'>
                    <h4><i class='fa fa-exchange'></i> Log de Transacciones SOAP - Unión Personal</h4>
                    <p>Registro completo de todas las transacciones SOAP ejecutadas con Unión Personal.</p>
                    <p>Incluye requests, responses y tiempos de ejecución para auditoría y debugging.</p>
                </div>
            </div>
        ";
    }

    /**
     * Método custom: Ver XML Request
     */
    public function getVerRequestXml($id)
    {
        if (!CRUDBooster::isRead() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $transaccion = \App\Models\UpTransaccionSoap::findOrFail($id);

        $data = [];
        $data['page_title'] = 'Request XML - Transacción #' . $id;
        $data['transaccion'] = $transaccion;
        $data['xml'] = $transaccion->request_xml;

        // Formatear XML
        try {
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($data['xml']);
            $data['xml_formateado'] = htmlspecialchars($dom->saveXML());
        } catch (\Exception $e) {
            $data['xml_formateado'] = htmlspecialchars($data['xml']);
        }

        return view('up_transacciones.ver_xml', $data);
    }

    /**
     * Método custom: Ver XML Response
     */
    public function getVerResponseXml($id)
    {
        if (!CRUDBooster::isRead() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $transaccion = \App\Models\UpTransaccionSoap::findOrFail($id);

        $data = [];
        $data['page_title'] = 'Response XML - Transacción #' . $id;
        $data['transaccion'] = $transaccion;
        $data['xml'] = $transaccion->response_xml;

        // Formatear XML
        try {
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($data['xml']);
            $data['xml_formateado'] = htmlspecialchars($dom->saveXML());
        } catch (\Exception $e) {
            $data['xml_formateado'] = htmlspecialchars($data['xml']);
        }

        return view('up_transacciones.ver_xml', $data);
    }
}
