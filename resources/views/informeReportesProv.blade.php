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
    <div class="container-fluid">
        <h1 class="text-center"> SISCON REPORTES </h1>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Módulo de reportes para proveedores</h3>
            </div>
            <div class="card-body">
                <p class="card-text">Seleccione un proveedor de la lista.</p>

                <div class="formulario">
                    <form method="POST" class="form-group" >
                        @csrf
                        <div class="col-6">
                            <select class="js-example-basic-single js-states form-control" id="id_label_single"
                                name="opcion">
                                <option value="Proveedor">Proveedor</option>
                                <option value="QRA">QRA</option>
                                <option value="Ortopedia Mayo">Ortopedia Mayo</option>
                                <option value="Forca">Forca</option>
                                <option value="Global Medica">Global Medica</option>
                                <option value="Apos informatica">Apos informatica</option>
                                <option value="SB torres">SB torres</option>
                                <option value="Medkit">Medkit</option>
                                <option value="Miguel Angel">Miguel Angel</option>
                                <option value="Nova">Nova</option>
                                <option value="Nowa">Nowa</option>
                                <option value="Medical Implants">Medical Implants</option>
                                <option value="proveedorUNO">proveedorUNO</option>
                                <option value="proveedorDOS">proveedorDOS</option>
                                <option value="proveedorTRES">proveedorTRES</option>
                                <option value="Ortopedia Rapalar">Ortopedia Rapalar</option>
                                <option value="Bionor">Bionor</option>
                                <option value="Medical Team">Medical Team</option>
                                <option value="Implantes Medicos">Implantes Medicos</option>
                                <option value="Medical Supplies">Medical Supplies</option>
                                <option value="Mat Medical">Mat Medical</option>
                                <option value="North Medical">North Medical</option>
                                <option value="Rioja Implantes">Rioja Implantes</option>
                                <option value="Ortopedia Di Vari">Ortopedia Di Vari</option>
                                <option value="Beta prov">Beta prov</option>
                                <option value="Beta proveedor uno">Beta proveedor uno</option>
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
                                $('.js-example-basic-single').select2();
                            });

                        </script>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <button type="submit" formaction="{{route('dateRangeProv')}}" class="btn btn-primary mr-2">Realizar consulta
                                     <i class="fa fa-search"></i>
                                    </button>
                                    <button type="submit" formaction="{{route('exportExcelProvAll')}}" class="btn btn-success">
                                        Exportar consulta a Excel   <i class="far fa-file-excel"></i>
                                    </button>

                                    <button type="submit" formaction="{{route('exportExcelProvGeneralAll')}}" class="btn btn-warning">
                                        Exportar reporte general   <i class="fa fa-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>


                            </div>
                        </div>


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
