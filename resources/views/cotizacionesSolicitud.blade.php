<?php 

$id = $_GET['id'];
$solicitud = DB::table('adjudicaciones')->where('id', $id)->value('nrosolicitud');
$proveedores = DB::table('cotizaciones')->where('nrosolicitud', $solicitud)->get(); 

foreach ($proveedores as $p) {
    $proveedor = $p->id;
    
}


?>

<div class="panel panel-default">
   
        <table class="table">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Precio final</th>
                    <th>Ver cotización</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($proveedores as $d)
                <tr>
                    <td>{{ $d->proveedor }}</td>
                    <td> $ {{ $d->total }}</td>
                    <td><a href="{{ url('admin/cotizaciones19/detail/'.$d->id) }}" class="btn btn-primary">Ver</a></td>
                </tr> 
                  
              @endforeach 


            </tbody>
        </table>
    </div>
</div>
