@extends('crudbooster::admin_template')

@section('content')

<html>
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
    <div class="container">
         <h1 class= "logo"> SISCON REPORTES </h1>

         <div class="card mb-24">
            <h5 class="card-header">Módulo de reportes para artículos</h5>
            <div class="card-body">
              <h5 class="card-title"></h5>
              <p class="card-text">Seleccione el número de artículo (puede consultar en panel de informes), luego el período para el reporte. Presione buscar.</p>
            

              <form action="" method="GET">



                
                <input class="form-control mt-12" type="text" name="busqueda" placeholder="Búsqueda por Número de Artículo"> <br>

    

                <input  class="datepicker" type="text" name="daterange" />

<script type="text/javascript">
$(function() {
    $('input[name="daterange"]').daterangepicker({
        format: "dd-mm-yyyy",
    });
});
</script>

<input class="btn btn-primary" type="submit" name="enviar" value="Buscar">

            </form>
              
            




            </div>
          </div>
            <style>
                #logo {
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                    padding: 3rem;
                    border: 0;
                }
                
                .btn-primary{
                    margin-top: 2rem;
                    display: block;
                    margin-left: 1.5rem;
                    margin-right: auto;
                    padding: 1.5rem;
                    border: 0;
                }

                .datepicker {
                    margin-left:auto;
                }
                </style>
            
        
            
            
            
            

            
            <br><br><br>
            </div>

            @php
            if(isset($_GET['busqueda'])){
                $id = $_GET['busqueda'];
                $fecha = $_GET['daterange'];
                //$id < 9 ? $id = $dni : $id = $id;

                if($id == ""){ 
                    echo "<h3> No se ha ingresado ningún número de artículo</h3>";}
                   else{

                    $entrantes_detail= DB::table('entrantes_detail')->where('articulos_id', $id)->get();                    

                    $cotizaciones_detail= DB::table('cotizaciones_detail')->where('articulos_id', $id)->get('entrantes_id');
                   
                    $nombreArticulo = DB::table('articulos')->where('id', $id)->value('des_articulo');
                    
                
                    echo "<h1>Nombre del artículo solicitado: ".$nombreArticulo."</h1>";
                    echo "<h3>Fecha de solicitud: ".$fecha."</h3>";

                    echo "<div class='panel panel-default'>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-hover'>";
                    echo "<h3> Datos de la solicitud del médico</h3>";
                    echo "<thead>";
                        echo "<th>Fecha</th>";
                            echo "<th>Numero de solicitud</th>";
                            echo "<th>Nombre del Afiliado</th>";
                            echo "<th>Nombre del médico</th>";
                            echo "<th>Nombre paciente</th>";
                            echo "<th>Especialidad</th>";
                            echo "<th>Cantidad</th>";
                        echo "</tr>";
                        foreach($entrantes_detail as $end){
                    $entrantes = DB::table('entrantes')->where('id', $end->entrantes_id)->get();

                    echo "<tr key=".$end->id.">";

                        foreach ($entrantes as $key => $d) {
                        

                           
                    echo "</thead>";
                    echo "<tbody>";
                            echo "<td>".$d->created_at."</td>";
                                echo "<td>".$d->nrosolicitud."</td>";
                                echo "<td>".$d->afiliados_id = DB::table('afiliados')->where('id', $d->afiliados_id)->value('apeynombres')."</td>";
                                echo "<td>".$medicos_id = DB::table('medicos')->where('id', $d->medicos_id)->value('nombremedico')."</td>";
                                echo "<td>".$d->afiliados_id."</td>";
                                echo "<td>".$d->grupo_articulos = DB::table('grupos')->where('id', $d->grupo_articulos)->value('des_grupo')."</td>";
                                echo "<td>".$d->cantidad = DB::table('entrantes_detail')->where('id', $end->id)->where('articulos_id', $id)->value('cantidad')."</td>";
                            echo "</tr>";
                        }}
                    echo "</tbody>";

                
                    echo "</table>";
                echo "</div>";


                echo "<br><br><br>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-hover'>";

                
                    echo "<thead>";
                        echo "<h3> Datos de cotizaciones del artículo</h3>";

                        echo "<tr>";
                            echo "<th>Fecha de cotización</th>";
                            echo "<th>Número de solicitud</th>";
                            echo "<th>Proveedor</th>";
                            echo "<th>Nombre médico</th>";
                            echo "<th>Precio Unitario</th>";
                            echo "<th>Cantidad</th>";
                            echo "<th>Total</th>";
                        echo "</tr>";
                        
                    echo "</thead>";
                    echo "<tbody>";
                        foreach ($cotizaciones_detail as $cot) {

                            $cotizaciones = DB::table('cotizaciones')->where('id', $cot->entrantes_id)->get();
                            
                        
                            # code...
                            echo "<tr key=".$cot->id.">";
                        foreach ($cotizaciones as $c) {
                            echo "<td>".$c->created_at."</td>";
                            echo "<td>".$c->nrosolicitud."</td>";
                            echo "<td>".$c->proveedor."</td>";
                            echo "<td>".DB::table('medicos')->where('id', $c->medicos_id)->value('nombremedico')."</td>";
                            echo "<td>".$unitario = DB::table('cotizaciones_detail')->where('entrantes_id', $c->id)->value('precio_unitario')."</td>";
                            echo "<td>".$qty = DB::table('cotizaciones_detail')->where('entrantes_id', $c->id)->value('cantidad')."</td>";
                            echo "<td>".$c->total."</td>";
                            echo "</tr>";
                        }}
                    echo "</tbody>";
                echo "</table>";
            echo "</div>";

                
                
                }
             }
     
         @endphp

</body>

@endsection

