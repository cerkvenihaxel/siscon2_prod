@extends('crudbooster::admin_template')

@section('title', 'Reportes Generales')

@section('content')

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-2 text-medium">Reportes Generales</h1>
                </div>
            </div>


        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body bg-white">
                        <h5 class="card-title text-bold">Informe por proveedores</h5>
                        <p class="card-text">Descargar informe con todos los proveedores. Cotizadas, adjudicadas y finalizadas</p>
                        <a href="{{route('reportes_generales.proveedores')}}" class="btn btn-success">
                            Descargar excel</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 bg-white">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-bold">Informe por médicos</h5>
                        <p class="card-text">Descargar informe de cada médico diferenciado por meses.</p>
                        <a href="#" class="btn btn-success">Descargar excel</a>
                    </div>
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body bg-white">
                            <h5 class="card-title text-bold">Informe Adjudicadas-Anuladas-Sin Adjudicar</h5>
                            <p class="card-text">Descargar informe con todas las solicitudes restantes</p>
                            <a href="{{route('reportes_generales.adj-an-sinadj')}}" class="btn btn-success">
                                Descargar excel</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 bg-white">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-bold">Informe solicitudes sin cotizar</h5>
                            <p class="card-text">Descargar informe solicitudes sin cotizar</p>
                            <a href="{{route('reportes_generales.sin-cotizar')}}" class="btn btn-success">Descargar excel</a>
                        </div>
                    </div>
                </div>
            </div>


        <style>
            .card {
                margin-bottom: 20px;
                background: white;
                height: 20vh;
                text-align: center;
                padding-top: 2rem;
                border-radius: 14px;
            }
        </style>
        </div>
    </section>

@endsection
