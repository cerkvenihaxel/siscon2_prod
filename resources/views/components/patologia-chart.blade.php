<div class="patologia-container">
    <canvas id="pieChart" width="600" height="500"></canvas>
</div>

<style>
    #patologiaChartsContainer {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    #patologiaChartsContainer > div {
        flex: 1;
        min-width: 300px;
        max-width: 50%;
    }

    @media screen and (max-width: 768px) {
        #patologiaChartsContainer {
            flex-direction: column;
        }

        #patologiaChartsContainer > div {
            max-width: 100%;
            width: 100%;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('pieChart').getContext('2d');
        var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Cantidad de afiliados por patología',
                        data: @json($dataset),
                        backgroundColor:  [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 99, 71, 0.2)',
                        'rgba(135, 206, 235, 0.2)',
                        'rgba(144, 238, 144, 0.2)',
                        'rgba(218, 112, 214, 0.2)',
                        'rgba(255, 215, 0, 0.2)',
                        'rgba(60, 179, 113, 0.2)',
                        'rgba(220, 20, 60, 0.2)',
                        'rgba(0, 191, 255, 0.2)',
                        'rgba(123, 104, 238, 0.2)',
                        'rgba(255, 140, 0, 0.2)'
                        ],
            borderColor: [
                'rgb(236, 112, 99)',
                'rgb(155, 89, 182)',
                'rgb(41, 128, 185)',
                'rgb(72, 201, 176)',
                'rgb(130, 224, 170)',
                'rgb(241, 196, 15)',
                'rgb(243, 156, 18)',
                'rgb(244, 236, 247)',
                'rgb(23, 32, 42)',
                'rgb(52, 73, 94)',
                'rgb(11, 83, 69)',
                'rgb(250, 219, 216)',
                'rgb(23, 32, 42)',
                'rgb(191, 201, 202)',
                'rgb(121, 125, 127)',
                'rgb(23, 32, 42)'
            ],
            borderWidth: 2,
    }]
    },
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Patologías'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Cantidad de afiliados por patología'
                    }
                }
            },
            plugins: {
                datalabels: {
                    color: '#000',
                    align: 'end',
                    anchor: 'end',
                    formatter: function(value, context) {
                        return context.chart.data.labels[context.dataIndex];
                    },
                    listeners: {
                        click: function(context) {
                            var label = context.chart.data.labels[context.dataIndex];
                            alert('Label clicked: ' + label);
                        }
                    }
                }
            },
            maintainAspectRatio: false,
                responsive: true
        },

    });
    });
</script>
