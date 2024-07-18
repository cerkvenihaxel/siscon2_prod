<!-- resources/views/components/dispensa-chart.blade.php -->

<div class="validation-container">
    <canvas id="validationChart" width="600" height="700"></canvas>
</div>

<style>
    .dispensa-container {
        margin: auto;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('validationChart').getContext('2d');
        //var datasetAssignated = @json($datasetAssignated);
        var datasetValidate = @json($datasetValidate);

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [
                    /*{
                    label: 'Cantidad de validaciones asignadas en el periodo',
                    data: datasetAssignated,
                    backgroundColor: 'rgb(236, 112, 99)',
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 5,
                }, */{
                    label: 'Cantidad de validaciones realizadas en el periodo',
                    data: datasetValidate,
                    backgroundColor: 'rgb(155, 89, 182)',
                    borderColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 5,
                }]
            },
            options: {
                indexAxis: 'x',
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Punto de dispensa'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Cantidad de validaciones'
                        }
                    }
                },
                maintainAspectRatio: false,
                responsive: true
            },
        });
    });
</script>
