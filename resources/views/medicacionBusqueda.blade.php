

<head>
        <meta charset="utf-8" lang="es">
        <title id="titulo">Siscon Medicacion</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/SISCON.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    </head>


    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand ml-12" href="#"> SISCON MEDICAMENTOS</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="window.close()">Cerrar</a>
            </li>
        </ul>
    </nav>
    <div class="bg-light mt-3">
    <div class="container">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">Datos del paciente - Buscar</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">Nombre</h5>
                                <p class="card-text">{{$nombrePaciente}}</p>
                            </div>
                    </div>
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">DNI</h5>
                                <p class="card-text">{{$dniPaciente}}</p>
                            </div>
                    </div>
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">Localidad</h5>
                                <p class="card-text">{{$localidadPaciente}}</p>
                            </div>
                    </div>
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">Teléfono</h5>
                                <p class="card-text">{{$telefonoPaciente}}.</p>
                            </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">Edad</h5>
                                <p class="card-text">{{$edadPaciente}}</p>
                            </div>
                    </div>
                    <div class="col-md-3">
                            <div class="card-body">
                                <h5 class="card-title">Sexo</h5>
                                <p class="card-text"></p>
                            </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="container bg-light mt-3">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">Medicación de la solicitud : {{$nroSolicitud}}</div>

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Código</th>
                <th>Monodroga</th>
                <th>Cantidad</th>
                <th>Ver precio en Kairos web</th>
            </tr>
            </thead>
            <tbody>
                @foreach($pedidoDetail as $pD)
                    <tr>
                    <td>{{$pD->articuloZafiro_id}}</td>
                    <td>{{$monodroga = DB::table('articulosZafiro')->where('id', $pD->articuloZafiro_id)->value('des_monodroga')}}</td>
                    <td>{{$pD->cantidad}}</td>
                    <td>
                        <a href="https://ar.kairosweb.com/?s={{$monodroga}}">
                            LINK
                        </a>
                    </td>
                    </tr>
                @endforeach

            </tbody>
        </table>


    </div>
        <div class="row">
            <div class="col text-center mt-3">
                <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
            </div>
        </div>

    </div>
    </body>

