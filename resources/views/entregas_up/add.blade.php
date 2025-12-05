<!DOCTYPE html>
<html>
<head>
    <title>Registrar Entrega - Consumo #{{ $consumo->id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('SISCON.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }
        .signature-pad {
            border: 2px solid #ddd;
            border-radius: 4px;
            background: white;
            cursor: crosshair;
        }
        .file-upload-wrapper {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .file-upload-wrapper:hover {
            border-color: #667eea;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
@include('components.up_topbar')
@include('components.up_sidebar')

<div class="container main-container">
    <div class="row">
        <div class="col s12">
            <div class="card-panel teal lighten-5">
                <h4 class="teal-text text-darken-2">
                    <i class="material-icons left">local_shipping</i>
                    Registrar Entrega de Prestación
                </h4>
                <p class="grey-text">Consumo #{{ $consumo->id }} - {{ $consumo->desc }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="card-panel red lighten-4 red-text text-darken-4">
            <i class="material-icons left">error</i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Información del Consumo -->
    <div class="card info-card">
        <div class="card-header">
            <i class="material-icons left">info</i>
            <span style="font-size: 20px; font-weight: 500;">Información del Consumo</span>
        </div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m6">
                    <p><strong>Afiliado:</strong> {{ $consumo->afiliado }}</p>
                    <p><strong>Nombre:</strong> {{ trim($consumo->nombres . ' ' . $consumo->apellidos) }}</p>
                    <p><strong>Plan:</strong> {{ $consumo->nombre_modelo_plan }}</p>
                </div>
                <div class="col s12 m6">
                    <p><strong>Prestación:</strong> {{ $consumo->desc }}</p>
                    <p><strong>Código:</strong> {{ $consumo->cod_prestacion }}</p>
                    <p><strong>Cantidad Solicitada:</strong> {{ $consumo->cant }}</p>
                    <p><strong>ID Transacción (IDTRAN):</strong> {{ $consumo->num_tran ?? $consumo->idtran_aprobacion ?? 'N/A' }}</p>
                    <p><strong>ID Autorización (IDAUT):</strong> {{ $consumo->idaut ?? 'N/A' }}</p>
                    <p><strong>Cargo:</strong> ${{ number_format($consumo->cargo ?? 0, 2) }}</p>
                    <p><strong>Importe OS:</strong> ${{ number_format($consumo->impos ?? 0, 2) }}</p>
                    <p><strong>Total:</strong> <span style="color: #d32f2f; font-weight: bold;">${{ number_format(($consumo->imptot ?? 0) + ($consumo->cargo ?? 0), 2) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Entrega -->
    <form action="/admin/entregas-up/store" method="POST" enctype="multipart/form-data" id="formEntrega">
        @csrf
        <input type="hidden" name="consumo_id" value="{{ $consumo->id }}">

        <div class="card info-card">
            <div class="card-header">
                <i class="material-icons left">assignment</i>
                <span style="font-size: 20px; font-weight: 500;">Datos de la Entrega</span>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="fecha_entrega" name="fecha_entrega" type="date" required value="{{ date('Y-m-d') }}">
                        <label for="fecha_entrega" class="active">Fecha de Entrega *</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="cantidad_entregada" name="cantidad_entregada" type="number" min="1" max="{{ $consumo->cant }}" required value="{{ $consumo->cant }}">
                        <label for="cantidad_entregada" class="active">Cantidad Entregada *</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12 m6">
                        <select name="estado_entrega" required>
                            <option value="completa" selected>Entrega Completa</option>
                            <option value="parcial">Entrega Parcial</option>
                            <option value="rechazada">Rechazada</option>
                        </select>
                        <label>Estado de Entrega *</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="quien_recibe" name="quien_recibe" type="text" required>
                        <label for="quien_recibe">Quien Recibe *</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="dni_afiliado" name="dni_afiliado" type="text">
                        <label for="dni_afiliado">DNI del que Recibe</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <select name="relacion_afiliado">
                            <option value="">Seleccionar relación</option>
                            <option value="afiliado">Afiliado</option>
                            <option value="familiar">Familiar</option>
                            <option value="tercero">Tercero Autorizado</option>
                        </select>
                        <label>Relación con Afiliado</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="remito" name="remito" type="text" value="{{ $consumo->remito ?? '' }}">
                        <label for="remito" class="{{ $consumo->remito ? 'active' : '' }}">Número de Remito</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="transporte" name="transporte" type="text" value="{{ $consumo->nro_transporte ?? '' }}">
                        <label for="transporte" class="{{ $consumo->nro_transporte ? 'active' : '' }}">Transporte</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="observaciones" name="observaciones" class="materialize-textarea"></textarea>
                        <label for="observaciones">Observaciones</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firma Digital -->
        <div class="card info-card">
            <div class="card-header">
                <i class="material-icons left">gesture</i>
                <span style="font-size: 20px; font-weight: 500;">Firma del Receptor</span>
            </div>
            <div class="card-content">
                <p class="grey-text">Por favor, solicite al receptor que firme en el recuadro a continuación:</p>
                <div class="row">
                    <div class="col s12 center">
                        <canvas id="signature-pad" class="signature-pad" width="600" height="200"></canvas>
                        <input type="hidden" name="firma_base64" id="firma_base64">
                    </div>
                    <div class="col s12 center" style="margin-top: 10px;">
                        <button type="button" class="btn red" onclick="clearSignature()">
                            <i class="material-icons left">clear</i>
                            Limpiar Firma
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archivos Adjuntos -->
        <div class="card info-card">
            <div class="card-header">
                <i class="material-icons left">attach_file</i>
                <span style="font-size: 20px; font-weight: 500;">Archivos Adjuntos</span>
            </div>
            <div class="card-content">
                <div class="file-upload-wrapper">
                    <i class="material-icons large grey-text">cloud_upload</i>
                    <p>Arrastre archivos aquí o haga clic para seleccionar</p>
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Seleccionar Archivos</span>
                            <input type="file" name="archivos[]" multiple accept="image/*,.pdf">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Seleccione uno o más archivos">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card info-card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12 m4">
                        <a href="/admin/consumos-up" class="btn grey waves-effect waves-light full-width">
                            <i class="material-icons left">arrow_back</i>
                            Cancelar
                        </a>
                    </div>
                    <div class="col s12 m4">
                        <a href="/admin/entregas-up/imprimir-formulario/{{ $consumo->id }}"
                           target="_blank"
                           class="btn blue waves-effect waves-light full-width">
                            <i class="material-icons left">print</i>
                            Imprimir Formulario
                        </a>
                    </div>
                    <div class="col s12 m4">
                        <button type="submit" class="btn green waves-effect waves-light full-width">
                            <i class="material-icons left">save</i>
                            Registrar Entrega
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    M.AutoInit();
});

// Signature Pad
const canvas = document.getElementById('signature-pad');
const signaturePad = new SignaturePad(canvas);

function clearSignature() {
    signaturePad.clear();
}

// Guardar firma antes de enviar formulario
$('#formEntrega').on('submit', function(e) {
    if (!signaturePad.isEmpty()) {
        const dataURL = signaturePad.toDataURL();
        $('#firma_base64').val(dataURL);
    }
});
</script>

</body>
</html>
