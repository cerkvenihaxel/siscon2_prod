
<?php

$id = $_GET['id'];
$nrosolicitud = DB::table('convenio_oficina_os')->where('id', $id)->value('nrosolicitud');
$pedidoID = DB::table('pedido_medicamento')->where('nrosolicitud', $nrosolicitud)->value('id');
$data = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $pedidoID)->get();

?>




<div class="panel panel-default">

        <table class="table">
            <thead>
                <tr>
                    <th>Código de artículos </th>
                    <th>Nombre del artículo</th>
                    <th>Nombre comercial || presentación </th>
                    <th>Cantidad</th>
                    <th>Consultar precio en Kairos web</th>

                </tr>
            </thead>
            <tbody>
              @foreach ($data as $d)
                <tr key={{$d->pedido_medicamento_id}}>
                    <td>{{ $d->articuloZafiro_id }}</td>
                    <td>{{ $monodroga = DB::table('articulosZafiro')->where('id', $d->articuloZafiro_id)->value('des_monodroga') }}</td>
                    <td>{{ DB::table('articulosZafiro')->where('id', $d->articuloZafiro_id)->value('des_articulo') }} || {{ DB::table('articulosZafiro')->where('id', $d->articuloZafiro_id)->value('presentacion') }} </td>
                    <td>{{ $d->cantidad }}</td>
                    <td>
                        <a href="https://ar.kairosweb.com/?s={{$monodroga}}" class="boton-kairoz" target="_blank">
                            Consultar
                        </a>
                    </td>
                </tr>

              @endforeach


            </tbody>
        </table>
    </div>

<style>
    .boton-kairoz {
        display: inline-block;
        font-weight: 400;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-color: #28a745;
        border: 1px solid #28a745;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .boton-kairoz:hover {
        color: #fff;
        background-color: #218838;
        border-color: #1e7e34;
    }

</style>
</div>


