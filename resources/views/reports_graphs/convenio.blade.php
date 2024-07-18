@extends('crudbooster::admin_template')
@section('content')

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
</head>
<body>
<h1>Datos Convenio Medicamentos</h1>

<div class="fechas">
    <label>Seleccione el intervalo de fechas:</label>
    <input id="daterange" type="text" class="form-control" name="daterange" />
    <button id="resetDates" class="btn btn-secondary" style="margin-top: 10px; background-color: red; color: white">Restablecer Fechas</button>
</div>
<script>
    $(function() {
        $('#daterange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' - ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Personalizado',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
            },
            startDate: '2024-01-01',
            endDate: '2024-12-31'
        }, function(start, end, label) {
            var startDate = start.format('YYYY-MM-DD');
            var endDate = end.format('YYYY-MM-DD');
            updateUrl(startDate, endDate);
        });
    });

    function updateUrl(startDate, endDate) {
        var url = new URL(window.location.href);
        url.searchParams.set('startDate', startDate);
        url.searchParams.set('endDate', endDate);
        window.location.href = url.href;
    }

    document.getElementById('resetDates').addEventListener('click', function() {
        var url = new URL(window.location.href);
        url.searchParams.delete('startDate');
        url.searchParams.delete('endDate');
        window.location.href = url.href;
    });
</script>

<x-topchart :start-date="request()->get('startDate', '2022-01-01')"
            :end-date="request()->get('endDate', '2029-12-31')" />

<section class="graphSection">
    <h2>Gráficos</h2>
    <p>Seleccione de las siguientes pestañas las diferentes secciones</p>
    <div class="btn-group" role="group" aria-label="Select Chart">
        <button type="button" class="btn btn-primary" onclick="showPatologiaCharts()">Patologías</button>
        <button type="button" class="btn btn-primary" onclick="showDispensaChart()">Puntos de dispensa</button>
        <button type="button" class="btn btn-primary" onclick="showMedicacionTable()">Medicación</button>
    </div>

    <div id="patologiaChartsContainer">
        <div id="barChartContainer">
            <x-bar-chart
                :start-date="request()->get('startDate', '2022-01-01')"
                :end-date="request()->get('endDate', '2029-12-31')" />
        </div>

        <div id="patologiaChartContainer">
            <x-patologia-chart :labels="$labels" :dataset="$dataset" />
        </div>
    </div>

    <div id="dispensaChartContainer" style="display: none;">
        <x-dispensa-chart id="dispensaChart"
                          :startDate="request()->get('startDate', '2023-01-01')"
                          :endDate="request()->get('endDate', '2029-12-31')" />
        <x-validation-chart id="validationChart"
                            :startDate="request()->get('startDate', '2023-01-01')"
                            :endDate="request()->get('endDate', '2029-12-31')" />
    </div>


     <div id="medicacionTableContainer" style="display: none;">
         <x-medicamentos-chart  :startDate="request()->get('startDate', '2023-01-01')"
                                :endDate="request()->get('endDate', '2029-12-31')"/>
    </div>





</section>

<script>
    function showPatologiaCharts() {
        document.getElementById('patologiaChartsContainer').style.display = 'block';
        document.getElementById('dispensaChartContainer').style.display = 'none';
        document.getElementById('medicacionTableContainer').style.display = 'none';

    }

    function showDispensaChart() {
        document.getElementById('patologiaChartsContainer').style.display = 'none';
        document.getElementById('dispensaChartContainer').style.display = 'block';
        document.getElementById('medicacionTableContainer').style.display = 'none';
    }

    function showMedicacionTable() {
        document.getElementById('patologiaChartsContainer').style.display = 'none';
        document.getElementById('dispensaChartContainer').style.display = 'none';
        document.getElementById('medicacionTableContainer').style.display = 'block';
    }

</script>
</body>
</html>

<style>
    .graphSection {
        margin: auto;
        padding-top: 2rem;
    }

    .period-selection {
        margin-top: 1rem;
    }
</style>
@endsection
