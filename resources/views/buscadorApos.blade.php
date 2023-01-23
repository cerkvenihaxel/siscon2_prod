@extends('layoutBuscador')

@section('content')

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body >
    <div class="container">
    <img id="logo" src="/LOGOAPOS2.png" height="200" class="img-fluid img-thumbnail">
    <style>
        #logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding: 3rem;
            border: 0;
        }</style>
    <form action="" method="GET">
        <input class="form-control" type="text" name="busqueda" placeholder="Búsqueda por Número de Afiliado"> <br>
        <input class="btn btn-primary" type="submit" name="enviar" value="Buscar">
    </form>
    
    <br><br><br>
    </div>

    <div class="container">
    @php
       if(isset($_GET['busqueda'])){
           $id = $_GET['busqueda'];

           //$id < 9 ? $id = $dni : $id = $id;

           if($id == ""){ 
               echo "<h3> No se ha ingresado ningún número de afiliado </h3>";}
              else{


           $idAfiliado = DB::table('entrantes')->where('nroAfiliado', $id)->get();

           
           
           $nombreAfiliado = DB::table('afiliados')->where('nroAfiliado', $id)->value('apeynombres');

           $fecha = date("d-m-Y H:i:s"); 

           echo "<h1>Nombre del afiliado: ".$nombreAfiliado."</h1>";

           echo "<h3>Fecha de consulta: ".$fecha."</h3>";

          echo "<div class='table-responsive'>";
             echo "<h2>Solicitud cargada por el médico</h2>";
              echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
                echo "<thead>";
                    echo "<tr>";
                            echo "<th>Fecha creada</th>";
                            echo "<th>Numero de solicitud</th>";
                            echo "<th>Nombre del Afiliado</th>";
                            echo "<th>Nombre del médico</th>";
                            echo "<th>Nombre paciente</th>";
                            echo "<th>Observación</th>";
                            echo "<th>Artículos requeridos</th>";
                            echo "<th>Cantidad</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                    foreach($idAfiliado as $entrantes){
                        echo "<tr>";
                            echo "<td>".$entrantes->created_at."</td>";
                            echo "<td>".$entrantes->nrosolicitud."</td>";
                            echo "<td>".$nombreAfiliado."</td>";
                            echo "<td>".$medicos_id = DB::table('medicos')->where('id', $entrantes->medicos_id)->value('nombremedico')."</td>";
                            echo "<td>".$entrantes->afiliados_id."</td>";
                            echo "<td>".$entrantes->observaciones."</td>";
                            
                            //Articulo lógica 

                            $articulos = DB::table('entrantes_detail')->where('entrantes_id', $entrantes->id)->get('articulos_id');
                            $cantidad = DB::table('entrantes_detail')->where('entrantes_id', $entrantes->id)->value('cantidad');

                            foreach ($articulos as $articulo){  
                                echo "<td>". $articulosNombre = DB::table('articulos')->where('id', $articulo->articulos_id)->value('des_articulo')."</td>";
                                echo "<td>".$cantidad."</td>";
                            }
                            
                        echo "</tr>";
                    }
                echo "</tbody>";

            echo "</table>";

            echo "<div class='table-responsive'>";
            echo "<h2>Cotización de proveedores</h2>";
                 echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Fecha de cotización</th>";
                            echo "<th>Número de Solicitud</th>";
                            echo "<th>Nombre del proveedor</th>";
                        echo "</tr>";
                    echo "</thead>";

                    echo "<tbody>";
                        foreach($idAfiliado as $idAfi){
                            $cotizaciones = DB::table('cotizaciones')->where('nrosolicitud', $idAfi->nrosolicitud)->get();
                        
                            foreach($cotizaciones as $cotizacion){
                            echo "<tr>";
                                echo "<td>".$cotizacion->created_at."</td>";
                                echo "<td>".$cotizacion->nrosolicitud."</td>";
                                echo "<td>".$cotizacion->proveedor."</td>";
                            echo "</tr>";
                        }

                        }
                    echo "</tbody>";

                echo "</table>";

            echo "</div>";

            echo "<div class='table-responsive'>";
            echo "<h2>Adjudicaciones de solicitud</h2>";
                 echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Fecha de adjudicación</th>";
                            echo "<th>Número de Solicitud</th>";
                            echo "<th>Nombre del proveedor adjudicado</th>";
                        echo "</tr>";
                    echo "</thead>";

                    echo "<tbody>";
                        foreach($idAfiliado as $idAfi){
                            $ordenes = DB::table('adjudicaciones')->where('nrosolicitud', $idAfi->nrosolicitud)->get();
                        
                            foreach($ordenes as $orden){
                            echo "<tr>";
                                echo "<td>".$orden->created_at."</td>";
                                echo "<td>".$orden->nrosolicitud."</td>";
                                echo "<td>".$orden->adjudicatario."</td>";
                               
                            echo "</tr>";
                        }

                        }
                    echo "</tbody>";

                echo "</table>";
                       
               
        }

    }

    @endphp
    </div>
</html>


@endsection
