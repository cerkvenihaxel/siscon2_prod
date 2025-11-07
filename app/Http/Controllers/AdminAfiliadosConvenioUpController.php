<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;

class AdminAfiliadosConvenioUpController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "nombre_completo";
        $this->limit = "20";
        $this->orderby = "ultima_verificacion_soap,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = false; // Autocreados desde SOAP
        $this->button_edit = true; // Permitir edición manual si necesario
        $this->button_delete = false; // No eliminar, son autocreados
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "afiliados_convenio_up";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Código", "name" => "codigo_afiliado"];
        $this->col[] = ["label" => "Apellido y Nombre", "name" => "apellido", "callback" => function($row) {
            return $row->apellido . ', ' . $row->nombre;
        }];
        $this->col[] = ["label" => "Plan", "name" => "plan_nombre", "callback" => function($row) {
            return ($row->plan ?? '') . ' - ' . ($row->plan_nombre ?? '');
        }];
        $this->col[] = ["label" => "Tipo", "name" => "tipo_afiliado"];
        $this->col[] = ["label" => "Tit/Fam", "name" => "titofam"];
        $this->col[] = ["label" => "Localidad", "name" => "localidad"];
        $this->col[] = ["label" => "Provincia", "name" => "provincia"];
        $this->col[] = ["label" => "Última Verificación SOAP", "name" => "ultima_verificacion_soap", "callback" => function($row) {
            return $row->ultima_verificacion_soap ? date('d/m/Y H:i', strtotime($row->ultima_verificacion_soap)) : '-';
        }];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ['label' => 'Código de Afiliado', 'name' => 'codigo_afiliado', 'type' => 'text', 'readonly' => true];
        $this->form[] = ['label' => 'Apellido', 'name' => 'apellido', 'type' => 'text'];
        $this->form[] = ['label' => 'Nombre', 'name' => 'nombre', 'type' => 'text'];
        $this->form[] = ['label' => 'Plan', 'name' => 'plan', 'type' => 'text', 'width' => 'col-sm-6'];
        $this->form[] = ['label' => 'Nombre del Plan', 'name' => 'plan_nombre', 'type' => 'text', 'width' => 'col-sm-6'];
        $this->form[] = ['label' => 'Sexo', 'name' => 'sexo', 'type' => 'select', 'options' => ['M' => 'Masculino', 'F' => 'Femenino'], 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Fecha de Nacimiento', 'name' => 'fecha_nacimiento', 'type' => 'date', 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Edad', 'name' => 'edad', 'type' => 'number', 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Código Postal', 'name' => 'codigo_postal', 'type' => 'text', 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Localidad', 'name' => 'localidad', 'type' => 'text', 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Provincia', 'name' => 'provincia', 'type' => 'text', 'width' => 'col-sm-4'];
        $this->form[] = ['label' => 'Tipo de Afiliado', 'name' => 'tipo_afiliado', 'type' => 'text', 'width' => 'col-sm-6'];
        $this->form[] = ['label' => 'Titular/Familiar', 'name' => 'titofam', 'type' => 'select', 'options' => ['TIT' => 'Titular', 'FAM' => 'Familiar'], 'width' => 'col-sm-6'];
        $this->form[] = ['label' => 'Última Versión Credencial', 'name' => 'ultima_version_credencial', 'type' => 'text'];
        $this->form[] = ['label' => 'Última Verificación SOAP', 'name' => 'ultima_verificacion_soap', 'type' => 'datetime', 'readonly' => true];
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
            'label' => 'Ver Solicitudes',
            'url' => CRUDBooster::mainpath('../up_solicitudes?q=[codigo_afiliado]'),
            'icon' => 'fa fa-file-text',
            'color' => 'primary',
        ];
        $this->addaction[] = [
            'label' => 'Ver Transacciones SOAP',
            'url' => CRUDBooster::mainpath('../up_transacciones_soap?q=[codigo_afiliado]'),
            'icon' => 'fa fa-exchange',
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

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        */
        $this->table_row_color = array();

        $this->pre_index_html = "
            <div class='panel panel-success'>
                <div class='panel-body'>
                    <h4><i class='fa fa-users'></i> Afiliados Autocreados desde SOAP Unión Personal</h4>
                    <p>🌟 <strong>Estos afiliados se crean automáticamente</strong> al ejecutar operaciones ELG (Elegibilidad) exitosas.</p>
                    <p>Los datos se sincronizan desde Unión Personal y se actualizan con cada verificación.</p>
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
}
