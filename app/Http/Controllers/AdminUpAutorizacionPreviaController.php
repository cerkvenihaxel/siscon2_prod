<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Services\UnionPersonalSoapService;
use App\Models\UpAutorizacionPrevia;

class AdminUpAutorizacionPreviaController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->title_field = "codigo_afiliado";
        $this->limit = "20";
        $this->orderby = "created_at,desc";
        $this->global_privilege = false;
        $this->button_table_action = true;
        $this->button_bulk_action = false;
        $this->button_action_style = "button_icon";
        $this->button_add = true;
        $this->button_edit = false; // Solo lectura después de crear
        $this->button_delete = true;
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_import = false;
        $this->button_export = true;
        $this->table = "up_autorizacion_previa";
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Fecha", "name" => "created_at", "callback" => function($row) {
            return date('d/m/Y H:i', strtotime($row->created_at));
        }];
        $this->col[] = ["label" => "Código Afiliado", "name" => "codigo_afiliado"];
        $this->col[] = ["label" => "Afiliado", "name" => "afi_apellido", "callback" => function($row) {
            if ($row->afi_apellido && $row->afi_nombre) {
                return $row->afi_apellido . ', ' . $row->afi_nombre;
            }
            return '-';
        }];
        $this->col[] = ["label" => "Plan", "name" => "afi_plan_nombre"];
        $this->col[] = ["label" => "Status", "name" => "status", "callback" => function($row) {
            $badges = ['OK' => 'success', 'NO' => 'danger', 'PEND' => 'warning', 'ERROR' => 'danger'];
            $color = $badges[$row->status] ?? 'default';
            return "<span class='badge badge-{$color}'>{$row->status}</span>";
        }];
        $this->col[] = ["label" => "ID Autorización", "name" => "idaut"];
        $this->col[] = ["label" => "ID Transacción", "name" => "idtran"];
        $this->col[] = ["label" => "Importe Total", "name" => "importe_total", "callback" => function($row) {
            return '$' . number_format($row->importe_total, 2, ',', '.');
        }];
        $this->col[] = ["label" => "Usuario", "name" => "usuario_creador"];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];

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
            'help' => 'Código del afiliado en Unión Personal',
        ];

        $this->form[] = [
            'label' => 'TOKEN (4 dígitos)',
            'name' => 'token',
            'type' => 'text',
            'width' => 'col-sm-4',
            'placeholder' => '9999',
            'help' => 'TOKEN de credencial digital (opcional, usar 9999 para testing)',
        ];

        $this->form[] = [
            'label' => 'Plan',
            'name' => 'plan',
            'type' => 'text',
            'width' => 'col-sm-4',
            'placeholder' => 'Ej: 150',
            'help' => 'Solo si no usa TOKEN (150 para testing)',
        ];

        $this->form[] = [
            'label' => 'Versión Credencial',
            'name' => 'vercred',
            'type' => 'text',
            'width' => 'col-sm-4',
            'placeholder' => 'Ej: 45',
            'help' => 'Solo si no usa TOKEN (45 para testing)',
        ];

        $this->form[] = [
            'label' => 'Verificación ID',
            'name' => 'verifid',
            'type' => 'text',
            'value' => 'MANUAL',
            'help' => 'Método de verificación',
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
            'help' => 'ID del prestador en Unión Personal (8888 para testing)',
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
            'help' => '8888 para testing',
        ];

        $this->form[] = [
            'label' => 'Password SOAP',
            'name' => 'usrpass',
            'type' => 'password',
            'width' => 'col-sm-6',
            'value' => config('union_personal.user_pass'),
            'help' => '7777 para testing',
        ];

        // Contexto de Autorización
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-file-text"></i> AUTORIZACIÓN</h4>',
            'name' => 'header_autorizacion',
            'type' => 'header',
        ];

        $this->form[] = [
            'label' => 'Tipo de Contexto',
            'name' => 'contexto_tipo',
            'type' => 'select',
            'options' => [
                'A' => 'A - Ambulatorio',
                'I' => 'I - Internado',
                'U' => 'U - Urgencia',
            ],
            'value' => 'A',
            'help' => 'Contexto de atención',
        ];

        $this->form[] = [
            'label' => 'Fecha Autorización',
            'name' => 'fecha_autorizacion',
            'type' => 'date',
            'value' => date('Y-m-d'),
        ];

        // Prestaciones
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-medkit"></i> PRESTACIONES</h4>',
            'name' => 'header_prestaciones',
            'type' => 'header',
        ];

        $this->form[] = [
            'label' => 'Prestaciones (JSON)',
            'name' => 'prestaciones_json',
            'type' => 'textarea',
            'required' => true,
            'help' => 'Formato JSON: [{"tipo":"P","id":"1420107","cant":1}] - Usar prestaciones: 1420107 (Consulta Especializada), 1420101 (Consulta Médica)',
            'placeholder' => '[
  {"tipo": "P", "id": "1420107", "cant": 1, "descripcion": "Consulta Especializada"},
  {"tipo": "P", "id": "1420101", "cant": 1, "descripcion": "Consulta Médica"}
]',
        ];

        $this->form[] = [
            'label' => 'Observaciones',
            'name' => 'observaciones',
            'type' => 'textarea',
            'placeholder' => 'Notas adicionales sobre esta autorización...',
        ];

        // Campos ocultos
        $this->form[] = ['name' => 'msgid', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idtran', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idaut', 'type' => 'hidden'];
        $this->form[] = ['name' => 'status', 'type' => 'hidden'];
        $this->form[] = ['name' => 'response_code', 'type' => 'hidden'];
        $this->form[] = ['name' => 'response_message', 'type' => 'hidden'];
        $this->form[] = ['name' => 'prestaciones', 'type' => 'hidden'];
        $this->form[] = ['name' => 'response_data', 'type' => 'hidden'];
        $this->form[] = ['name' => 'usuario_creador', 'type' => 'hidden', 'value' => CRUDBooster::myName()];

        # END FORM DO NOT REMOVE THIS LINE

        /*
        | ----------------------------------------------------------------------
        | Add More Action Button / Menu
        | ----------------------------------------------------------------------
        */
        $this->addaction = array();
        $this->addaction[] = [
            'label' => 'Ver Transacciones SOAP',
            'url' => CRUDBooster::mainpath('ver-transacciones/[id]'),
            'icon' => 'fa fa-list',
            'color' => 'primary',
        ];

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        */
        $this->table_row_color = array();
        $this->table_row_color[] = ['condition' => "[status] == 'OK'", 'color' => 'success'];
        $this->table_row_color[] = ['condition' => "[status] == 'NO' || [status] == 'ERROR'", 'color' => 'danger'];

        $this->pre_index_html = "
            <div class='alert alert-info'>
                <h4><i class='fa fa-info-circle'></i> Información de Prueba - Autorización Previa</h4>
                <div class='row'>
                    <div class='col-md-6'>
                        <strong>📋 Afiliados de Prueba:</strong>
                        <ul class='list-unstyled' style='margin-left: 15px;'>
                            <li>• <strong>54715500</strong> - Plan 150 (Accord) - VerCred: 45</li>
                            <li>• <strong>54715300</strong> - Plan 2 - VerCred: 31</li>
                        </ul>
                        <strong>🔑 TOKEN:</strong> <code>9999</code> (desarrollo)
                    </div>
                    <div class='col-md-6'>
                        <strong>🏥 Prestaciones de Prueba:</strong>
                        <ul class='list-unstyled' style='margin-left: 15px;'>
                            <li>• <code>1420107</code> - Consulta Especializada</li>
                            <li>• <code>1420101</code> - Consulta Médica</li>
                        </ul>
                        <strong>📍 Contextos:</strong> A=Ambulatorio, I=Internado, U=Urgencia
                    </div>
                </div>
            </div>            <div class='panel panel-success'>
                <div class='panel-heading'>
                    <h3 class='panel-title'><i class='fa fa-check'></i> Consulta Rápida de Autorización Previa (AP)</h3>
                </div>
                <div class='panel-body'>
                    <form id='formConsultaAP' class='form-horizontal'>
                        <div class='row'>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Código Afiliado *</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='codigo_afiliado' class='form-control' placeholder='54715500' value='54715500' required>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>TOKEN</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='token' class='form-control' placeholder='9999' value='9999'>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Plan</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='plan' class='form-control' placeholder='150' value='150'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Versión Cred.</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='vercred' class='form-control' placeholder='45' value='45'>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Contexto</label>
                                    <div class='col-sm-8'>
                                        <select name='contexto_tipo' class='form-control'>
                                            <option value='A' selected>A - Ambulatorio</option>
                                            <option value='I'>I - Internado</option>
                                            <option value='U'>U - Urgencia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Prestación *</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='prestacion_id' class='form-control' placeholder='1420107' value='1420107' required>
                                        <small class='text-muted'>Ej: 1420107 (Consulta Especializada)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class='col-md-12 text-center'>
                                <button type='submit' class='btn btn-success btn-lg'>
                                    <i class='fa fa-check'></i> Autorizar Prestación
                                </button>
                                <button type='button' class='btn btn-default btn-lg' id='btnLimpiarFormAP'>
                                    <i class='fa fa-eraser'></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id='resultadoAP' style='margin-top: 20px; display: none;'></div>
                </div>
            </div>

            <script>
            (function() {
                function initConsultaAP() {
                    if (typeof jQuery === 'undefined') {
                        setTimeout(initConsultaAP, 100);
                        return;
                    }

                    jQuery(document).ready(function($) {
                        $('#btnLimpiarFormAP').on('click', function() {
                            $('#formConsultaAP')[0].reset();
                            $('#resultadoAP').slideUp();
                        });

                        $('#formConsultaAP').on('submit', function(e) {
                            e.preventDefault();

                            var btnSubmit = $(this).find('button[type=submit]');
                            var originalText = btnSubmit.html();
                            btnSubmit.prop('disabled', true).html('<i class=\'fa fa-spinner fa-spin\'></i> Autorizando...');

                            $.ajax({
                                url: '" . CRUDBooster::mainpath('consultar-ap') . "',
                                type: 'POST',
                                data: $(this).serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                                },
                                success: function(response) {
                                    $('#resultadoAP').html(response.html).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                },
                                error: function(xhr) {
                                    var errorMsg = 'Error al autorizar prestación';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    var errorHtml = '<div class=\'alert alert-danger\'><i class=\'fa fa-exclamation-triangle\'></i> ' + errorMsg + '</div>';
                                    $('#resultadoAP').html(errorHtml).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                }
                            });
                        });
                    });
                }

                initConsultaAP();
            })();
            </script>
        ";

        $this->index_button = array();
        $this->index_button[] = [
            'label' => 'Consumos UP',
            'url' => CRUDBooster::adminPath('up_consumos'),
            'icon' => 'fa fa-list',
            'color' => 'primary'
        ];
        $this->index_button[] = [
            'label' => 'Elegibilidad (ELG)',
            'url' => CRUDBooster::adminPath('up_elegibilidad'),
            'icon' => 'fa fa-check-circle',
            'color' => 'info'
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
        ];
    }

    /**
     * Hook antes de agregar - Ejecutar transacción SOAP
     */
    public function hook_before_add(&$postdata)
    {
        $startTime = microtime(true);

        // Parsear prestaciones JSON
        $prestaciones = [];
        if (!empty($postdata['prestaciones_json'])) {
            $prestaciones = json_decode($postdata['prestaciones_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                CRUDBooster::redirect(CRUDBooster::mainpath('add'), 'Error: formato JSON inválido en prestaciones', 'danger');
                return false;
            }
        }

        // Preparar params para AP
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_AP_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $postdata['codigo_afiliado'],
            'prestador_id' => $postdata['prestador_id'] ?? config('union_personal.prestador_id'),
            'prestador_nombre' => $postdata['prestador_nombre'] ?? null,
            'usrid' => $postdata['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $postdata['usrpass'] ?? config('union_personal.user_pass'),
            'verifid' => $postdata['verifid'] ?? 'MANUAL',
            'contexto_tipo' => $postdata['contexto_tipo'] ?? 'A',
            'fecha' => $postdata['fecha_autorizacion'] ?? now()->format('Y-m-d'),
            'prestaciones' => $prestaciones,
        ];

        // TOKEN o Plan/VerCred
        if (!empty($postdata['token'])) {
            $params['token'] = $postdata['token'];
        } else {
            $params['plan'] = $postdata['plan'] ?? null;
            $params['vercred'] = $postdata['vercred'] ?? null;
        }

        // Ejecutar AP
        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarAP($params);

        // Crear registro
        $registro = UpAutorizacionPrevia::crearDesdeRespuestaSOAP($params, $resultado);

        if ($resultado['success']) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Autorización aprobada exitosamente. ID AUT: ' . $resultado['idaut'] . ' | ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en autorización: ' . $resultado['message'], 'danger');
        }

        // Cancelar el guardado normal de CRUDBooster
        return false;
    }

    /**
     * Método custom: Ver Transacciones SOAP
     */
    public function getVerTransacciones($id)
    {
        $autorizacion = UpAutorizacionPrevia::with('transacciones')->findOrFail($id);

        $data = [];
        $data['page_title'] = 'Transacciones SOAP - AP: ' . $autorizacion->codigo_afiliado;
        $data['autorizacion'] = $autorizacion;

        return view('up_transacciones.index', $data);
    }

    /**
     * Método custom: Consultar AP sin guardar
     */
    public function postConsultarAp()
    {
        try {
            $startTime = microtime(true);
            $request = Request::instance();

            // Validar datos
            $codigoAfiliado = $request->input('codigo_afiliado');
            $prestacionId = $request->input('prestacion_id');

            if (empty($codigoAfiliado) || empty($prestacionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El código de afiliado y la prestación son requeridos'
                ], 400);
            }

            // Preparar prestación
            $prestaciones = [[
                'tipo' => 'P',
                'id' => $prestacionId,
                'cant' => 1
            ]];

            // Preparar params para AP
            $params = [
                'start_time' => $startTime,
                'msgid' => 'SISCON2_CONSULTA_AP_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
                'afiliado_codigo' => $codigoAfiliado,
                'prestador_id' => config('union_personal.prestador_id'),
                'usrid' => config('union_personal.user_id'),
                'usrpass' => config('union_personal.user_pass'),
                'verifid' => 'MANUAL',
                'contexto_tipo' => $request->input('contexto_tipo', 'A'),
                'prestaciones' => $prestaciones,
            ];

            // TOKEN o Plan/VerCred
            if (!empty($request->input('token'))) {
                $params['token'] = $request->input('token');
            } else {
                $params['plan'] = $request->input('plan') ?? null;
                $params['vercred'] = $request->input('vercred') ?? null;
            }

            // Ejecutar AP
            $soapService = new UnionPersonalSoapService();
            $resultado = $soapService->ejecutarAP($params);

            // Generar HTML de respuesta
            $html = $this->generarHtmlResultadoAP($resultado, $params, $prestaciones);

            return response()->json([
                'success' => $resultado['success'],
                'html' => $html,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en postConsultarAp', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar HTML con los resultados de AP
     */
    private function generarHtmlResultadoAP($resultado, $params, $prestaciones)
    {
        $html = '<div class="panel panel-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= '<div class="panel-heading">';
        $html .= '<h3 class="panel-title"><i class="fa fa-' . ($resultado['success'] ? 'check' : 'exclamation-triangle') . '"></i> ';
        $html .= 'Resultado de Autorización Previa</h3>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';

        // Estado general
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><span class="label label-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= $resultado['success'] ? 'AUTORIZACIÓN APROBADA' : 'AUTORIZACIÓN RECHAZADA';
        $html .= '</span></h4>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<hr>';

        // Información de la transacción
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<dl class="dl-horizontal">';
        $html .= '<dt>Código Afiliado:</dt><dd><strong>' . htmlspecialchars($params['afiliado_codigo']) . '</strong></dd>';
        $html .= '<dt>MSGID:</dt><dd><code>' . htmlspecialchars($params['msgid']) . '</code></dd>';

        if ($resultado['success'] && !empty($resultado['idtran'])) {
            $html .= '<dt>ID TRANSACCIÓN:</dt><dd><span class="label label-primary">' . htmlspecialchars($resultado['idtran']) . '</span></dd>';
        }

        if (!empty($resultado['idaut'])) {
            $html .= '<dt>ID AUTORIZACIÓN:</dt><dd><span class="label label-success">' . htmlspecialchars($resultado['idaut']) . '</span></dd>';
        }

        if (!empty($resultado['status'])) {
            $statusColor = $resultado['status'] == 'OK' ? 'success' : 'danger';
            $html .= '<dt>STATUS:</dt><dd><span class="label label-' . $statusColor . '">' . htmlspecialchars($resultado['status']) . '</span></dd>';
        }

        $html .= '</dl>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<dl class="dl-horizontal">';
        $html .= '<dt>Contexto:</dt><dd>' . htmlspecialchars($params['contexto_tipo']) . '</dd>';
        $html .= '<dt>Prestación:</dt><dd><code>' . htmlspecialchars($prestaciones[0]['id']) . '</code></dd>';
        $html .= '<dt>Fecha Consulta:</dt><dd>' . now()->format('d/m/Y H:i:s') . '</dd>';
        $html .= '</dl>';
        $html .= '</div>';
        $html .= '</div>';

        // Datos del afiliado
        if (!empty($resultado['data']['AFI'])) {
            $afi = $resultado['data']['AFI'];
            $html .= '<hr>';
            $html .= '<h4><i class="fa fa-user"></i> Datos del Afiliado</h4>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';
            if (!empty($afi['APELLIDO'])) $html .= '<dt>Apellido:</dt><dd>' . htmlspecialchars($afi['APELLIDO']) . '</dd>';
            if (!empty($afi['NOMBRE'])) $html .= '<dt>Nombre:</dt><dd>' . htmlspecialchars($afi['NOMBRE']) . '</dd>';
            if (!empty($afi['PLAN'])) $html .= '<dt>Plan:</dt><dd>' . htmlspecialchars($afi['PLAN']) . '</dd>';
            $html .= '</dl>';
            $html .= '</div>';
            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';
            if (!empty($afi['SEXO'])) $html .= '<dt>Sexo:</dt><dd>' . htmlspecialchars($afi['SEXO']) . '</dd>';
            if (!empty($afi['FNAC'])) $html .= '<dt>F. Nac:</dt><dd>' . htmlspecialchars($afi['FNAC']) . '</dd>';
            $html .= '</dl>';
            $html .= '</div>';
            $html .= '</div>';
        }

        // Mensaje de error
        if (!$resultado['success']) {
            $html .= '<hr>';
            $html .= '<div class="alert alert-danger">';
            $html .= '<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> ';
            $html .= htmlspecialchars($resultado['message'] ?? 'Error desconocido');
            $html .= '</div>';
        }

        // Mensaje adicional
        if (!empty($resultado['data']['RSPMSGGADIC'])) {
            $html .= '<div class="alert alert-info">';
            $html .= '<i class="fa fa-info-circle"></i> ' . htmlspecialchars($resultado['data']['RSPMSGGADIC']);
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
