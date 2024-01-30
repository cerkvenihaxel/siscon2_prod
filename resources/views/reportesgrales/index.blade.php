@extends('crudbooster::admin_template')

@section('title', 'Reportes Generales')

@section('content')

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
                        <h5 class="card-title text-bold">Informe de Solicitudes Cargadas</h5>
                        <p class="card-text">Medicamentos cargados divididos por patologías</p>
                        <div class="formulario">
                            <form method="POST">
                                @csrf
                                <div class="col-6">
                                    <div class="fechas">
                                        <label>Seleccione el intervalo de fechas </label>
                                        <input class="datepicker" type="text" name="daterange" />
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $('input[name="daterange"]').daterangepicker({
                                            locale: {
                                                format: 'YYYY-MM-DD',
                                                separator: " - ",
                                                applyLabel: "Aplicar",
                                                cancelLabel: "Cancelar",
                                                fromLabel: "Desde",
                                                toLabel: "Hasta",
                                                customRangeLabel: "Personalizado",
                                                daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                            },
                                        });
                                    });
                                </script>
                                <input type="submit" formaction="{{route('reportes_generales.medicamentos')}}" class="btn btn-success"
                                    id="inputForm" value = "Descargar excel">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body bg-white">
                        <h5 class="card-title text-bold">Informe por proveedores</h5>
                        <p class="card-text">Descargar informe con todos los proveedores. Cotizadas, adjudicadas y finalizadas</p>
                        <div class="formulario">
                            <form method="POST">
                                @csrf
                                <div class="col-6">
                                    <div class="fechas">
                                        <label>Seleccione el intervalo de fechas </label>
                                        <input class="datepicker" type="text" name="daterange" />
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $('input[name="daterange"]').daterangepicker({
                                            locale: {
                                                format: 'YYYY-MM-DD',
                                                separator: " - ",
                                                applyLabel: "Aplicar",
                                                cancelLabel: "Cancelar",
                                                fromLabel: "Desde",
                                                toLabel: "Hasta",
                                                customRangeLabel: "Personalizado",
                                                daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                            },
                                        });
                                    });
                                </script>
                                <input type="submit" formaction="{{route('reportes_generales.proveedores')}}" class="btn btn-success"
                                    id="inputForm" value = "Descargar excel">
                            </form>
                        </div>
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
                            <div class="formulario">
                                <form method="POST">
                                    @csrf
                                    <div class="col-6">
                                        <div class="fechas">
                                            <label>Seleccione el intervalo de fechas </label>
                                            <input class="datepicker" type="text" name="daterange" />
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('input[name="daterange"]').daterangepicker({
                                                locale: {
                                                    format: 'YYYY-MM-DD',
                                                    separator: " - ",
                                                    applyLabel: "Aplicar",
                                                    cancelLabel: "Cancelar",
                                                    fromLabel: "Desde",
                                                    toLabel: "Hasta",
                                                    customRangeLabel: "Personalizado",
                                                    daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                    monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                                },
                                            });
                                        });
                                    </script>
                                    <input type="submit" formaction="{{route('reportes_generales.adj-an-sinadj')}}" class="btn btn-success"
                                        id="inputForm" value = "Descargar excel">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 bg-white">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-bold">Informe solicitudes sin cotizar</h5>
                            <p class="card-text">Descargar informe solicitudes sin cotizar</p>
                            <div class="formulario">
                                <form method="POST">
                                    @csrf
                                    <div class="col-6">
                                        <div class="fechas">
                                            <label>Seleccione el intervalo de fechas </label>
                                            <input class="datepicker" type="text" name="daterange" />
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('input[name="daterange"]').daterangepicker({
                                                locale: {
                                                    format: 'YYYY-MM-DD',
                                                    separator: " - ",
                                                    applyLabel: "Aplicar",
                                                    cancelLabel: "Cancelar",
                                                    fromLabel: "Desde",
                                                    toLabel: "Hasta",
                                                    customRangeLabel: "Personalizado",
                                                    daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                    monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                                },
                                            });
                                        });
                                    </script>
                                    <input type="submit" formaction="{{route('reportes_generales.sin-cotizar')}}" class="btn btn-success"
                                        id="inputForm" value = "Descargar excel">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body bg-white">
                            <h5 class="card-title text-bold">Informe por especialidad</h5>
                            <p class="card-text">Descargar informe con todas especialidades</p>
                            
                            <div class="formulario">
                                <form method="POST">
                                    @csrf
                                    <div class="col-6">
                                        <div class="fechas">
                                            <label>Seleccione el intervalo de fechas </label>
                                            <input class="datepicker" type="text" name="daterange" />
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('input[name="daterange"]').daterangepicker({
                                                locale: {
                                                    format: 'YYYY-MM-DD',
                                                    separator: " - ",
                                                    applyLabel: "Aplicar",
                                                    cancelLabel: "Cancelar",
                                                    fromLabel: "Desde",
                                                    toLabel: "Hasta",
                                                    customRangeLabel: "Personalizado",
                                                    daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                    monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                                },
                                            });
                                        });
                                    </script>
                                    <input type="submit" formaction="{{route('reportes_generales.especialidad')}}" class="btn btn-success"
                                        id="inputForm" value = "Descargar excel">
                                </form>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 bg-white">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-bold">Informe por mes</h5>
                            <p class="card-text">Descargar informe solicitudes entrantes por mes</p>
                            <div class="formulario">
                                <form method="POST">
                                    @csrf
                                    <div class="col-6">
                                        <div class="fechas">
                                            <label>Seleccione el intervalo de fechas </label>
                                            <input class="datepicker" type="text" name="daterange" />
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('input[name="daterange"]').daterangepicker({
                                                locale: {
                                                    format: 'YYYY-MM-DD',
                                                    separator: " - ",
                                                    applyLabel: "Aplicar",
                                                    cancelLabel: "Cancelar",
                                                    fromLabel: "Desde",
                                                    toLabel: "Hasta",
                                                    customRangeLabel: "Personalizado",
                                                    daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                    monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                                },
                                            });
                                        });
                                    </script>
                                    <input type="submit" formaction="{{route('reportes_generales.mes')}}" class="btn btn-success"
                                        id="inputForm" value = "Descargar excel">
                                </form>
                            </div>  
                          
                            {{-- <a href="{{route('reportes_generales.mes')}}"class="btn btn-success">Descargar excel</a> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 bg-white">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-bold">Informe por médicos</h5>
                            <p class="card-text">Descargar informe de cada médico diferenciado por meses.</p>
                           
                            <div class="formulario">
                                <form method="POST">
                                    @csrf
                                    <div class="col-6">
                                        <div class="fechas">
                                            <label>Seleccione el intervalo de fechas </label>
                                            <input class="datepicker" type="text" name="daterange" />
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function () {
                                            $('input[name="daterange"]').daterangepicker({
                                                locale: {
                                                    format: 'YYYY-MM-DD',
                                                    separator: " - ",
                                                    applyLabel: "Aplicar",
                                                    cancelLabel: "Cancelar",
                                                    fromLabel: "Desde",
                                                    toLabel: "Hasta",
                                                    customRangeLabel: "Personalizado",
                                                    daysOfWeek: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                                                    monthNames: ["Enero", "Febrero","Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"                                                ],
                                                },
                                            });
                                        });
                                    </script>
                                    <input type="submit" formaction="{{route('reportes_generales.medicos')}}" class="btn btn-success"
                                        id="inputForm" value = "Descargar excel">
                                </form>
                            </div>  
                           
                            {{-- <a href="{{route('reportes_generales.medicos')}}" class="btn btn-success">Descargar excel</a> --}}
                        </div>
                    </div>
                </div>
            </div>


            <style>
            .card {
                margin-bottom: 20px;
                background: white;
                /* height: 20vh; */
                text-align: center;
                padding-top: 2rem;
                border-radius: 14px;
            }
            .card-body {
                padding: 1.25rem;
            }
            .formulario {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .formulario label,
            .formulario input[type="submit"] {
                margin: 10px 0; /* Ajusta los valores superior e inferior según sea necesario */
            }
        </style>
        </div>
    </section>

@endsection
