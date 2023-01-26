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

<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
</head>



<body>
    <div class="container">
         <h1 class= "logo"> SISCON REPORTES </h1>

         <div class="card mb-24">
            <h5 class="card-header">Módulo de reportes para artículos</h5>
            <div class="card-body">
              <h5 class="card-title"></h5>
              <p class="card-text">Seleccione el número de artículo (puede consultar en panel de informes), luego el período para el reporte. Presione buscar.</p>
            
              <form method="POST">
                @csrf

                <input class="form-control form-control-sm" id="floatingInput" type="text" name="busqueda" placeholder="Escriba el código del artículo"> <br>
                <label>Seleccione el intervalo de fechas </label>

                <input  class="datepicker" type="text" name="daterange"  />

<script type="text/javascript">
$(function() {
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
</script>

<button type="submit" formaction="{{route('dateRange')}}" class="btn btn-primary">Realizar consulta</button>
<label for="inputForm">Exportar Archivo Excel</label>
<input type="submit" formaction="{{route('exportExcelAll')}}" class="btn btn-success" id="inputForm">

            </form>


          
            </div>
          </div>
            <style>
                #logo {
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                    padding: 3rem;
                    border: 0;
                }
            .btn{

                margin-left: auto;
                margin-right: auto;
                padding: 1.25rem;
                border: 0;
            }

                .datepicker {
                    margin-left:auto;
                }
                </style>
            <br><br><br>
            </div>
 
</body>

@endsection

