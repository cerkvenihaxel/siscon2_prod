<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Services\UnionPersonalSoapService;
use App\Models\UpElegibilidad;

class AdminUpElegibilidadController extends \crocodicstudio\crudbooster\controllers\CBController
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
        $this->table = "up_elegibilidad";
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
        $this->col[] = ["label" => "Plan", "name" => "afi_plan_nombre", "callback" => function($row) {
            return $row->afi_plan_nombre ? $row->afi_plan_nombre . ' (' . $row->afi_plan . ')' : $row->afi_plan;
        }];
        $this->col[] = ["label" => "Status", "name" => "status", "callback" => function($row) {
            $badges = ['OK' => 'success', 'NO' => 'danger', 'PEND' => 'warning', 'ERROR' => 'danger'];
            $color = $badges[$row->status] ?? 'default';
            return "<span class='badge badge-{$color}'>{$row->status}</span>";
        }];
        $this->col[] = ["label" => "ID Transacción", "name" => "idtran"];
        $this->col[] = ["label" => "Mensaje", "name" => "response_message"];
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
            'help' => 'TOKEN de credencial digital (usar 9999 para testing - no expira)',
        ];

        $this->form[] = [
            'label' => 'Plan',
            'name' => 'plan',
            'type' => 'text',
            'width' => 'col-sm-4',
            'placeholder' => 'Ej: 150',
            'help' => 'Solo si no usa TOKEN (150=Accord, 2=Plan Básico para testing)',
        ];

        $this->form[] = [
            'label' => 'Versión Credencial',
            'name' => 'vercred',
            'type' => 'text',
            'width' => 'col-sm-4',
            'placeholder' => 'Ej: 45',
            'help' => 'Solo si no usa TOKEN (45 para Plan 150, 31 para Plan 2)',
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

        $this->form[] = [
            'label' => 'Observaciones',
            'name' => 'observaciones',
            'type' => 'textarea',
            'placeholder' => 'Notas adicionales sobre esta consulta...',
        ];

        // Campos de solo lectura (resultado)
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
        $this->index_button = array();
        $this->index_button[] = [
            'label' => 'Consumos UP',
            'url' => CRUDBooster::adminPath('up_consumos'),
            'icon' => 'fa fa-list',
            'color' => 'primary'
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
        ];
        $this->pre_index_html = "
            <div class='alert alert-info'>
                <h4><i class='fa fa-info-circle'></i> Información de Prueba - Unión Personal</h4>
                <div class='row'>
                    <div class='col-md-6'>
                        <strong>📋 Afiliados de Prueba (TEST):</strong>
                        <ul class='list-unstyled' style='margin-left: 15px;'>
                            <li>• <strong>54715500</strong> - Plan 150 (Accord) - VerCred: 45</li>
                            <li>• <strong>54715300</strong> - Plan 2 - VerCred: 31</li>
                        </ul>
                        <strong>🔑 TOKEN de Desarrollo:</strong> <code>9999</code> (no expira)
                    </div>
                    <div class='col-md-6'>
                        <strong>🏥 Credenciales SOAP:</strong>
                        <ul class='list-unstyled' style='margin-left: 15px;'>
                            <li>• Usuario: <code>8888</code></li>
                            <li>• Password: <code>7777</code></li>
                            <li>• Prestador ID: <code>8888</code></li>
                        </ul>
                        <strong>⚠️ Nota:</strong> Solo para verificación, NO válido para facturar
                    </div>
                </div>
            </div>            <div class='panel panel-primary'>
                <div class='panel-heading'>
                    <h3 class='panel-title'><i class='fa fa-check-circle'></i> Consulta Rápida de Elegibilidad (ELG)</h3>
                </div>
                <div class='panel-body'>
                    <form id='formConsultaELG' class='form-horizontal'>
                        <div class='row'>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Código Afiliado *</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='codigo_afiliado' class='form-control' placeholder='Ej: 54715500' value='54715500' required>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>TOKEN</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='token' class='form-control' placeholder='9999' value='9999'>
                                        <small class='text-muted'>TOKEN de credencial digital (usar 9999 para testing)</small>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Plan</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='plan' class='form-control' placeholder='150' value='150'>
                                        <small class='text-muted'>Solo si no usa TOKEN</small>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='col-sm-4 control-label'>Versión Credencial</label>
                                    <div class='col-sm-8'>
                                        <input type='text' name='vercred' class='form-control' placeholder='45' value='45'>
                                        <small class='text-muted'>Solo si no usa TOKEN</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class='col-md-12 text-center'>
                                <button type='submit' class='btn btn-primary btn-lg'>
                                    <i class='fa fa-search'></i> Consultar Elegibilidad
                                </button>
                                <button type='button' class='btn btn-default btn-lg' id='btnLimpiarFormELG'>
                                    <i class='fa fa-eraser'></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id='resultadoELG' style='margin-top: 20px; display: none;'></div>
                </div>
            </div>

            <script>
            (function() {
                function initConsultaELG() {
                    if (typeof jQuery === 'undefined') {
                        setTimeout(initConsultaELG, 100);
                        return;
                    }

                    jQuery(document).ready(function($) {
                        // Botón limpiar
                        $('#btnLimpiarFormELG').on('click', function() {
                            $('#formConsultaELG')[0].reset();
                            $('#resultadoELG').slideUp();
                        });

                        // Submit del formulario
                        $('#formConsultaELG').on('submit', function(e) {
                            e.preventDefault();

                            var btnSubmit = $(this).find('button[type=submit]');
                            var originalText = btnSubmit.html();
                            btnSubmit.prop('disabled', true).html('<i class=\'fa fa-spinner fa-spin\'></i> Consultando...');

                            $.ajax({
                                url: '" . CRUDBooster::mainpath('consultar-elegibilidad') . "',
                                type: 'POST',
                                data: $(this).serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                                },
                                success: function(response) {
                                    $('#resultadoELG').html(response.html).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                },
                                error: function(xhr) {
                                    var errorMsg = 'Error al consultar elegibilidad';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    var errorHtml = '<div class=\'alert alert-danger\'><i class=\'fa fa-exclamation-triangle\'></i> ' + errorMsg + '</div>';
                                    $('#resultadoELG').html(errorHtml).slideDown();
                                    btnSubmit.prop('disabled', false).html(originalText);
                                }
                            });
                        });
                    });
                }

                initConsultaELG();
            })();
            </script>
        ";
    }

    /**
     * Hook antes de agregar - Ejecutar transacción SOAP
     */
    public function hook_before_add(&$postdata)
    {
        $startTime = microtime(true);

        // Preparar params para ELG
        $params = [
            'start_time' => $startTime,
            'msgid' => 'SISCON2_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
            'afiliado_codigo' => $postdata['codigo_afiliado'],
            'prestador_id' => $postdata['prestador_id'] ?? config('union_personal.prestador_id'),
            'usrid' => $postdata['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $postdata['usrpass'] ?? config('union_personal.user_pass'),
            'verifid' => $postdata['verifid'] ?? 'MANUAL',
        ];

        // TOKEN o Plan/VerCred
        if (!empty($postdata['token'])) {
            $params['token'] = $postdata['token'];
        } else {
            $params['plan'] = $postdata['plan'] ?? null;
            $params['vercred'] = $postdata['vercred'] ?? null;
        }

        // Ejecutar ELG
        $soapService = new UnionPersonalSoapService();
        $resultado = $soapService->ejecutarELG($params);

        // Crear registro
        $registro = UpElegibilidad::crearDesdeRespuestaSOAP($params, $resultado);

        if ($resultado['success']) {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Elegibilidad verificada exitosamente. ID TRAN: ' . $resultado['idtran'], 'success');
        } else {
            CRUDBooster::redirect(CRUDBooster::mainpath(), 'Error en elegibilidad: ' . $resultado['message'], 'danger');
        }

        // Cancelar el guardado normal de CRUDBooster
        return false;
    }

    /**
     * Método custom: Ver Transacciones SOAP
     */
    public function getVerTransacciones($id)
    {
        $elegibilidad = UpElegibilidad::with('transacciones')->findOrFail($id);

        $data = [];
        $data['page_title'] = 'Transacciones SOAP - ELG: ' . $elegibilidad->codigo_afiliado;
        $data['elegibilidad'] = $elegibilidad;

        return view('up_transacciones.index', $data);
    }

    /**
     * Método custom: Consultar Elegibilidad sin guardar
     */
    public function postConsultarElegibilidad()
    {
        try {
            $startTime = microtime(true);

            // Validar datos
            $request = Request::instance();
            $codigoAfiliado = $request->input('codigo_afiliado');

            if (empty($codigoAfiliado)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El código de afiliado es requerido'
                ], 400);
            }

            // Preparar params para ELG
            $params = [
                'start_time' => $startTime,
                'msgid' => 'SISCON2_CONSULTA_ELG_' . now()->format('YmdHis') . '_' . strtoupper(substr(uniqid(), -4)),
                'afiliado_codigo' => $codigoAfiliado,
                'prestador_id' => config('union_personal.prestador_id'),
                'usrid' => config('union_personal.user_id'),
                'usrpass' => config('union_personal.user_pass'),
                'verifid' => 'MANUAL',
            ];

            // TOKEN o Plan/VerCred
            if (!empty($request->input('token'))) {
                $params['token'] = $request->input('token');
            } else {
                $params['plan'] = $request->input('plan') ?? null;
                $params['vercred'] = $request->input('vercred') ?? null;
            }

            // Ejecutar ELG
            $soapService = new UnionPersonalSoapService();
            $resultado = $soapService->ejecutarELG($params);

            // Agregar información de debugging
            $params['response_time'] = number_format((microtime(true) - $startTime), 3);
            $params['status'] = $resultado['success'] ? 'OK' : 'ERROR';

            // Generar HTML de respuesta
            $html = $this->generarHtmlResultadoELG($resultado, $params);

            return response()->json([
                'success' => $resultado['success'],
                'html' => $html,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en postConsultarElegibilidad', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Generar HTML con los resultados de la consulta ELG
     */
    private function generarHtmlResultadoELG($resultado, $params)
    {
        $html = '<div class="panel panel-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= '<div class="panel-heading">';
        $html .= '<h3 class="panel-title"><i class="fa fa-' . ($resultado['success'] ? 'check-circle' : 'exclamation-triangle') . '"></i> ';
        $html .= 'Resultado de Consulta de Elegibilidad</h3>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';

        // Estado general
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><span class="label label-' . ($resultado['success'] ? 'success' : 'danger') . '">';
        $html .= $resultado['success'] ? 'CONSULTA EXITOSA' : 'ERROR EN CONSULTA';
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
            $statusColor = $resultado['status'] == 'OK' ? 'success' : ($resultado['status'] == 'NO' ? 'danger' : 'warning');
            $html .= '<dt>STATUS:</dt><dd><span class="label label-' . $statusColor . '">' . htmlspecialchars($resultado['status']) . '</span></dd>';
        }

        $html .= '</dl>';
        $html .= '</div>';

        // Tiempos de respuesta
        $html .= '<div class="col-md-6">';
        $html .= '<dl class="dl-horizontal">';

        if (!empty($resultado['response_time'])) {
            $html .= '<dt>Tiempo de Respuesta:</dt><dd>' . number_format($resultado['response_time'], 3) . ' seg</dd>';
        }

        $html .= '<dt>Fecha Consulta:</dt><dd>' . now()->format('d/m/Y H:i:s') . '</dd>';

        if (!empty($params['token'])) {
            $html .= '<dt>Método:</dt><dd>TOKEN (<code>' . htmlspecialchars($params['token']) . '</code>)</dd>';
        } else {
            $html .= '<dt>Método:</dt><dd>Plan/VerCred (<code>' . $params['plan'] . '/' . $params['vercred'] . '</code>)</dd>';
        }

        $html .= '</dl>';
        $html .= '</div>';
        $html .= '</div>';

        // Datos del afiliado (si hay)
        if ($resultado['success'] && !empty($resultado['data'])) {
            $data = $resultado['data'];

            $html .= '<hr>';
            $html .= '<h4><i class="fa fa-user"></i> Datos del Afiliado</h4>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';

            if (!empty($data['AFI']['CODIGO'])) {
                $html .= '<dt>Código:</dt><dd><strong>' . htmlspecialchars($data['AFI']['CODIGO']) . '</strong></dd>';
            }
            if (!empty($data['AFI']['APELLIDO'])) {
                $html .= '<dt>Apellido:</dt><dd>' . htmlspecialchars($data['AFI']['APELLIDO']) . '</dd>';
            }
            if (!empty($data['AFI']['NOMBRE'])) {
                $html .= '<dt>Nombre:</dt><dd>' . htmlspecialchars($data['AFI']['NOMBRE']) . '</dd>';
            }
            if (!empty($data['AFI']['SEXO'])) {
                $html .= '<dt>Sexo:</dt><dd>' . htmlspecialchars($data['AFI']['SEXO']) . '</dd>';
            }
            if (!empty($data['AFI']['FNAC'])) {
                $html .= '<dt>F. Nacimiento:</dt><dd>' . htmlspecialchars($data['AFI']['FNAC']) . '</dd>';
            }
            if (!empty($data['AFI']['EDAD'])) {
                $html .= '<dt>Edad:</dt><dd>' . htmlspecialchars($data['AFI']['EDAD']) . ' años</dd>';
            }

            $html .= '</dl>';
            $html .= '</div>';

            $html .= '<div class="col-md-6">';
            $html .= '<dl class="dl-horizontal">';

            if (!empty($data['AFI']['PLAN'])) {
                $html .= '<dt>Plan:</dt><dd><span class="label label-info">' . htmlspecialchars($data['AFI']['PLAN']) . '</span></dd>';
            }
            if (!empty($data['AFI']['PLAN_NOMBRE'])) {
                $html .= '<dt>Nombre Plan:</dt><dd>' . htmlspecialchars($data['AFI']['PLAN_NOMBRE']) . '</dd>';
            }
            if (!empty($data['AFI']['TIPOAFI'])) {
                $html .= '<dt>Tipo Afiliado:</dt><dd>' . htmlspecialchars($data['AFI']['TIPOAFI']) . '</dd>';
            }
            if (!empty($data['AFI']['TITOFAM'])) {
                $html .= '<dt>Tit/Fam:</dt><dd>' . htmlspecialchars($data['AFI']['TITOFAM']) . '</dd>';
            }
            if (!empty($data['AFI']['CODPOS'])) {
                $html .= '<dt>Código Postal:</dt><dd>' . htmlspecialchars($data['AFI']['CODPOS']) . '</dd>';
            }
            if (!empty($data['AFI']['LOCALIDAD'])) {
                $html .= '<dt>Localidad:</dt><dd>' . htmlspecialchars($data['AFI']['LOCALIDAD']) . '</dd>';
            }
            if (!empty($data['AFI']['PROVINCIA'])) {
                $html .= '<dt>Provincia:</dt><dd>' . htmlspecialchars($data['AFI']['PROVINCIA']) . '</dd>';
            }

            $html .= '</dl>';
            $html .= '</div>';
            $html .= '</div>';
        }

        // Mensaje de error si hay
        if (!$resultado['success']) {
            $html .= '<hr>';
            $html .= '<div class="alert alert-danger">';
            $html .= '<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> ';
            $html .= htmlspecialchars($resultado['message'] ?? 'Error desconocido');
            $html .= '</div>';

            // Información de debugging adicional
            $html .= '<div class="panel panel-warning">';
            $html .= '<div class="panel-heading"><h4 class="panel-title"><i class="fa fa-wrench"></i> Información de Debugging</h4></div>';
            $html .= '<div class="panel-body">';
            $html .= '<dl class="dl-horizontal">';
            $html .= '<dt>Endpoint SOAP:</dt><dd><code>' . htmlspecialchars(config('union_personal.test.endpoint')) . '</code></dd>';
            $html .= '<dt>Prestador ID:</dt><dd><code>' . htmlspecialchars(config('union_personal.prestador_id')) . '</code></dd>';
            $html .= '<dt>Usuario SOAP:</dt><dd><code>' . htmlspecialchars(config('union_personal.user_id')) . '</code></dd>';
            $html .= '<dt>Ambiente:</dt><dd><code>' . htmlspecialchars(config('union_personal.ambiente')) . '</code></dd>';

            // Verificar última transacción SOAP en la BD
            $ultimaTransaccion = \DB::table('up_transacciones_soap')
                ->where('transaction_type', 'ELG')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimaTransaccion) {
                $html .= '<dt>Última transacción ELG:</dt><dd>' . date('d/m/Y H:i:s', strtotime($ultimaTransaccion->created_at)) . '</dd>';
                $html .= '<dt>Status última trans:</dt><dd><span class="label label-' . ($ultimaTransaccion->status == 'OK' ? 'success' : 'danger') . '">' . $ultimaTransaccion->status . '</span></dd>';
            }

            $html .= '</dl>';

            $html .= '<hr>';
            $html .= '<h5><i class="fa fa-bug"></i> Posibles Causas:</h5>';
            $html .= '<ul>';
            $html .= '<li>El servicio SOAP de Unión Personal no está respondiendo</li>';
            $html .= '<li>El endpoint configurado es incorrecto</li>';
            $html .= '<li>Las credenciales (Usuario/Password) son inválidas</li>';
            $html .= '<li>El servidor no tiene acceso a la red</li>';
            $html .= '<li>El WSDL no es accesible</li>';
            $html .= '</ul>';

            $html .= '<h5><i class="fa fa-check"></i> Verificaciones Recomendadas:</h5>';
            $html .= '<ol>';
            $html .= '<li>Verificar que el archivo <code>.env</code> tenga las configuraciones correctas</li>';
            $html .= '<li>Revisar los logs en <code>storage/logs/laravel.log</code></li>';
            $html .= '<li>Verificar conectividad con: <code>telnet ' . parse_url(config('union_personal.test.endpoint'), PHP_URL_HOST) . ' ' . parse_url(config('union_personal.test.endpoint'), PHP_URL_PORT) . '</code></li>';
            $html .= '<li>Consultar la tabla <strong>Transacciones SOAP</strong> para ver el historial de errores</li>';
            $html .= '</ol>';

            $html .= '</div>';
            $html .= '</div>';
        }

        // Mensaje adicional
        if (!empty($resultado['data']['MENSAJE'])) {
            $html .= '<hr>';
            $html .= '<div class="alert alert-info">';
            $html .= '<i class="fa fa-info-circle"></i> <strong>Mensaje:</strong> ';
            $html .= htmlspecialchars($resultado['data']['MENSAJE']);
            $html .= '</div>';
        }

        $html .= '</div>'; // panel-body
        $html .= '</div>'; // panel

        return $html;
    }
}
