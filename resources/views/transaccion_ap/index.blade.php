<!DOCTYPE html>
<html>
<head>
    <title>Transacción AP (Consumo de prestaciones)</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Material UI CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-container { margin-top: 20px; padding: 20px; }
        .step-card { margin-bottom: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .step-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; }
        .step-number { background: rgba(255,255,255,0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
        .step-content { padding: 24px; }
        .input-field input:focus + label, .input-field input:valid + label { color: #667eea !important; }
        .input-field input:focus { border-bottom: 1px solid #667eea !important; box-shadow: 0 1px 0 0 #667eea !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; }
        .btn-danger { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); border: none; }
        .progress-container { margin: 20px 0; }
        .status-chip { padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .floating-action { position: fixed; bottom: 30px; right: 30px; z-index: 1000; }
        .result-card { margin-top: 20px; border-left: 4px solid #667eea; }
        .medication-item { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin: 8px 0; background: white; }
        .select2-container--default .select2-selection--single { height: 48px; border: none; border-bottom: 1px solid #9e9e9e; border-radius: 0; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 46px; padding-left: 0; }
    </style>
</head>
<body>
@include('components.up_topbar')
@include('components.up_sidebar')

<div class="container main-container">
    <!-- Header -->
    <div class="row">
        <div class="col s12">
            <div class="card-panel teal lighten-5">
                <h4 class="teal-text text-darken-2">
                    <i class="material-icons left">medical_services</i>
                    Transacción AP - Consumo de Prestaciones
                </h4>
                <p class="grey-text">Sistema integrado de autorización y gestión de medicamentos</p>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-container">
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s6 center">
                        <div id="step1-indicator" class="status-chip status-pending">
                            <i class="material-icons tiny left">person_search</i>
                            1. Validación de Afiliado
                        </div>
                    </div>
                    <div class="col s6 center">
                        <div id="step2-indicator" class="status-chip status-pending">
                            <i class="material-icons tiny left">medication</i>
                            2. Medicamentos
                        </div>
                    </div>
                </div>
                <div class="progress">
                    <div id="progress-bar" class="determinate" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 1: Validación de Afiliado -->
    <div class="card step-card">
        <div class="step-header">
            <div class="row valign-wrapper">
                <div class="step-number">1</div>
                <div>
                    <h5 class="white-text no-margin">Validación de Afiliado</h5>
                    <p class="white-text opacity-8 no-margin">Verificar estado del afiliado en tiempo real</p>
                </div>
            </div>
        </div>
        <div class="step-content">
            <form id="elegibilidadForm">
                <div class="row">
                    <div class="input-field col s12 m3">
                        <i class="material-icons prefix">person</i>
                        <input id="afiliado" type="text" class="validate" required>
                        <label for="afiliado">Código de Afiliado</label>
                        <span class="helper-text" data-error="Campo requerido" data-success="Válido">Ingrese el código del afiliado</span>
                    </div>
                    <div class="input-field col s12 m2">
                        <i class="material-icons prefix">vpn_key</i>
                        <input id="token" type="text" class="validate" value="9999">
                        <label for="token">TOKEN</label>
                        <span class="helper-text">TOKEN credencial</span>
                    </div>
                    <div class="input-field col s12 m2">
                        <input id="plan" type="text" class="validate" value="150">
                        <label for="plan">Plan</label>
                        <span class="helper-text">Solo si no usa TOKEN</span>
                    </div>
                    <div class="input-field col s12 m2">
                        <input id="vercred" type="text" class="validate" value="45">
                        <label for="vercred">VerCred</label>
                        <span class="helper-text">Solo si no usa TOKEN</span>
                    </div>
                    <div class="input-field col s12 m1">
                        <select id="tipo_documento">
                            <option value="DNI" selected>DNI</option>
                            <option value="LC">LC</option>
                            <option value="LE">LE</option>
                        </select>
                        <label>Tipo Doc</label>
                    </div>
                    <div class="col s12 m2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light full-width" style="margin-top: 25px;">
                            <i class="material-icons left">search</i>
                            Consultar
                        </button>
                    </div>
                </div>
            </form>
            
            <div id="elegibilidadResult" class="result-card card" style="display: none;">
                <div class="card-content">
                    <div id="elegibilidadData"></div>
                    <div class="center" style="margin-top: 15px;">
                        <button type="button" id="verXmlBtn" class="btn btn-small blue waves-effect waves-light" style="display: none;">
                            <i class="material-icons left">code</i>
                            VER XML
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 2: Búsqueda y Autorización de Medicamentos -->
    <div class="card step-card">
        <div class="step-header">
            <div class="row valign-wrapper">
                <div class="step-number">2</div>
                <div>
                    <h5 class="white-text no-margin">Búsqueda y Autorización de Medicamentos</h5>
                    <p class="white-text opacity-8 no-margin">Buscar monodrogas y procesar autorizaciones</p>
                </div>
            </div>
        </div>
        <div class="step-content">
            <!-- Sección de medicamentos -->
            <div id="medicamentosSection" class="card" style="display: none; margin-top: 20px;">
                <div class="card-content">
                    <div class="card-title">
                        <i class="material-icons left">medication</i>
                        Búsqueda y Autorización de Medicamentos
                    </div>
                    
                    <div class="row">
                        <div class="input-field col s12 m8">
                            <input id="medicamento_search" type="text" class="validate autocomplete" placeholder="Escriba para buscar medicamento...">
                            <label for="medicamento_search">Buscar Medicamento</label>
                            <div id="medicamento_results" class="collection" style="display: none; position: absolute; z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ddd;"></div>
                        </div>
                        <div class="input-field col s12 m2">
                            <input id="cantidad_medicamento" type="number" class="validate" value="1" min="1">
                            <label for="cantidad_medicamento">Cantidad</label>
                        </div>
                        <div class="col s12 m2">
                            <button type="button" id="agregarMedicamento" class="btn btn-primary waves-effect waves-light full-width" style="margin-top: 25px;">
                                <i class="material-icons left">add</i>
                                Agregar
                            </button>
                        </div>
                    </div>

                    <!-- Botón para entrada manual -->
                    <div class="center" style="margin: 20px 0;">
                        <button type="button" id="toggleManualEntry" class="btn btn-outline waves-effect waves-light">
                            <i class="material-icons left">edit</i>
                            Colocar manualmente código artículo
                        </button>
                    </div>

                    <!-- Entrada Manual de Código (oculta por defecto) -->
                    <div id="manualEntrySection" style="display: none;">
                        <div class="divider" style="margin: 20px 0;"></div>
                        <h6><i class="material-icons left">edit</i>Entrada Manual de Código</h6>
                        <div class="row">
                            <div class="input-field col s12 m2">
                                <select id="tipo_manual">
                                    <option value="P" selected>P - Prestación</option>
                                    <option value="M">M - Medicamento</option>
                                </select>
                                <label>TIPO</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="codigo_manual" type="text" class="validate" placeholder="1420107">
                                <label for="codigo_manual">CODIGO ALFABETA</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="descripcion_manual" type="text" class="validate">
                                <label for="descripcion_manual">Descripción (opcional)</label>
                            </div>
                            <div class="input-field col s12 m2">
                                <input id="cantidad_manual" type="number" class="validate" value="1" min="1">
                                <label for="cantidad_manual">Cantidad</label>
                            </div>
                            <div class="col s12 m2">
                                <button type="button" id="agregarManual" class="btn btn-orange waves-effect waves-light full-width" style="margin-top: 25px;">
                                    <i class="material-icons left">add_circle</i>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Prescripción -->
                    <div class="divider" style="margin: 20px 0;"></div>
                    <h6><i class="material-icons left">assignment</i>Prescripción</h6>
                    <div class="row">
                        <div class="input-field col s12 m3">
                            <select id="tipo_matricula">
                                <option value="" disabled selected>Seleccione tipo</option>
                                <option value="Ciudad de Buenos Aires">Ciudad de Buenos Aires</option>
                                <option value="Buenos Aires">Buenos Aires</option>
                                <option value="Catamarca">Catamarca</option>
                                <option value="Chaco">Chaco</option>
                                <option value="Chubut">Chubut</option>
                                <option value="Córdoba">Córdoba</option>
                                <option value="Corrientes">Corrientes</option>
                                <option value="Entre Ríos">Entre Ríos</option>
                                <option value="Formosa">Formosa</option>
                                <option value="Jujuy">Jujuy</option>
                                <option value="La Pampa">La Pampa</option>
                                <option value="La Rioja">La Rioja</option>
                                <option value="Mendoza">Mendoza</option>
                                <option value="Misiones">Misiones</option>
                                <option value="Neuquén">Neuquén</option>
                                <option value="Río Negro">Río Negro</option>
                                <option value="Salta">Salta</option>
                                <option value="San Juan">San Juan</option>
                                <option value="San Luis">San Luis</option>
                                <option value="Santa Cruz">Santa Cruz</option>
                                <option value="Santa Fe">Santa Fe</option>
                                <option value="Santiago del Estero">Santiago del Estero</option>
                                <option value="Tierra del Fuego">Tierra del Fuego</option>
                                <option value="Tucumán">Tucumán</option>
                            </select>
                            <label>Tipo Matrícula</label>
                        </div>
                        <div class="input-field col s12 m3">
                            <input id="matricula" type="text" class="validate">
                            <label for="matricula">Matrícula</label>
                        </div>
                        <div class="input-field col s12 m3">
                            <input id="fecha_receta" type="text" class="datepicker validate">
                            <label for="fecha_receta">Fecha Receta</label>
                        </div>
                        <div class="input-field col s12 m3">
                            <input id="diagnostico" type="text" class="validate">
                            <label for="diagnostico">Diagnóstico</label>
                        </div>
                    </div>

                    <div id="medicamentosContainer" style="display: none;">
                        <h6><i class="material-icons left">list</i>Medicamentos Seleccionados</h6>
                        <div id="medicamentosList"></div>
                        
                        <div class="center" style="margin-top: 20px;">
                            <button type="button" id="procesarTransacciones" class="btn btn-success waves-effect waves-light">
                                <i class="material-icons left">send</i>
                                Procesar Transacciones AP
                            </button>
                        </div>
                    </div>

                    <!-- Resultados de transacciones -->
                    <div id="resultadosTransacciones" style="display: none; margin-top: 20px;">
                        <h6><i class="material-icons left">assignment_turned_in</i>Resultados de Transacciones</h6>
                        <div id="resultadosList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para errores -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <h4 class="red-text">
            <i class="material-icons left">error</i>
            Error en Transacción
        </h4>
        <div id="errorContent"></div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
    </div>
</div>

<!-- Modal para XMLs -->
<div id="xmlModal" class="modal modal-fixed-footer" style="width: 90%; max-width: 1200px;">
    <div class="modal-content">
        <h4><i class="material-icons left">code</i>XML de Validación de Afiliado</h4>
        <div class="row">
            <div class="col s6">
                <h6 class="blue-text">XML Solicitud</h6>
                <pre id="xmlRequest" style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="col s6">
                <h6 class="green-text">XML Respuesta</h6>
                <pre id="xmlResponse" style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <p class="white-text center" style="margin-top: 20px;">Procesando...</p>
    </div>
</div>

<!-- Floating Action Button -->
<div class="floating-action">
    <a class="btn-floating btn-large waves-effect waves-light teal pulse" onclick="scrollToTop()">
        <i class="material-icons">keyboard_arrow_up</i>
    </a>
</div>

<script>
$(document).ready(function() {
    // Initialize Material components
    M.AutoInit();
    
    // Inicializar datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        yearRange: [2020, 2030],
        autoClose: true,
        defaultDate: new Date(),
        setDefaultDate: true,
        i18n: {
            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
            weekdaysAbbrev: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
            cancel: 'Cancelar',
            done: 'Aceptar'
        }
    });
    
    // Variables globales
    let transaccionData = {};
    let medicamentosAgregados = [];
    let currentStep = 1;
    let afiliadoElegibilidad = {};
    let xmlData = {}; // Para almacenar XMLs

    // Validación de Afiliado - Guardar datos del afiliado
    $('#elegibilidadForm').on('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        // Guardar datos para usar en transacciones
        afiliadoElegibilidad = {
            codigo: $('#afiliado').val(),
            token: $('#token').val(),
            plan: $('#plan').val(),
            vercred: $('#vercred').val()
        };
        
        $.ajax({
            url: '/transaccion-ap/elegibilidad',
            method: 'POST',
            data: {
                afiliado: $('#afiliado').val(),
                tipo_documento: $('#tipo_documento').val(),
                token: $('#token').val() || '9999',
                plan: $('#plan').val(),
                vercred: $('#vercred').val()
            },
            success: function(response) {
                hideLoading();
                
                // Guardar XMLs para mostrar
                xmlData = {
                    request: response.xml_request,
                    response: response.xml_response
                };
                
                if (response.success && response.status === 'OK') {
                    let afiData = response.data.AFI || {};
                    let elegibilidadHtml = `
                        <div class="green lighten-4 green-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                            <h6><i class="material-icons left">check_circle</i>Afiliado Elegible</h6>
                            <div class="row">
                                <div class="col s6">
                                    <strong>Código:</strong> ${afiData.CODIGO || response.data.AFICODIGO || $('#afiliado').val()}<br>
                                    <strong>Nombre:</strong> ${afiData.APELLIDO || ''}, ${afiData.NOMBRE || ''}<br>
                                    <strong>Plan:</strong> ${afiData.PLAN_NOMBRE || ''} (${afiData.PLAN || ''})
                                </div>
                                <div class="col s6">
                                    <strong>ID Transacción:</strong> ${response.idtran || 'N/A'}<br>
                                    <strong>Tipo Afiliado:</strong> ${afiData.TIPOAFI || 'N/A'}<br>
                                    <strong>Estado:</strong> <span class="green-text">ELEGIBLE</span>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#elegibilidadData').html(elegibilidadHtml);
                    $('#elegibilidadResult').slideDown();
                    $('#medicamentosSection').slideDown();
                    $('#verXmlBtn').show();
                    updateProgress(2);
                    medicamentoSearch = initMedicamentosSearch();
                    showToast('Validación verificada - Puede buscar medicamentos');
                } else {
                    let errorHtml = `
                        <div class="red lighten-4 red-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                            <h6><i class="material-icons left">error</i>Error de Validación</h6>
                            <p><strong>Mensaje:</strong> ${response.message}</p>
                            ${response.data && response.data.RSPMSGG ? `<p><strong>Detalle:</strong> ${response.data.RSPMSGG}</p>` : ''}
                            ${response.idtran ? `<p><strong>ID Transacción:</strong> ${response.idtran}</p>` : ''}
                        </div>
                    `;
                    
                    $('#elegibilidadData').html(errorHtml);
                    $('#elegibilidadResult').slideDown();
                    $('#verXmlBtn').show();
                    $('#step1-indicator').removeClass('status-pending').addClass('status-error');
                    showToast('Error en verificación de validación', 'error');
                }
            },
            error: function() {
                hideLoading();
                showToast('Error en consulta de validación', 'error');
            }
        });
    });

    // Mostrar XMLs
    $('#verXmlBtn').on('click', function() {
        if (xmlData.request) {
            $('#xmlRequest').text(formatXml(xmlData.request));
        }
        if (xmlData.response) {
            $('#xmlResponse').text(formatXml(xmlData.response));
        }
        M.Modal.getInstance($('#xmlModal')).open();
    });

    // Formatear XML para mejor visualización
    function formatXml(xml) {
        if (!xml) return 'No disponible';
        
        // Simple formatting - add line breaks after tags
        return xml.replace(/></g, '>\n<')
                 .replace(/^\s*\n/gm, '')
                 .trim();
    }

    // Inicializar búsqueda de medicamentos
    function initMedicamentosSearch() {
        let searchTimeout;
        let selectedMedicamento = null;
        
        $('#medicamento_search').on('input', function() {
            const query = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 3) {
                $('#medicamento_results').hide();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                $.ajax({
                    url: '/transaccion-ap/buscar-articulos',
                    method: 'GET',
                    data: { search: query },
                    success: function(data) {
                        const results = $('#medicamento_results');
                        results.empty();
                        
                        if (data.length === 0) {
                            results.append('<div class="collection-item grey-text">No se encontraron medicamentos</div>');
                        } else {
                            data.forEach(item => {
                                results.append(`
                                    <a href="#!" class="collection-item medicamento-item" data-codigo="${item.codigo}" data-descripcion="${item.descripcion}" data-precio="${item.precio}">
                                        <div class="row valign-wrapper no-margin">
                                            <div class="col s10">
                                                <span class="title">${item.descripcion}</span><br>
                                                <small class="grey-text">Código: ${item.codigo} | Precio: $${item.precio}</small>
                                            </div>
                                            <div class="col s2 right-align">
                                                <i class="material-icons grey-text">add_circle_outline</i>
                                            </div>
                                        </div>
                                    </a>
                                `);
                            });
                        }
                        
                        results.show();
                    }
                });
            }, 300);
        });
        
        // Seleccionar medicamento
        $(document).on('click', '.medicamento-item', function() {
            selectedMedicamento = {
                codigo: $(this).data('codigo'),
                descripcion: $(this).data('descripcion'),
                precio: $(this).data('precio')
            };
            
            $('#medicamento_search').val(selectedMedicamento.descripcion);
            $('#medicamento_results').hide();
        });
        
        // Ocultar resultados al hacer click fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#medicamento_search, #medicamento_results').length) {
                $('#medicamento_results').hide();
            }
        });
        
        return { getSelected: () => selectedMedicamento };
    }

    // Toggle entrada manual
    $('#toggleManualEntry').on('click', function() {
        const section = $('#manualEntrySection');
        const button = $(this);
        
        if (section.is(':visible')) {
            section.slideUp();
            button.html('<i class="material-icons left">edit</i>Colocar manualmente código artículo');
        } else {
            section.slideDown();
            button.html('<i class="material-icons left">close</i>Ocultar entrada manual');
            // Reinicializar el select cuando se muestra la sección
            setTimeout(function() {
                M.FormSelect.init(document.getElementById('tipo_manual'));
            }, 100);
        }
    });

    // Procesar múltiples transacciones AP
    $('#procesarTransacciones').on('click', function() {
        if (medicamentosAgregados.length === 0) {
            showToast('No hay medicamentos para procesar', 'error');
            return;
        }

        showLoading();

        $.ajax({
            url: '/transaccion-ap/procesar-multiples',
            method: 'POST',
            data: {
                medicamentos: medicamentosAgregados,
                afiliado_data: afiliadoElegibilidad,
                prescripcion: {
                    tipo_matricula: $('#tipo_matricula').val(),
                    matricula: $('#matricula').val(),
                    fecha_receta: $('#fecha_receta').val(),
                    diagnostico: $('#diagnostico').val()
                }
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    mostrarResultadosTransacciones(response.resultados);
                    showToast('Transacciones procesadas');
                } else {
                    showToast('Error al procesar transacciones', 'error');
                }
            },
            error: function() {
                hideLoading();
                showToast('Error al procesar transacciones', 'error');
            }
        });
    });

    // Mostrar resultados de transacciones
    function mostrarResultadosTransacciones(resultados) {
        const container = $('#resultadosList');
        container.empty();

        resultados.forEach(function(resultado, index) {
            const isSuccess = resultado.status === 'OK';
            const cardColor = isSuccess ? 'green lighten-4' : 'red lighten-4';
            const textColor = isSuccess ? 'green-text text-darken-2' : 'red-text text-darken-2';
            const icon = isSuccess ? 'check_circle' : 'error';

            let ticketButton = '';
            if (!isSuccess) {
                ticketButton = `
                    <div class="col s12 m6" style="margin-top: 10px;">
                        <button class="btn btn-primary waves-effect waves-light full-width" onclick="imprimirTicketRechazo('${resultado.medicamento.codigo}', '${resultado.medicamento.descripcion}', '${resultado.idtran}', '${resultado.mensaje}', '${resultado.pr_mensaje || ''}', '${resultado.pr_adicional || ''}')">
                            <i class="material-icons left">print</i>
                            Imprimir Ticket de Rechazo
                        </button>
                    </div>
                `;
            }

            let xmlButton = '';
            if (resultado.idtran) {
                xmlButton = `
                    <div class="col s12 m6" style="margin-top: 10px;">
                        <button class="btn purple waves-effect waves-light full-width" onclick="verXMLTransaccion('${resultado.idtran}')">
                            <i class="material-icons left">code</i>
                            Ver XML
                        </button>
                    </div>
                `;
            }

            // Botón Ver Consumo si se creó correctamente
            let verConsumoButton = '';
            if (isSuccess && resultado.consumo_id) {
                verConsumoButton = `
                    <div class="col s12 m6" style="margin-top: 10px;">
                        <a href="/admin/consumos-up/${resultado.consumo_id}" class="btn teal waves-effect waves-light full-width" target="_blank">
                            <i class="material-icons left">visibility</i>
                            Ver Consumo
                        </a>
                    </div>
                `;
            } else if (isSuccess && resultado.consumo_error) {
                // Mostrar error de creación de consumo
                verConsumoButton = `
                    <div class="col s12" style="margin-top: 10px;">
                        <div class="orange lighten-4 orange-text text-darken-2" style="padding: 10px; border-radius: 4px;">
                            <i class="material-icons tiny left">warning</i>
                            Error al crear consumo: ${resultado.consumo_error}
                        </div>
                    </div>
                `;
            }

            // Mostrar tipo si está disponible
            const tipoInfo = resultado.medicamento.tipo ? `<strong>Tipo:</strong> ${resultado.medicamento.tipo} (${resultado.medicamento.tipo === 'M' ? 'Medicamento' : 'Prestación'})<br>` : '';

            container.append(`
                <div class="card">
                    <div class="card-content ${cardColor} ${textColor}">
                        <div class="card-title">
                            <i class="material-icons left">${icon}</i>
                            ${resultado.medicamento.descripcion}
                        </div>
                        <div class="row">
                            <div class="col s6">
                                ${tipoInfo}
                                <strong>Código:</strong> ${resultado.medicamento.codigo}<br>
                                <strong>Cantidad:</strong> ${resultado.medicamento.cantidad}<br>
                                <strong>Status:</strong> ${resultado.status}
                            </div>
                            <div class="col s6">
                                <strong>ID Transacción:</strong> ${resultado.idtran || 'N/A'}<br>
                                ${resultado.idaut ? `<strong>ID Autorización:</strong> ${resultado.idaut}<br>` : ''}
                                <strong>Mensaje:</strong> ${resultado.mensaje}
                            </div>
                            ${ticketButton}
                            ${verConsumoButton}
                            ${xmlButton}
                        </div>
                        ${resultado.pr_mensaje ? `<p><strong>Detalle:</strong> ${resultado.pr_mensaje}</p>` : ''}
                        ${resultado.pr_adicional ? `<p><strong>Info adicional:</strong> ${resultado.pr_adicional}</p>` : ''}
                    </div>
                </div>
            `);
        });

        $('#resultadosTransacciones').slideDown();
    }

    // Función para imprimir ticket de rechazo
    window.imprimirTicketRechazo = function(codigo, descripcion, idtran, motivo, detalle, adicional) {
        const params = new URLSearchParams({
            afiliado: afiliadoElegibilidad.codigo,
            nombre: 'TITULAR', // Se puede mejorar obteniendo del resultado de validación
            apellido: 'PRUEBAS',
            plan: 'ACCORD DORADO',
            medicamento: descripcion,
            codigo_prestacion: codigo,
            cantidad: '1',
            idtran: idtran,
            motivo_rechazo: motivo,
            detalle_rechazo: detalle,
            codigo_error: adicional
        });

        window.open(`/transaccion-ap/ticket-rechazo?${params.toString()}`, '_blank', 'width=600,height=800');
    };

    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Progress management
    function updateProgress(step) {
        currentStep = step;
        const progress = (step - 1) * 50;
        $('#progress-bar').css('width', progress + '%');
        
        // Update step indicators
        $('.status-chip').removeClass('status-success status-error').addClass('status-pending');
        for(let i = 1; i < step; i++) {
            $(`#step${i}-indicator`).removeClass('status-pending status-error').addClass('status-success');
        }
    }

    function showLoading() {
        $('#loadingOverlay').fadeIn();
    }

    function hideLoading() {
        $('#loadingOverlay').fadeOut();
    }

    function showToast(message, type = 'success') {
        const color = type === 'success' ? 'green' : 'red';
        M.toast({html: `<i class="material-icons left">${type === 'success' ? 'check' : 'error'}</i>${message}`, classes: color});
    }

    // Validación de Afiliado
    $('#elegibilidadForm').on('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        $.ajax({
            url: '/transaccion-ap/elegibilidad',
            method: 'POST',
            data: {
                afiliado: $('#afiliado').val(),
                tipo_documento: $('#tipo_documento').val(),
                token: $('#token').val() || '9999',
                plan: $('#plan').val(),
                vercred: $('#vercred').val()
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.status === 'OK') {
                    // Mostrar información detallada del afiliado
                    let afiData = response.data.AFI || {};
                    let elegibilidadHtml = `
                        <div class="green lighten-4 green-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                            <h6><i class="material-icons left">check_circle</i>Afiliado Elegible</h6>
                            <div class="row">
                                <div class="col s6">
                                    <strong>Código:</strong> ${afiData.CODIGO || response.data.AFICODIGO || $('#afiliado').val()}<br>
                                    <strong>Nombre:</strong> ${afiData.APELLIDO || ''}, ${afiData.NOMBRE || ''}<br>
                                    <strong>Plan:</strong> ${afiData.PLAN_NOMBRE || ''} (${afiData.PLAN || ''})
                                </div>
                                <div class="col s6">
                                    <strong>ID Transacción:</strong> ${response.idtran || 'N/A'}<br>
                                    <strong>Tipo Afiliado:</strong> ${afiData.TIPOAFI || 'N/A'}<br>
                                    <strong>Estado:</strong> <span class="green-text">ELEGIBLE</span>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#elegibilidadData').html(elegibilidadHtml);
                    $('#elegibilidadResult').slideDown();
                    updateProgress(2);
                    showToast('Validación verificada correctamente');
                } else {
                    // Mostrar error detallado
                    let errorHtml = `
                        <div class="red lighten-4 red-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                            <h6><i class="material-icons left">error</i>Error de Validación</h6>
                            <p><strong>Mensaje:</strong> ${response.message}</p>
                            ${response.data && response.data.RSPMSGG ? `<p><strong>Detalle:</strong> ${response.data.RSPMSGG}</p>` : ''}
                            ${response.idtran ? `<p><strong>ID Transacción:</strong> ${response.idtran}</p>` : ''}
                        </div>
                    `;
                    
                    $('#elegibilidadData').html(errorHtml);
                    $('#elegibilidadResult').slideDown();
                    $('#step1-indicator').removeClass('status-pending').addClass('status-error');
                    showToast('Error en verificación de validación', 'error');
                }
            },
            error: function() {
                hideLoading();
                showToast('Error en consulta de validación', 'error');
            }
        });
    });

    // Transacción AP
    $('#transaccionForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#afiliado').val()) {
            showToast('Primero debe realizar la validación de afiliado', 'error');
            return;
        }

        showLoading();

        $.ajax({
            url: '/transaccion-ap/procesar',
            method: 'POST',
            data: {
                afiliado: $('#afiliado').val(),
                codigo_prestacion: $('#codigo_prestacion').val(),
                cantidad: $('#cantidad').val()
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.status === 'OK') {
                    transaccionData = response;
                    $('#transaccionResult').html(`
                        <div class="card-content">
                            <div class="green lighten-4 green-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                                <h6><i class="material-icons left">check_circle</i>Transacción Aprobada</h6>
                                <div class="row">
                                    <div class="col s6">
                                        <strong>ID Transacción:</strong> ${response.idtran}<br>
                                        <strong>ID Autorización:</strong> ${response.idaut}
                                    </div>
                                    <div class="col s6">
                                        <strong>Afiliado:</strong> ${response.afiliado_data.nombre} ${response.afiliado_data.apellido}<br>
                                        <strong>Plan:</strong> ${response.afiliado_data.plan}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).slideDown();
                    $('#medicamentosSection').slideDown();
                    updateProgress(3);
                    initMedicamentosSelect();
                    showToast('Transacción procesada exitosamente');
                } else {
                    showErrorModal(response);
                    $('#step2-indicator').removeClass('status-pending').addClass('status-error');
                }
            },
            error: function() {
                hideLoading();
                showToast('Error en transacción AP', 'error');
            }
        });
    });

    // Inicializar select de medicamentos
    function initMedicamentosSelect() {
        $('#medicamento_select').select2({
            placeholder: 'Buscar medicamento...',
            ajax: {
                url: '/transaccion-ap/buscar-articulos',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        codigo_prestacion: $('#codigo_prestacion').val()
                    };
                },
                processResults: function(data) {
                    return { results: data };
                },
                cache: true
            },
            minimumInputLength: 2,
            theme: 'default'
        });
    }

    // Agregar medicamento
    let medicamentoSearch;
    $('#agregarMedicamento').on('click', function() {
        const medicamentoData = medicamentoSearch ? medicamentoSearch.getSelected() : null;
        const cantidad = $('#cantidad_medicamento').val();

        if (!medicamentoData || !cantidad) {
            showToast('Seleccione un medicamento y cantidad', 'error');
            return;
        }

        const medicamento = {
            codigo: medicamentoData.codigo,
            descripcion: medicamentoData.descripcion,
            cantidad: cantidad,
            precio: medicamentoData.precio
        };

        medicamentosAgregados.push(medicamento);
        actualizarListaMedicamentos();
        $('#medicamento_search').val('');
        $('#cantidad_medicamento').val(1);
        showToast('Medicamento agregado');
    });

    // Agregar medicamento manual
    $('#agregarManual').on('click', function() {
        const tipo = $('#tipo_manual').val();
        const codigo = $('#codigo_manual').val().trim();
        const descripcion = $('#descripcion_manual').val().trim() || 'Medicamento manual';
        const cantidad = $('#cantidad_manual').val();

        if (!codigo || !cantidad) {
            showToast('Ingrese código y cantidad', 'error');
            return;
        }

        // Verificar si ya existe
        if (medicamentosAgregados.some(med => med.codigo === codigo)) {
            showToast('Este código ya fue agregado', 'error');
            return;
        }

        const medicamento = {
            tipo: tipo,
            codigo: codigo,
            descripcion: descripcion,
            cantidad: parseInt(cantidad),
            precio: 0.00,
            manual: true
        };

        medicamentosAgregados.push(medicamento);
        actualizarListaMedicamentos();

        // Limpiar campos
        $('#codigo_manual').val('');
        $('#descripcion_manual').val('');
        $('#cantidad_manual').val(1);
        showToast(`${tipo === 'P' ? 'Prestación' : 'Medicamento'} agregado manualmente`);
    });

    // Actualizar lista de medicamentos
    function actualizarListaMedicamentos() {
        const container = $('#medicamentosList');
        container.empty();

        medicamentosAgregados.forEach(function(med, index) {
            const tipoLabel = med.tipo ? `Tipo: ${med.tipo} | ` : '';
            const manualLabel = med.manual ? ' (Manual)' : '';
            
            container.append(`
                <div class="medication-item">
                    <div class="row valign-wrapper">
                        <div class="col s8">
                            <h6 class="no-margin">${med.descripcion}${manualLabel}</h6>
                            <p class="grey-text no-margin">${tipoLabel}Código: ${med.codigo} | Cantidad: ${med.cantidad} | Precio: $${med.precio}</p>
                        </div>
                        <div class="col s4 right-align">
                            <button class="btn btn-danger waves-effect waves-light" onclick="eliminarMedicamento(${index})">
                                <i class="material-icons">delete</i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        });

        $('#medicamentosContainer').toggle(medicamentosAgregados.length > 0);
    }

    // Eliminar medicamento
    window.eliminarMedicamento = function(index) {
        medicamentosAgregados.splice(index, 1);
        actualizarListaMedicamentos();
        showToast('Medicamento eliminado');
    };

    // Guardar consumos
    $('#guardarConsumos').on('click', function() {
        if (medicamentosAgregados.length === 0) return;

        showLoading();

        const promises = medicamentosAgregados.map(function(med) {
            return $.ajax({
                url: '/transaccion-ap/guardar-consumo',
                method: 'POST',
                data: {
                    afiliado: $('#afiliado').val(),
                    nombres: transaccionData.afiliado_data.nombre,
                    apellidos: transaccionData.afiliado_data.apellido,
                    codigo_prestacion: med.codigo,
                    descripcion: med.descripcion,
                    cantidad: med.cantidad,
                    precio: med.precio,
                    idtran: transaccionData.idtran,
                    idaut: transaccionData.idaut
                }
            });
        });

        Promise.all(promises).then(function() {
            hideLoading();
            showToast('Consumos guardados exitosamente');
            setTimeout(() => location.reload(), 2000);
        }).catch(function() {
            hideLoading();
            showToast('Error al guardar consumos', 'error');
        });
    });

    // Mostrar modal de error
    function showErrorModal(response) {
        let errorHtml = `
            <div class="red lighten-4 red-text text-darken-2" style="padding: 20px; border-radius: 8px;">
                <h6><i class="material-icons left">error</i>Detalles del Error</h6>
                <p><strong>Código:</strong> ${response.codigo}</p>
                <p><strong>Mensaje:</strong> ${response.mensaje}</p>
        `;
        
        if (response.pr_mensaje) {
            errorHtml += `<p><strong>Detalle:</strong> ${response.pr_mensaje}</p>`;
        }
        if (response.pr_adicional) {
            errorHtml += `<p><strong>Información adicional:</strong> ${response.pr_adicional}</p>`;
        }

        errorHtml += '</div>';

        $('#errorContent').html(errorHtml);
        M.Modal.getInstance($('#errorModal')).open();
    }

    // Ver XML de transacción
    window.verXMLTransaccion = function(idtran) {
        $.ajax({
            url: `/admin/transaccion-ap/${idtran}/xml`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#xmlRequestContent').text(response.xml_request || 'No disponible');
                    $('#xmlResponseContent').text(response.xml_response || 'No disponible');
                    M.Modal.getInstance($('#modalXML')).open();
                    $('ul.tabs').tabs();
                } else {
                    M.toast({html: 'Error al cargar XML: ' + response.message, classes: 'red'});
                }
            },
            error: function() {
                M.toast({html: 'Error en la solicitud', classes: 'red'});
            }
        });
    };

    // Scroll to top
    window.scrollToTop = function() {
        $('html, body').animate({scrollTop: 0}, 500);
    };
});
</script>

<!-- Modal XML -->
<div id="modalXML" class="modal modal-fixed-footer" style="width: 80%; height: 80%;">
    <div class="modal-content">
        <h4>XML Request y Response</h4>
        <div class="row">
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s6"><a href="#xml-request" class="active">Request XML</a></li>
                    <li class="tab col s6"><a href="#xml-response">Response XML</a></li>
                </ul>
            </div>
            <div id="xml-request" class="col s12">
                <pre id="xmlRequestContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 400px;"></pre>
            </div>
            <div id="xml-response" class="col s12">
                <pre id="xmlResponseContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 400px;"></pre>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
    </div>
</div>

</body>
</html>
