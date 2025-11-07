<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Services\UnionPersonalSoapService;
use App\Models\UpAnulacion;

class AdminUpAnulacionesController extends \crocodicstudio\crudbooster\controllers\CBController
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
        $this->table = "up_anulaciones";
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
        $this->col[] = ["label" => "Tipo Anulación", "name" => "tipoidanul", "callback" => function($row) {
            $tipos = [
                'IDTRAN' => '<span class="label label-info">IDTRAN</span>',
                'MSGID' => '<span class="label label-warning">MSGID</span>',
                'IDAUT' => '<span class="label label-primary">IDAUT</span>',
            ];
            return $tipos[$row->tipoidanul] ?? $row->tipoidanul;
        }];
        $this->col[] = ["label" => "ID Anulado", "name" => "idanul"];
        $this->col[] = ["label" => "Status", "name" => "status", "callback" => function($row) {
            $badges = ['OK' => 'success', 'NO' => 'danger', 'PEND' => 'warning', 'ERROR' => 'danger'];
            $color = $badges[$row->status] ?? 'default';
            return "<span class='badge badge-{$color}'>{$row->status}</span>";
        }];
        $this->col[] = ["label" => "ID Transacción", "name" => "idtran"];
        $this->col[] = ["label" => "Motivo", "name" => "motivo"];
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

        // Credenciales SOAP
        $this->form[] = [
            'label' => '<h4 class="text-primary"><i class="fa fa-key"></i> CREDENCIALES SOAP</h4>',
            'name' => 'header_soap',
            'type' => 'header',
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

        // Datos de Anulación
        $this->form[] = [
            'label' => '<h4 class="text-danger"><i class="fa fa-times-circle"></i> DATOS DE ANULACIÓN</h4>',
            'name' => 'header_anulacion',
            'type' => 'header',
        ];

        $this->form[] = [
            'label' => 'Tipo de ID a Anular',
            'name' => 'tipoidanul',
            'type' => 'select',
            'required' => true,
            'options' => [
                'IDTRAN' => 'IDTRAN - ID de Transacción',
                'MSGID' => 'MSGID - ID de Mensaje Interno',
                'IDAUT' => 'IDAUT - ID de Autorización',
            ],
            'value' => 'IDTRAN',
            'help' => 'IDTRAN: ID de transacción (ej: 326423927), MSGID: ID interno (ej: SISCON2_AP_xxx), IDAUT: ID de autorización (ej: 82502981)',
        ];

        $this->form[] = [
            'label' => 'ID a Anular',
            'name' => 'idanul',
            'type' => 'text',
            'required' => true,
            'validation' => 'required|max:100',
            'placeholder' => 'Ej: 1114XXXXXX o SISCON2_AP_xxxxx',
            'help' => 'Copie el ID exacto de una transacción previa (obténgalo de ELG o AP exitosos)',
        ];

        $this->form[] = [
            'label' => 'Motivo de Anulación',
            'name' => 'motivo',
            'type' => 'textarea',
            'required' => true,
            'validation' => 'required',
            'placeholder' => 'Describa el motivo de la anulación...',
            'help' => 'Este motivo quedará registrado en el sistema',
        ];

        $this->form[] = [
            'label' => 'Fecha Anulación',
            'name' => 'fecha_anulacion',
            'type' => 'date',
            'value' => date('Y-m-d'),
        ];

        $this->form[] = [
            'label' => 'Observaciones Adicionales',
            'name' => 'observaciones',
            'type' => 'textarea',
            'placeholder' => 'Notas adicionales sobre esta anulación...',
        ];

        // Campos ocultos
        $this->form[] = ['name' => 'msgid', 'type' => 'hidden'];
        $this->form[] = ['name' => 'idtran', 'type' => 'hidden'];
        $this->form[] = ['name' => 'status', 'type' => 'hidden'];
        $this->form[] = ['name' => 'response_code', 'type' => 'hidden'];
        $this->form[] = ['name' => 'response_message', 'type' => 'hidden'];
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
            'label' => 'Ver XML',
            'url' => CRUDBooster::mainpath('ver-xml/[id]'),
            'icon' => 'fa fa-code',
            'color' => 'info',
            'showIf' => "[idtran] != ''",
        ];
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
            <div class='alert alert-warning'>
                <h4><i class='fa fa-exclamation-triangle'></i> Información de Anulación - Unión Personal</h4>
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
                        <strong>🔄 Tipos de Anulación:</strong>
                        <ul class='list-unstyled' style='margin-left: 15px;'>
                            <li>• <strong>IDTRAN</strong> - ID de Transacción (ej: 326423927)</li>
                            <li>• <strong>MSGID</strong> - ID de Mensaje (ej: SISCON2_AP_xxx)</li>
                            <li>• <strong>IDAUT</strong> - ID de Autorización (ej: 82502981)</li>
                        </ul>
                        <strong>⚠️ IMPORTANTE:</strong> Las anulaciones son irreversibles
                    </div>
                </div>
            </div>            <div class='panel panel-danger'>
                <div class='panel-heading'>
                    <h3 class='panel-title'><i class='fa fa-times-circle'></i> Consulta Rápida de Anulación (ATR)</h3>
                </div>
                <div class='panel-body'>
                    <div class='alert alert-warning'>
                        <strong>⚠️ Importante:</strong> La anulación de transacciones es irreversible. Asegúrese de ingresar el ID correcto.
                    </div>

                    <form id='formConsultaATR' class='form-horizontal'>
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
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Versión Cred.</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='vercred' class='form-control' placeholder='45' value='45'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Tipo Anulación *</label>
                                    <div class='col-sm-8'>
                                        <select name='tipoidanul' class='form-control' required>
                                            <option value='IDTRAN' selected>IDTRAN - ID de Transacción</option>
                                            <option value='MSGID'>MSGID - ID de Mensaje</option>
                                            <option value='IDAUT'>IDAUT - ID de Autorización</option>
                                        </select>
                                        <small class='text-muted'>Seleccione el tipo de ID que desea anular</small>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>ID a Anular *</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='idanul' class='form-control' placeholder='1114062721' required>
                                        <small class='text-muted'>Ej: 1114062721 (IDTRAN)</small>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Motivo *</label>
                                    <div class='col-sm-8'>
                                        <textarea name='motivo' class='form-control' rows='3' placeholder='Motivo de la anulación...' required>Anulación de prueba desde SISCON2</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class='col-md-12 text-center'>
                                <button type='submit' class='btn btn-danger btn-lg'>
                                    <i class='fa fa-times-circle'></i> Anular Transacción
                                </button>
                                <button type='button' class='btn btn-default btn-lg' id='btnLimpiarFormATR'>
                                    <i class='fa fa-eraser'></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id='resultadoATR' style='margin-top: 20px; display: none;'></div>
                </div>
            </div>

            <script>
            (function() {
                function initConsultaATR() {
                    if (typeof jQuery === 'undefined') {
                        setTimeout(initConsultaATR, 100);
                        return;
                    }

                    jQuery(document).ready(function($) {
                        $('#btnLimpiarFormATR').on('click', function() {
                            $('#formConsultaATR')[0].reset();
                            $('#resultadoATR').slideUp();
                        });

                        $('#formConsultaATR').on('submit', function(e) {
                            e.preventDefault();

                            var btnSubmit = $(this).find('button[type=submit]');
                            var originalText = btnSubmit.html();
                            btnSubmit.prop('disabled', true).html('<i class=\'fa fa-spinner fa-spin\'></i> Anulando...');

                            $.ajax({
                                url: '" . CRUDBooster::mainpath('consultar-atr') . "',
                                type: 'POST',
                                data: $(this).serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                                },
                                success: function(response) {
                                    $('#resultadoATR').html(response.html).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                },
                                error: function(xhr) {
                                    var errorMsg = 'Error al anular transacción';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    var errorHtml = '<div class=\'alert alert-danger\'><i class=\'fa fa-exclamation-triangle\'></i> ' + errorMsg + '</div>';
                                    $('#resultadoATR').html(errorHtml).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                }
                            });
                        });
                    });
                }

                initConsultaATR();
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
            'label' => 'Autorización Previa (AP)',
            'url' => CRUDBooster::adminPath('up_autorizacion_previa'),
            'icon' => 'fa fa-check',
            'color' => 'success'
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

        // Preparar params para ATR
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_ATR_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $postdata['codigo_afiliado'],
            'usrid' => $postdata['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $postdata['usrpass'] ?? config('union_personal.user_pass'),
            'tipoidanul' => $postdata['tipoidanul'],
            'idanul' => $postdata['idanul'],
            'motivo' => $postdata['motivo'],
            'fecha' => $postdata['fecha_anulacion'] ?? now()->format('Y-m-d'),
        ];

        // TOKEN o Plan/VerCred
        if (!empty($postdata['token'])) {
            $params['token'] = $postdata['token'];
        } else {
            $params['plan'] = $postdata['plan'] ?? null;
            $params['vercred'] = $postdata['vercred'] ?? null;
        }

        // Ejecutar ATR
        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarATR($params);

        // Crear registro
        $registro = UpAnulacion::crearDesdeRespuestaSOAP($params, $resultado);

        if ($resultado['success']) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Anulación procesada exitosamente. ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en anulación: ' . $resultado['message'], 'danger');
        }

        // Cancelar el guardado normal de CRUDBooster
        return false;
    }

    /**
     * Método custom: Ver Transacciones SOAP
     */
    public function getVerTransacciones($id)
    {
        $anulacion = UpAnulacion::with('transacciones')->findOrFail($id);

        $data = [];
        $data['page_title'] = 'Transacciones SOAP - ATR: ' . $anulacion->codigo_afiliado;
        $data['anulacion'] = $anulacion;

        return view('up_transacciones.index', $data);
    }

    /**
     * Método custom: Consultar ATR sin guardar
     */
    public function postConsultarAtr()
    {
        try {
            $startTime = microtime(true);
            $request = Request::instance();

            // Validar datos
            $codigoAfiliado = $request->input('codigo_afiliado');
            $tipoidanul = $request->input('tipoidanul');
            $idanul = $request->input('idanul');
            $motivo = $request->input('motivo');

            if (empty($codigoAfiliado) || empty($tipoidanul) || empty($idanul) || empty($motivo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Todos los campos son requeridos'
                ], 400);
            }

            // Preparar params para ATR
            $params = [
                'start_time' => $startTime,
                'msgid' => 'SISCON2_CONSULTA_ATR_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
                'afiliado_codigo' => $codigoAfiliado,
                'usrid' => config('union_personal.user_id'),
                'usrpass' => config('union_personal.user_pass'),
                'tipoidanul' => $tipoidanul,
                'idanul' => $idanul,
                'motivo' => $motivo,
            ];

            // TOKEN o Plan/VerCred
            if (!empty($request->input('token'))) {
                $params['token'] = $request->input('token');
            } else {
                $params['plan'] = $request->input('plan') ?? null;
                $params['vercred'] = $request->input('vercred') ?? null;
            }

            // Ejecutar ATR
            $soapService = new UnionPersonalSoapService();
            $resultado = $soapService->ejecutarATR($params);

            // Generar HTML de respuesta
            $html = $this->generarHtmlResultadoATR($resultado, $params);

            return response()->json([
                'success' => $resultado['success'],
                'html' => $html,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en postConsultarAtr', [
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
     * Generar HTML con los resultados de ATR
     */
    private function generarHtmlResultadoATR($resultado, $params)
    {
        $html = '<div class="panel panel-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= '<div class="panel-heading">';
        $html .= '<h3 class="panel-title"><i class="fa fa-' . ($resultado['success'] ? 'check' : 'exclamation-triangle') . '"></i> ';
        $html .= 'Resultado de Anulación (ATR)</h3>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';

        // Estado general
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><span class="label label-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= $resultado['success'] ? 'ANULACIÓN EXITOSA' : 'ANULACIÓN RECHAZADA';
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

        if (!empty($resultado['status'])) {
            $statusColor = $resultado['status'] == 'OK' ? 'success' : 'danger';
            $html .= '<dt>STATUS:</dt><dd><span class="label label-' . $statusColor . '">' . htmlspecialchars($resultado['status']) . '</span></dd>';
        }

        $html .= '</dl>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<dl class="dl-horizontal">';
        $html .= '<dt>Tipo de Anulación:</dt><dd><span class="label label-info">' . htmlspecialchars($params['tipoidanul']) . '</span></dd>';
        $html .= '<dt>ID Anulado:</dt><dd><code>' . htmlspecialchars($params['idanul']) . '</code></dd>';
        $html .= '<dt>Fecha Consulta:</dt><dd>' . now()->format('d/m/Y H:i:s') . '</dd>';
        $html .= '</dl>';
        $html .= '</div>';
        $html .= '</div>';

        // Motivo
        $html .= '<hr>';
        $html .= '<h5><i class="fa fa-comment"></i> Motivo de Anulación:</h5>';
        $html .= '<p class="well well-sm">' . htmlspecialchars($params['motivo']) . '</p>';

        // Datos del afiliado
        if (!empty($resultado['data']['AFI'])) {
            $afi = $resultado['data']['AFI'];
            $html .= '<hr>';
            $html .= '<h4><i class="fa fa-user"></i> Datos del Afiliado</h4>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';
            if (!empty($afi['CODIGO'])) $html .= '<dt>Código:</dt><dd>' . htmlspecialchars($afi['CODIGO']) . '</dd>';
            if (!empty($afi['APELLIDO'])) $html .= '<dt>Apellido:</dt><dd>' . htmlspecialchars($afi['APELLIDO']) . '</dd>';
            if (!empty($afi['NOMBRE'])) $html .= '<dt>Nombre:</dt><dd>' . htmlspecialchars($afi['NOMBRE']) . '</dd>';
            $html .= '</dl>';
            $html .= '</div>';
            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';
            if (!empty($afi['PLAN'])) $html .= '<dt>Plan:</dt><dd>' . htmlspecialchars($afi['PLAN']) . '</dd>';
            if (!empty($afi['SEXO'])) $html .= '<dt>Sexo:</dt><dd>' . htmlspecialchars($afi['SEXO']) . '</dd>';
            if (!empty($afi['DOMICILIO'])) $html .= '<dt>Domicilio:</dt><dd>' . htmlspecialchars($afi['DOMICILIO']) . '</dd>';
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
