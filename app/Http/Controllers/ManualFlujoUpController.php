<?php

namespace App\Http\Controllers;

use CRUDBooster;

class ManualFlujoUpController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "manual";
        $this->limit = "20";
        $this->orderby = "id,desc";
        $this->global_privilege = false;
        $this->button_table_action = false;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = false;
        $this->button_edit = false;
        $this->button_delete = false;
        $this->button_detail = false;
        $this->button_show = false;
        $this->button_filter = false;
        $this->button_import = false;
        $this->button_export = false;
        $this->table = "cms_users"; // Tabla dummy
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        $this->col = [];
        $this->form = [];

        $this->index_button = array();
        $this->index_button[] = [
            'label' => 'Volver a Consumos UP',
            'url' => CRUDBooster::adminPath('up_consumos'),
            'icon' => 'fa fa-arrow-left',
            'color' => 'default'
        ];
    }

    public function getIndex()
    {
        if (!CRUDBooster::isRead() && $this->global_privilege == false) {
            CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
        }

        $data = [];
        $data['page_title'] = 'Manual de Usuario - Flujo Unión Personal';
        $data['page_menu'] = 'Manual UP';
        $data['page_icon'] = 'fa fa-book';

        return view('manual_flujo_up', $data);
    }
}
