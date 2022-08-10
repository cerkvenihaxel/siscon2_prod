
<?php 

$id = $_GET['id'];
$data = DB::table('entrantes_detail')->where('entrantes_id', $id)->get();

?>

<div class="panel panel-default">
   
        <table class="table">
            <thead>
                <tr>
                    <th>Código de artículos </th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($data as $d)
                <tr key={{$d->entrantes_id}}>
                    <td>{{ $d->articulos_id }}</td>
                    <td>{{ $d->cantidad }}</td>
                </tr> 
                  
              @endforeach


            </tbody>
        </table>
    </div>
</div>


