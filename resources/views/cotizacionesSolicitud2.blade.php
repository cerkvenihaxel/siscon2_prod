
@extends('crudbooster::admin_template')
@section('content');

<?php 

$id = $_GET['id'];
$solicitud = DB::table('cotizaciones')->where('id', $id)->value('nrosolicitud');
$proveedores = DB::table('cotizaciones')->where('nrosolicitud', $solicitud)->get(); 

foreach ($proveedores as $p) {
    $proveedor = $p->id;
    
}


?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    </head>
  <body>


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
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
@endsection

</html>

