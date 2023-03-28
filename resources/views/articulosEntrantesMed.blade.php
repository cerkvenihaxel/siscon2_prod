
<?php 

$id = $_GET['id'];
$data = DB::table('pedido_medicamento_detail')->where('pedido_medicamento_id', $id)->get();

?>

<div class="panel panel-default">
   
        <table class="table">
            <thead>
                <tr>
                    <th>Código de artículos </th>
                    <th>Nombre del artículo</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($data as $d)
                <tr key={{$d->pedido_medicamento_id}}>
                    <td>{{ $d->articuloZafiro_id }}</td>
                    <td>{{ DB::table('articulosZafiro')->where('id', $d->articuloZafiro_id)->value('des_articulo') }}</td>
                    <td>{{ $d->cantidad }}</td>
                </tr> 
                  
              @endforeach


            </tbody>
        </table>
    </div>
</div>


