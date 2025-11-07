<!DOCTYPE html>
<html>
<head>
    <title>XML Anulación #{{ $anulacion->id }} - {{ $anulacion->codigo_afiliado }}</title>
    <link rel="icon" type="image/png" href="{{ asset('siscon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f5f5;
            margin-left: 250px;
            margin-top: 60px;
        }
        .main-container {
            margin-top: 20px;
            padding: 20px;
        }
        .info-card {
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
        }
        .card-header i {
            margin-right: 15px;
            font-size: 32px;
        }
        .card-header h5 {
            margin: 0;
            font-weight: 500;
        }
        .info-row {
            padding: 12px 24px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 500;
            color: #666;
            min-width: 120px;
        }
        .info-value {
            font-weight: 400;
            color: #333;
            text-align: right;
            flex: 1;
        }
        .status-chip {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-ok { background: #00b894; color: white; }
        .status-no { background: #d63031; color: white; }
        .status-error { background: #d63031; color: white; }
        .status-pend { background: #fdcb6e; color: #856404; }
        .tipo-chip {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .tipo-idtran { background: #0984e3; color: white; }
        .tipo-msgid { background: #e17055; color: white; }
        .tipo-idaut { background: #6c5ce7; color: white; }
        .xml-container {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .xml-tabs {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .xml-tabs .tabs {
            background: #f8f9fa;
        }
        .xml-tabs .tabs .tab a {
            color: #666;
            font-weight: 500;
        }
        .xml-tabs .tabs .tab a.active {
            color: #2c3e50;
        }
        .xml-tabs .indicator {
            background-color: #2c3e50;
        }
        .actions-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 12px 12px;
        }
        .btn-action {
            margin: 5px;
        }
    </style>
</head>
<body>
    @include('components.up_topbar')
    @include('components.up_sidebar')

    <div class="main-container">
        <!-- Header Principal -->
        <div class="card info-card">
            <div class="card-header">
                <i class="material-icons">code</i>
                <div>
                    <h5>XML de Anulación UP</h5>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;">Afiliado: {{ $anulacion->codigo_afiliado }} | ID: {{ $anulacion->id }}</p>
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha</span>
                <span class="info-value">{{ $anulacion->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="info-value">
                    <span class="status-chip status-{{ strtolower($anulacion->status) }}">
                        {{ $anulacion->status }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo</span>
                <span class="info-value">
                    <span class="tipo-chip tipo-{{ strtolower($anulacion->tipoidanul) }}">
                        {{ $anulacion->tipoidanul }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Motivo</span>
                <span class="info-value">{{ Str::limit($anulacion->motivo, 50) }}</span>
            </div>
        </div>

        <!-- XML Tabs -->
        <div class="xml-tabs">
            <div class="row" style="margin-bottom: 0;">
                <div class="col s12">
                    <ul class="tabs">
                        <li class="tab col s6">
                            <a href="#xml-request" class="active">
                                <i class="material-icons left">send</i>XML Solicitud
                            </a>
                        </li>
                        <li class="tab col s6">
                            <a href="#xml-response">
                                <i class="material-icons left">reply</i>XML Respuesta
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div id="xml-request" class="col s12">
                    <div style="padding: 20px;">
                        <h6 style="margin-bottom: 15px; color: #666;">
                            <i class="material-icons tiny">send</i> XML de Solicitud Enviado
                        </h6>
                        <div class="xml-container">{{ $xmlData['request'] }}</div>
                    </div>
                </div>
                
                <div id="xml-response" class="col s12">
                    <div style="padding: 20px;">
                        <h6 style="margin-bottom: 15px; color: #666;">
                            <i class="material-icons tiny">reply</i> XML de Respuesta Recibido
                        </h6>
                        <div class="xml-container">{{ $xmlData['response'] }}</div>
                    </div>
                </div>
            </div>
            
            <div class="actions-section">
                <div class="row" style="margin-bottom: 0;">
                    <div class="col s12 m3">
                        <a href="{{ url('/admin/anulaciones-up/detalle/' . $anulacion->id) }}" class="btn blue waves-effect waves-light full-width btn-action">
                            <i class="material-icons left">arrow_back</i>
                            Volver al Detalle
                        </a>
                    </div>
                    <div class="col s12 m3">
                        <a href="{{ url('/admin/anulaciones-up') }}" class="btn grey waves-effect waves-light full-width btn-action">
                            <i class="material-icons left">list</i>
                            Ver Listado
                        </a>
                    </div>
                    <div class="col s12 m3">
                        <button onclick="copiarXML('request')" class="btn green waves-effect waves-light full-width btn-action">
                            <i class="material-icons left">content_copy</i>
                            Copiar Solicitud
                        </button>
                    </div>
                    <div class="col s12 m3">
                        <button onclick="copiarXML('response')" class="btn orange waves-effect waves-light full-width btn-action">
                            <i class="material-icons left">content_copy</i>
                            Copiar Respuesta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.tabs').tabs();
        });

        function copiarXML(tipo) {
            const xmlContent = tipo === 'request' ? 
                document.querySelector('#xml-request .xml-container').textContent :
                document.querySelector('#xml-response .xml-container').textContent;
            
            navigator.clipboard.writeText(xmlContent).then(function() {
                M.toast({
                    html: `XML de ${tipo === 'request' ? 'solicitud' : 'respuesta'} copiado al portapapeles`,
                    classes: 'green'
                });
            }).catch(function() {
                M.toast({html: 'Error al copiar XML', classes: 'red'});
            });
        }
    </script>
</body>
</html>
