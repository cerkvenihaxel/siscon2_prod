@extends('crudbooster::admin_template')

@section('content')

<?

$medicos = DB::table('medicos')->get();

?>
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
        <h1 class="logo"> SISCON REPORTES </h1>

        <div class="card mb-24">
            <h5 class="card-header">Módulo de reportes para médicos</h5>
            <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text">Seleccione un médico de la lista.</p>

                <div class="formulario">
                    <form method="POST">
                        @csrf
                        <div class="col-6">
                            <select class="js-example-basic-single js-states form-control" id="id_label_single"
                                name="opcion">
                                <option value="1">MEDICO SISTEMA</option>
                                <option value="2">MEDICO SISTEMA</option>
                                <option value="485">GUCHEA CARLOS ALBERTO</option>
                                <option value="528">SOMERVILLE HAROLD</option>
                                <option value="642">SOSA FRANCISCO NICOLAS</option>
                                <option value="650">DIAZ ORONA ANGEL</option>
                                <option value="652">PALIZA ENRIQUE DELFIN</option>
                                <option value="715">DE LA VEGA ALCALDE MARIO</option>
                                <option value="720">GALIMBERTI ANDRES PABLO</option>
                                <option value="727">ESPECHE CARLOS EDUARDO</option>
                                <option value="764">Calvo Gonzalo</option>
                                <option value="767">VALERIANO ORLANDO GABRIEL</option>
                                <option value="776">MACISZONEK DANIEL</option>
                                <option value="803">ZALAZAR RICARDO</option>
                                <option value="867">ROMERO RAUL WALTER</option>
                                <option value="898">LLORENS PERERA MARCOS</option>
                                <option value="929">CEJAS CLAUDIO ARMANDO</option>
                                <option value="933">AYAN JAVIER ANTONIO</option>
                                <option value="947">VAZQUEZ ROBERTO RENE</option>
                                <option value="961">DOMINGUEZ RICARDO OSCAR NICOLAS</option>
                                <option value="974">SOTERAS JORGE HECTOR</option>
                                <option value="985">VARGAS JUAN CARLOS</option>
                                <option value="997">BARRIENTOS JAVIER GONZALO</option>
                                <option value="1002">SAIN RAUL EDUARDO</option>
                                <option value="1004">RUBIO MARTIN D ELIAS</option>
                                <option value="1007">LUDUEÑA RAUL SERGIO</option>
                                <option value="1008">MATELLON JUAN MARTIN</option>
                                <option value="1010">OLIVA CERKVENIH CRISTIAN</option>
                                <option value="1012">GONZALEZ VICTOR HUGO</option>
                                <option value="1013">PAEZ SERGIO NICOLAS</option>
                                <option value="1014">GOITEA GABRIEL HERNAN</option>
                                <option value="1015">CASTRO GOMEZ CRISTIAN EDUARDO</option>
                                <option value="1016">BALLARINO DANIEL EDUARDO</option>
                                <option value="1017">DIPP JHONNATAN MOHAMED</option>
                                <option value="1020">CUBBILLO JESUS LUIS</option>
                                <option value="1022">SANTANDER MARIO FEDERICO</option>
                                <option value="1024">CARRIZO SANTANDER EMILIANO</option>
                                <option value="1032">FLORES ALEJANDRO</option>
                                <option value="1034">ALTUBE GONZALO</option>
                                <option value="1035">OLIVERA CARLOS</option>
                                <option value="1036">DIAZ FERNANDO</option>
                                <option value="1037">CAMPOS CARLOS ALBERTO</option>
                                <option value="1038">NICOLA FRANCO</option>
                                <option value="1041">BOIERO JUAN PABLO</option>
                                <option value="1044">ROMERO LEONARDO JAVIER</option>
                                <option value="1047">DE CAMINOS EVANGELINA</option>
                                <option value="1051">UHRIG MARCOS ARIEL</option>
                                <option value="1056">VARGAS CLAUDIO MARTIN</option>
                                <option value="1057">SOSA ROBERTO ALEJANDRO</option>
                                <option value="1059">MERCADO EDGARDO OMAR</option>
                                <option value="1060">VEGA LAZARTE PEDRO</option>
                                <option value="1062">ROMERO CARLOS IVAN</option>
                                <option value="1064">CEJAS MARIÑO ARIEL</option>
                                <option value="1068">PARISI SEBASTIAN</option>
                                <option value="1073">HRELLAC NICOLAS</option>
                                <option value="1079">RODRIGUEZ RAMIREZ ELMER</option>
                                <option value="1081">TOTI RODOLFO DANIEL</option>
                                <option value="1082">ARABEL BRIZUELA EMILIA</option>
                                <option value="1087">BUSTAMANTE SONIA</option>
                                <option value="1088">SANCHEZ MADOERY JUAN CARLOS</option>
                                <option value="1093">CARREÑO GABRIEL FERNANDO</option>
                                <option value="1109">GUGLIERI GERMAN</option>
                                <option value="1121">LUCCA SERGIO D</option>
                                <option value="1126">PARISI PABLO</option>
                                <option value="1127">PANETTA ANALIA</option>
                                <option value="1128">BALLARINO LAURA</option>
                                <option value="1167">MURARO JUAN M</option>
                                <option value="1177">JUAN PAEZ</option>
                                <option value="1178">GRANILLO VALDEZ MARIA</option>
                                <option value="1197">FERNANDO FLORES</option>
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

                        <button type="submit" formaction="{{route('dateRangeMed')}}" class="btn btn-primary">Realizar
                            consulta</button>
                        <label for="inputForm">Exportar Archivo Excel</label>
                        <input type="submit" formaction="{{route('exportExcelMedAll')}}" class="btn btn-success" id="inputForm">

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
