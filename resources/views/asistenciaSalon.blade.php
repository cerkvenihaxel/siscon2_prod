@extends('layoutAsistencia')

@section('content')

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <!-- Font Awesome CDN -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
  
<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .row h1 {
        width: 100%;
        text-align: center;
        font-size: 3.75em;
        margin: 0.6em 0;
        font-weight: 600;
        color: #070024;
    }

    .column {
        padding: 1em;
    }

    .card {
        padding: 3.1em 1.25em;
        text-align: center;
        background: linear-gradient(0deg, #397ef6 10px, transparent 10px);
        background-repeat: no-repeat;
        background-position: 0 0.62em;
        box-shadow: 0 0 2.5em rgba(0, 0, 0, 0.15);
        border-radius: 0.5em;
        transition: 0.5s;
        cursor: pointer;
    }

    .card .icon {
        font-size: 2.5em;
        height: 2em;
        width: 2em;
        margin: auto;
        background-color: #397ef6;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: #ffffff;
    }

    .icon:before {
        position: absolute;
        content: "";
        height: 1.5em;
        width: 1.5em;
        border: 0.12em solid #397ef6;
        border-radius: 50%;
        transition: 0.5s;
    }

    .card h3 {
        font-size: 1.3em;
        margin: 1em 0 1.4em 0;
        font-weight: 600;
        letter-spacing: 0.3px;
        color: #070024;
    }

    .card p {
        line-height: 2em;
        color: #625a71;
    }

    .card:hover {
        background-position: 0;
    }

    .card:hover .icon:before {
        height: 2.25em;
        width: 2.25em;
    }

    @media screen and (min-width: 768px) {
        section {
            padding: 1em 7em;
        }
    }

    @media screen and (min-width: 992px) {
        section {
            padding: 1em;
        }

        .card {
            padding: 5em 2em;
        }

        .column {
            flex: 0 0 33.33%;
            max-width: 33.33%;
            padding: 0 1em;
        }
    }

</style>
<body >
    <div class="container">
        <section>
            <div class="row">
                <h1>Opciones</h1>
            </div>
            <div class="row">
                <!-- Column One -->
                <div class="column">
                    <div class="card">
                        <div class="icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3>Instructivo PDF</h3>
                        <p>
                            En el instructivo PDF se encuentra la información necesaria para el uso de la plataforma.

                        </p>
                    </div>
                </div>
                <!-- Column Two -->
                <div class="column">
                    <div class="card">
                        <div class="icon">
                            <i class="fa-solid fa-play"></i>
                        </div>
                        <h3>Videotutorial</h3>
                        <p>
                            ¿Necesitas ayuda? En el videotutorial se explica paso a paso como utilizar la plataforma.
                        </p>
                    </div>
                </div>
                <!-- Column Three -->
                <div class="column">
                    <div class="card">
                        <div class="icon">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h3>Número de contacto</h3>
                        <p>
                            Si tienes alguna duda o consulta, puedes comunicarte con nosotros al número del link de
                            whatsapp.
                        </p>
                        <a href="https://wa.me/5493804447733/?text=Hola%20necesito%20ayuda%20con%20SISCON"
                            target="_blank">
                            <img src="logo-wasap.png" width="50" height="50"> Ir a whatsapp
                        </a>
                    </div>
                </div>
            </div>
        </section>
   
    
    <br><br><br>
    </div>

    <div class="container">
        <iframe src="/pdf/salonAsist.pdf" width="1200px" height="900px"></iframe>
    </div>

     
</html>


@endsection
