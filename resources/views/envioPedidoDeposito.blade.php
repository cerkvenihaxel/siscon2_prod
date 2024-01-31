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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
</head>

<body>
    <div class="container container-sm overflow-hidden">
        <h1 class="logo"> Envío de pedido a depósito  (CONVENIO MEDICAMENTOS) </h1>

        <div class="card mb-24">
            <h5 class="card-header">Convenio Medicamentos APOS</h5>
            <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text">Seleccione una farmacia o punto de retiro.</p>

                <div class="formulario">
                    <form method="POST">
                        @csrf
                        <div class="col-6">
                            <select class="js-example-basic-single js-states form-control" id="id_label_single" name="opcion">
                                <option value="0">Seleccione una opción</option>
                                <option value="3">FARMANOR I - San Andrés 1 (Zona Este)</option>
                                <option value="4">FARMANOR II  - San Andrés 2 (Zona Norte)</option>
                                <option value="5">FARMANOR III - Santo Tomás (Zona Centro)</option>
                                <option value="6">FARMANOR IV - HIPERFARMA (Zona Sur)</option>
                                <option value="7">FARMANOR V - GALENICA (Zona Centro)</option>
                                <option value="8">FARMANOR VI - FARMANOR (Zona Oeste)</option>
                                <option value="9">FARMANOR IX - TELCOS II (Zona Norte)</option>
                                <option value="10">FARMANOR VII - CHILECITO</option>
                                <option value="11">FARMANOR VIII - REAL (Zona Centro)</option>
                                <option value="12">FARMANOR X - TELCOS 3</option>
                            </select>
                            </label>

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
                                        daysOfWeek: [
                                            "Do",
                                            "Lu",
                                            "Ma",
                                            "Mi",
                                            "Ju",
                                            "Vi",
                                            "Sa"
                                        ],
                                        monthNames: [
                                            "Enero",
                                            "Febrero",
                                            "Marzo",
                                            "Abril",
                                            "Mayo",
                                            "Junio",
                                            "Julio",
                                            "Agosto",
                                            "Septiembre",
                                            "Octubre",
                                            "Noviembre",
                                            "Diciembre"
                                        ],
                                    },

                                });
                            });

                            $(document).ready(function () {
                                $('.js-example-basic-single').select2({
                                    templateSelection: formatState
                                });
                            });

                        </script>

                      <button type="submit" formaction="{{route('dateRangeDeposito')}}" class="btn btn-primary">Ver pedido</button>
                        <!--<label for="inputForm">Exportar Archivo Excel</label>
                        <input type="submit" formaction="{{route('exportExcelMedAll')}}" class="btn btn-success" id="inputForm"> -->

                    </form>



                </div>
            </div>
        </div>

        <style>
            .formulario {
                margin-left: 20%;
                margin-right: 20%;
                display: grid;
                grid-auto-flow: column;
                grid-column-gap: 10px;
                display: block;
            }

            .container {
                width: 100%;
                max-width: 100%;
                padding: 15px;
                margin: auto;

            }

            #logo {
                display: block;
                margin-left: auto;
                margin-right: auto;
                padding: 3rem;
                border: 0;
            }


            .datepicker {
                margin-left: auto;
            }

            .fechas {

                padding-top: 2rem;
                padding-bottom: 2rem;
            }

        </style>
        <br><br><br>
    </div>

</body>

@endsection
