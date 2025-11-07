@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-code"></i> {{ $page_title }}
        </h3>
        <div class="box-tools pull-right">
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="box-body">
        <!-- Información de la transacción -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <strong>Información de la Transacción</strong>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <th width="200">ID Transacción:</th>
                                <td>{{ $transaccion->id }}</td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><span class="badge badge-info">{{ $transaccion->transaction_type }}</span></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge badge-{{ $transaccion->status == 'OK' ? 'success' : 'danger' }}">
                                        {{ $transaccion->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Afiliado:</th>
                                <td>{{ $transaccion->afiliado_codigo }}</td>
                            </tr>
                            <tr>
                                <th>MSGID:</th>
                                <td>{{ $transaccion->msgid }}</td>
                            </tr>
                            <tr>
                                <th>IDTRAN:</th>
                                <td>{{ $transaccion->idtran }}</td>
                            </tr>
                            @if($transaccion->idaut)
                            <tr>
                                <th>IDAUT:</th>
                                <td>{{ $transaccion->idaut }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Fecha:</th>
                                <td>{{ $transaccion->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Tiempo de Ejecución:</th>
                                <td>{{ $transaccion->execution_time_ms }} ms</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- XML -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Contenido XML</strong>
                        <button type="button" class="btn btn-xs btn-primary pull-right" onclick="copiarXML()">
                            <i class="fa fa-copy"></i> Copiar
                        </button>
                    </div>
                    <div class="panel-body">
                        <pre id="xml-content" style="background-color: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 600px; overflow-y: auto;">{{ $xml_formateado }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copiarXML() {
    const xmlContent = document.getElementById('xml-content').textContent;
    const textarea = document.createElement('textarea');
    textarea.value = xmlContent;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    swal("¡Copiado!", "El XML ha sido copiado al portapapeles", "success");
}
</script>
@endsection
