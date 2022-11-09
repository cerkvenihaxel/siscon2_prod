@extends('crudbooster::admin_template')
@section('content')

<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:400,100,900);
    
.base{
    background-color: white;
}
html,
body {
  height: 100%;
  width: 100%; 
  background: white;
  font-weight: 400;
}

.wrapper .base{
  display: table;
  height: 100%;
  width: 100%;
}

.container-fostrap {
  display: table-cell;
  padding: 1em;
  text-align: center;
  vertical-align: middle;
}

h1.heading {
  color: #fff;
  font-size: 1.15em;
  font-weight: 900;
  margin: 0 0 0.5em;
  color: #222d32;
}
@media (min-width: 450px) {
  h1.heading {
    font-size: 3.55em;
  }
}
@media (min-width: 760px) {
  h1.heading {
    font-size: 3.05em;
  }
}
@media (min-width: 900px) {
  h1.heading {
    font-size: 3.25em;
    margin: 0 0 0.3em;
  }
} 
.card {
  display: block; 
    margin-bottom: 20px;
    line-height: 1.42857143;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); 
    transition: box-shadow .25s; 
}
.card:hover {
  box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
}
.img-card {
  width: 100%;
  height:200px;
  border-top-left-radius:2px;
  border-top-right-radius:2px;
  display:block;
    overflow: hidden;
}
.img-card img{
  width: 100%;
  height: 200px;
  object-fit:cover; 
  transition: all .25s ease;
} 
.card-content {
  padding:15px;
  text-align:left;
}
.card-title {
  margin-top:0px;
  font-weight: 700;
  font-size: 1.65em;
}
.card-title a {
  color: #000;
  text-decoration: none !important;
}
.card-read-more {
  border-top: 1px solid #D4D4D4;
}
.card-read-more a {
  text-decoration: none !important;
  padding:10px;
  font-weight:600;
  text-transform: uppercase
}
    </style>


<section class="box">
    <div class="container-fostrap">
        <div>
            <h1 class="heading">
                Asistencia Virtual para proveedores
            </h1>
        </div>
        <div class="content">
            <div class="container-sm">
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="card">
                            <a class="img-card" href="/pdf/asistProveedor.pdf">
                            <img src="/pdflogo.png" />
                          </a>
                            <div class="card-content">
                                <h4 class="card-title">
                                    <a> Instructivo PDF
                                  </a>
                                </h4>
                                <p class="">
                                    En el instructivo PDF se encuentra la información necesaria para el uso de la plataforma.
                                </p>
                            </div>
                            <div class="card-read-more">
                                <a href="/pdf/asistProveedor.pdf" target="_blank" class="btn btn-link btn-block">
                                    Ir al instructivo
                                </a>
                            </div>
                        </div>

                        

                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="card">
                            <a class="img-card" href="https://www.youtube.com/watch?v=52Kl2XK79-s">
                            <img src="/youtube.png" />
                          </a>
                            <div class="card-content">
                                <h4 class="card-title">
                                    <a> Videotutorial
                                  </a>
                                </h4>
                                <p class="">
                                    ¿Necesitas ayuda? En el videotutorial se explica paso a paso como utilizar la plataforma.                                </p>
                            </div>
                            <div class="card-read-more">
                                <a href="https://www.youtube.com/watch?v=52Kl2XK79-s" target="_blank" class="btn btn-link btn-block">
                                    Ir a los videotutoriales
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="card">
                            <a class="img-card" href="https://wa.me/5493804447733/?text=Hola%20necesito%20ayuda%20con%20SISCON">
                            <img src="/logo-wasap.png" />
                          </a>
                            <div class="card-content">
                                <h4 class="card-title">
                                    <a>Número de contacto
                                  </a>
                                </h4>
                                <p class="">
                                    Si tienes alguna duda o consulta, puedes comunicarte con nosotros al número del link de
                           whatsapp.
                                </p>
                            </div>
                            <div class="card-read-more">
                                <a href="https://wa.me/5493804447733/?text=Hola%20necesito%20ayuda%20con%20SISCON"
                                target="_blank" class="btn btn-link btn-block">
                                    Ir a whatsapp
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    

                </div>


            </div>
        </div>
    </div>
</section>

@endsection
