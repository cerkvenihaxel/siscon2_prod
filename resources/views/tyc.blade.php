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
                Términos y condiciones
            </h1>
        </div>
        <div class="content">
            <div class="container-sm">
                <div class="row">
                        <div class="card">
                            <a class="img-card" href="/pdf/tyc.pdf">
                            <img src="/pdflogo.png" />
                          </a>
                            <div class="card-content">
                                <h4 class="card-title">
                                    <a> Términos y condiciones de uso.
                                  </a>
                                </h4>
                                <p class="">
                                    En el instructivo PDF se encuentran los términos y condiciones para el uso de la plataforma.
                                </p>
                            </div>
                            <div class="card-read-more">
                                <a href="/pdf/tyc.pdf" target="_blank" class="btn btn-link btn-block">
                                    Ver términos y condiciones
                                </a>
                            </div>
                        </div>

                        

                
            
                    
                    

                </div>


            </div>
        </div>
    </div>
</section>

@endsection
