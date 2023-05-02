@php
$resultados = [];
$mostrarTabla = false;
@endphp

    <head>
        <meta charset="utf-8" lang="es_ES" >
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="SISCON.png">
        <title>Buscador SISCON Convenio</title>
        <!-- Estilos de Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <!-- Estilos de AdminLTE -->
        <link rel="stylesheet" href="https://adminlte.io/themes/v3/css/adminlte.min.css">
      </head>

  <body class="hold-transition sidebar-mini layout-fixed">

    <nav class="navbar navbar-expand-lg navbar-primary bg-primary">
        <a class="navbar-brand" href="http://www.siscon.info">
            SISCON
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Inicio <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Acerca de</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Contacto</a>
            </li>
          </ul>
        </div>
      </nav>

    <div class="wrapper">
      <!-- Barra de navegación -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <!-- Formulario de búsqueda -->
        <div class="container d-flex justify-content-center align-items-center mt-4 mb-4">
            <div class="card card-outline-primary">
              <div class="card-header">
                <img src="SISCON.png" alt="Logo de la página" class="mx-auto d-block">
              </div>
              <div class="card-body">
                <form action="" method="GET">
                    <input class="form-control" type="text" name="busqueda" placeholder="Búsqueda por Número de Afiliado"> <br>
                    <input class="btn btn-primary btn-block" type="submit" name="enviar" value="Buscar">

                </form>
              </div>
            </div>
          </div>


        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Opciones de usuario -->
        </ul>
      </nav>

      <div class="card w-100 mx-auto">
        <div class="card-body">
          <p class="card-text h5 text-center">
            La información sobre los pacientes y su medicación es confidencial y está protegida por la ley. Esta información es utilizada únicamente con fines de gestión y atención médica por la gerenciadora y obra social correspondiente.
          </p>
        </div>
      </div>


      @php

      if(isset($_GET['busqueda'])){

        $id = $_GET['busqueda'];

        if($id == ""){
               echo "<h3> No se ha ingresado ningún número de afiliado </h3>";}
              else{
        $nroAfiliado = $id;
        $data = DB::table('afiliados_articulos')->where('nro_afiliado', $nroAfiliado)->get();
        $nombreAfiliado = DB::table('afiliados')->where('nroAfiliado', $nroAfiliado)->value('apeynombres');

     echo '<div class="content-wrapper mt-4">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Resultados de la búsqueda del paciente : '.$nombreAfiliado.'</h3>
          </div>
          <div class="card-body">

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID ARTÍCULO</th>
        <th>MEDICACIÓN ARTÍCULOS</th>
        <th>CANTIDAD</th>
        <th>OPCIONES</th>
      </tr>
    </thead>
    <tbody>';



      foreach ($data as $result) {
        echo '<tr>';
          echo'<td>'.$result->id_articulo.'</td>';
         echo'<td>'.$result->articulo.'</td>';
        echo'<td>'.$result->cantidad.'</td>';
        echo '<td>
      <button class="btn btn-sm btn-primary">Editar</button>
      <button class="btn btn-sm btn-danger">Eliminar</button>
      </td>';
        echo'</tr>';
      }


   echo'
   </tbody>
         </table>

            </div>


            </div>





            </div>

            <div class="container-fluid">
  <div class="row d-flex justify-content-center my-3">
    <div class="col-sm-12">
      <div class="btn-group btn-group-lg" role="group" aria-label="...">

      <!-- Agregar botones de acciones de editar ficha -->
        <a href="/admin/pedido_medicamento35/add?id='.$id.'" class="btn btn-success mr-3">Crear ficha</a>
        <button type="button" class="btn btn-primary text-light mr-3">Ver ficha</button>
        <button type="button" class="btn btn-warning text-light mr-3">Guardar</button>
        <button type="button" class="btn btn-danger">Salir</button>
      </div>
    </div>
  </div>
</div>

            <style>
        .nav-link {
            color: #f8f9fa;
        }
        .navbar-brand {
            color: #f8f9fa;
        }
</style>';
          }
        }
@endphp

