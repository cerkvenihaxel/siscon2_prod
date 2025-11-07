<!DOCTYPE html>
<html>
<head>
    <title>Anulación UP - Sistema de Gestión</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Material UI CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; margin-left: 250px; margin-top: 60px; }
        .main-content { padding: 20px; }
        .form-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .result-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-top: 20px; }
        .status-chip { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .status-ok { background: #00b894; color: white; }
        .status-error { background: #d63031; color: white; }
        .btn-action { margin: 5px; }
    </style>
</head>
<body>
    @include('components.up_topbar')
    @include('components.up_sidebar')

    <div class="main-content">
        <!-- Header -->
        <div class="row">
            <div class="col s12">
                <div class="card-panel red lighten-5">
                    <h4 class="red-text text-darken-2">
                        <i class="material-icons left">cancel</i>
                        Anulación UP - Transacción ATR
                    </h4>
                    <p class="grey-text">Procesamiento de anulaciones de transacciones</p>
                </div>
            </div>
        </div>

        <!-- Información del consumo -->
        @if(request('consumo_id'))
        <div class="card form-card">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons left">info</i>
                    Información del Consumo
                </span>
                <div class="chip blue white-text">
                    Consumo ID: {{ request('consumo_id') }}
                </div>
            </div>
        </div>
        @endif

        <!-- Formulario de Anulación -->
        <div class="card form-card">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons left">assignment</i>
                    Datos de la Anulación
                </span>
                
                <form id="formAnulacion">
                    @if(request('consumo_id'))
                        <input type="hidden" name="consumo_id" value="{{ request('consumo_id') }}">
                    @endif
                    
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="text" id="afiliado" name="afiliado" value="{{ request('afiliado') }}" required>
                            <label for="afiliado">Código de Afiliado *</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <select name="tipo_anulacion" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="IDTRAN" {{ request('tipo_anulacion') == 'IDTRAN' ? 'selected' : '' }}>Por ID de Transacción</option>
                                <option value="MSGID" {{ request('tipo_anulacion') == 'MSGID' ? 'selected' : '' }}>Por ID de Mensaje</option>
                                <option value="IDAUT" {{ request('tipo_anulacion') == 'IDAUT' ? 'selected' : '' }}>Por ID de Autorización</option>
                            </select>
                            <label>Tipo de Anulación *</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="text" id="id_anulacion" name="id_anulacion" value="{{ request('id_anulacion') }}" required>
                            <label for="id_anulacion">ID a Anular *</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input type="text" id="plan" name="plan" value="{{ request('plan') }}">
                            <label for="plan">Plan</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <select id="motivo_predefinido" name="motivo_predefinido" onchange="toggleMotivoPersonalizado()">
                                <option value="">Seleccionar motivo...</option>
                                <option value="Error en la carga de datos">Error en la carga de datos</option>
                                <option value="Cancelación médica">Cancelación médica</option>
                                <option value="Medicamento no disponible">Medicamento no disponible</option>
                                <option value="Solicitud duplicada">Solicitud duplicada</option>
                                <option value="Error de sistema">Error de sistema</option>
                                <option value="Cambio de tratamiento">Cambio de tratamiento</option>
                                <option value="Anulación por auditoría">Anulación por auditoría</option>
                                <option value="otros">Otros (especificar)</option>
                            </select>
                            <label>Motivo de Anulación *</label>
                        </div>
                    </div>

                    <div class="row" id="motivo_personalizado_row" style="display: none;">
                        <div class="input-field col s12">
                            <textarea id="motivo_personalizado" name="motivo_personalizado" class="materialize-textarea"></textarea>
                            <label for="motivo_personalizado">Especificar motivo *</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="text" id="token" name="token" value="{{ request('token') }}">
                            <label for="token">Token</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input type="text" id="vercred" name="vercred" value="{{ request('vercred') }}">
                            <label for="vercred">Ver Credencial</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s12">
                            <button type="submit" class="btn red waves-effect waves-light btn-action">
                                <i class="material-icons left">cancel</i>
                                Procesar Anulación
                            </button>
                            @if(request('consumo_id'))
                                <a href="{{ url('/admin/consumos-up') }}" class="btn grey waves-effect waves-light btn-action">
                                    <i class="material-icons left">arrow_back</i>
                                    Volver a Consumos
                                </a>
                            @else
                                <a href="{{ url('/admin/anulaciones-up') }}" class="btn grey waves-effect waves-light btn-action">
                                    <i class="material-icons left">list</i>
                                    Ver Anulaciones
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultados -->
        <div id="resultados" class="card result-card" style="display: none;">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons left">assessment</i>
                    Resultado de la Anulación
                </span>
                <div id="contenidoResultados"></div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar XML -->
    <div id="xmlModal" class="modal modal-fixed-footer" style="width: 90%; height: 90%;">
        <div class="modal-content">
            <h4>XML de Anulación</h4>
            <div class="row">
                <div class="col s12">
                    <ul class="tabs">
                        <li class="tab col s6"><a href="#xml-request" class="active">XML Solicitud</a></li>
                        <li class="tab col s6"><a href="#xml-response">XML Respuesta</a></li>
                    </ul>
                </div>
                <div id="xml-request" class="col s12">
                    <pre id="xmlRequestContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 500px; font-size: 12px;"></pre>
                </div>
                <div id="xml-response" class="col s12">
                    <pre id="xmlResponseContent" style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto; max-height: 500px; font-size: 12px;"></pre>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            M.AutoInit();
            
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Precargar motivo si viene en la URL
            const motivoUrl = '{{ request("motivo") }}';
            if (motivoUrl) {
                // Si viene desde consumo, usar "Error en la carga de datos" por defecto
                if (motivoUrl.includes('Anulación desde')) {
                    $('#motivo_predefinido').val('Error en la carga de datos');
                } else {
                    // Buscar si coincide con algún motivo predefinido
                    const selectMotivo = $('#motivo_predefinido');
                    let encontrado = false;
                    
                    selectMotivo.find('option').each(function() {
                        if ($(this).val() === motivoUrl) {
                            selectMotivo.val(motivoUrl);
                            encontrado = true;
                            return false;
                        }
                    });
                    
                    // Si no coincide, seleccionar "otros" y poner el motivo personalizado
                    if (!encontrado) {
                        selectMotivo.val('otros');
                        $('#motivo_personalizado').val(motivoUrl);
                        $('#motivo_personalizado_row').show();
                    }
                }
                
                M.FormSelect.init($('#motivo_predefinido')[0]);
                M.textareaAutoResize($('#motivo_personalizado'));
            }
        });

        function toggleMotivoPersonalizado() {
            const select = $('#motivo_predefinido');
            const row = $('#motivo_personalizado_row');
            const textarea = $('#motivo_personalizado');
            
            if (select.val() === 'otros') {
                row.show();
                textarea.prop('required', true);
            } else {
                row.hide();
                textarea.prop('required', false);
                textarea.val('');
            }
        }

        $('#formAnulacion').on('submit', function(e) {
            e.preventDefault();
            
            // Determinar el motivo final
            let motivoFinal = '';
            const motivoPredefinido = $('#motivo_predefinido').val();
            
            if (motivoPredefinido === 'otros') {
                motivoFinal = $('#motivo_personalizado').val();
            } else {
                motivoFinal = motivoPredefinido;
            }
            
            if (!motivoFinal) {
                M.toast({html: 'Debe seleccionar un motivo de anulación', classes: 'red'});
                return;
            }
            
            const formData = {
                afiliado: $('#afiliado').val(),
                tipo_anulacion: $('select[name="tipo_anulacion"]').val(),
                id_anulacion: $('#id_anulacion').val(),
                motivo: motivoFinal,
                token: $('#token').val(),
                plan: $('#plan').val(),
                vercred: $('#vercred').val(),
                consumo_id: $('input[name="consumo_id"]').val()
            };

            $.ajax({
                url: '{{ route("anulacion-up.procesar") }}',
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    $('button[type="submit"]').prop('disabled', true).html('<i class="material-icons left">hourglass_empty</i>Procesando...');
                },
                success: function(response) {
                    mostrarResultado(response);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON || {message: 'Error desconocido'};
                    mostrarError(error.message);
                },
                complete: function() {
                    $('button[type="submit"]').prop('disabled', false).html('<i class="material-icons left">cancel</i>Procesar Anulación');
                }
            });
        });

        function mostrarResultado(response) {
            const statusClass = response.success ? 'green' : 'red';
            const statusIcon = response.success ? 'check_circle' : 'error';
            
            let xmlButton = '';
            if (response.xml_request || response.xml_response) {
                xmlButton = `<button class="btn blue waves-effect waves-light btn-action" onclick="verXML()">
                    <i class="material-icons left">code</i>Ver XML
                </button>`;
            }

            let volverButton = '';
            if (response.consumo_actualizado) {
                volverButton = `<a href="/admin/consumos-up" class="btn teal waves-effect waves-light btn-action">
                    <i class="material-icons left">list</i>Ver Consumos
                </a>`;
            }

            const html = `
                <div class="card-panel ${statusClass} lighten-4">
                    <h5 class="${statusClass}-text text-darken-2">
                        <i class="material-icons left">${statusIcon}</i>
                        ${response.success ? 'Anulación Exitosa' : 'Error en Anulación'}
                    </h5>
                    <p><strong>Mensaje:</strong> ${response.message}</p>
                    ${response.status ? `<p><strong>Status:</strong> <span class="status-chip status-${response.status.toLowerCase()}">${response.status}</span></p>` : ''}
                    ${response.idtran ? `<p><strong>ID Transacción:</strong> <code>${response.idtran}</code></p>` : ''}
                    ${response.consumo_actualizado ? '<p><strong>Estado del consumo actualizado correctamente</strong></p>' : ''}
                    <div style="margin-top: 15px;">
                        ${xmlButton}
                        ${volverButton}
                    </div>
                </div>
            `;

            $('#contenidoResultados').html(html);
            $('#resultados').show();

            // Guardar XML para modal
            if (response.xml_request) {
                window.currentXmlRequest = response.xml_request;
            }
            if (response.xml_response) {
                window.currentXmlResponse = response.xml_response;
            }

            // Scroll to results
            $('html, body').animate({
                scrollTop: $("#resultados").offset().top - 100
            }, 500);
        }

        function mostrarError(mensaje) {
            const html = `
                <div class="card-panel red lighten-4">
                    <h5 class="red-text text-darken-2">
                        <i class="material-icons left">error</i>Error
                    </h5>
                    <p>${mensaje}</p>
                </div>
            `;
            $('#contenidoResultados').html(html);
            $('#resultados').show();
        }

        function verXML() {
            if (window.currentXmlRequest || window.currentXmlResponse) {
                $('#xmlRequestContent').text(window.currentXmlRequest || 'No disponible');
                $('#xmlResponseContent').text(window.currentXmlResponse || 'No disponible');
                M.Modal.getInstance($('#xmlModal')).open();
            }
        }
    </script>
</body>
</html>
