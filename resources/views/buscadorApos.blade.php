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


           $data = DB::table('entrantes')->where('nroAfiliado', $id)->get();
           
           foreach ($data as $b) {
            $art = $b->id;
            $nombre = $b->afiliados_id;
            $medicoID = $b->medicos_id;
            $clinicaID = $b->clinicas_id;
            $estadopacienteID = $b->estado_paciente_id;
            $estadosolicitudID = $b->estado_solicitud_id;

            # code...
           }
          
           $articles = DB::table('entrantes_detail')->where('entrantes_id', $art)->get();

           foreach($articles as $a){
            $articulo = $a->articulos_id;
           }

           $art = DB::table('articulos')->where('id', $articulo)->get();

              foreach($art as $a){
                $articuloSolicitud = $a->des_articulo;
              }


           $apeynombres = DB::table('afiliados')->where('id', $nombre)->get();
           $medicos = DB::table('medicos')->where('id', $medicoID)->get();
           $clinicas = DB::table('clinicas')->where('id', $clinicaID)->get();
           $estadopaciente = DB::table('estado_paciente')->where('id', $estadopacienteID)->get();
           $estadosolicitud = DB::table('estado_solicitud')->where('id', $estadosolicitudID)->get();


          foreach ($apeynombres as $ayn) {
            $apeynom = $ayn->apeynombres;
          }
            foreach ($medicos as $med) {
            $medico = $med->nombremedico;
            }
            
            foreach ($clinicas as $cli) {
            $clinica = $cli->nombre;
            }

            foreach ($estadopaciente as $est) {
            $estadoPaciente = $est->estado;
            }

            foreach($estadosolicitud as $est){
            $estadoSolicitud = $est->estado;
            }

              echo "<div class='panel panel-default'>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-hover'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Nombre Afiliado </th>";
                            echo "<th>Número de Afiliado</th>";
                            echo "<th>Médico solicitante</th>";
                            echo "<th>Clínica</th>";
                            echo "<th>Estado Paciente</th>";
                            echo "<th>Estado Solicitud</th>";
                            echo "<th>Fecha de carga</th>";
                            echo "<th>Observación</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                        foreach ($data as $d) {
                            echo "<tr key=".$d->entrantes_id.">";
                                echo "<td>".$d->afiliados_id = $apeynom."</td>";
                                echo "<td>".$d->nroAfiliado."</td>";
                                echo "<td>".$d->medicos_id = $medico."</td>";
                                echo "<td>".$d->clinicas_id = $clinica."</td>";
                                echo "<td>".$d->estado_paciente_id = $estadoPaciente."</td>";
                                echo "<td>".$d->estado_solicitud_id = $estadoSolicitud."</td>";
                                echo "<td>".$d->created_at."</td>";
                                echo "<td>".$d->observaciones."</td>";
                            echo "</tr>";
                        }
                    echo "</tbody>";

                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Artículo solicitado</th>";
                            echo "<th>Grupo </th>";
                            echo "<th>Cantidad</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                        foreach ($articles as $a) {
                            echo "<tr key=".$a->entrantes_id.">";
    	                        echo "<td>".DB::table('articulos')->where('id', $a->articulos_id)->value('des_articulo')."</td>";
                                echo "<td>".$a->grupo."</td>";
                                echo "<td>".$a->cantidad."</td>";
                            echo "</tr>";
                        }
                       

                    } 
        }

    @endphp
    </div>
</html>


@endsection
