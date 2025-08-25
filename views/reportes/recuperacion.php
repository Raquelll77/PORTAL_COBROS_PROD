<div class="contenedor">
    <div class="contenedor-95">
        <h1>Reporte de Pagos</h1>
        <!-- Formulario para descargar pagos -->
        <form id="form-descargar" method="POST" action="<?= BASE_URL ?>/reportes-recuperacion">

            <fieldset>
                <legend>
                    <h3>Ingrese el rango de fechas de los pagos</h3>
                </legend>
                <div class="grid-3">
                    <div class="campo">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio">
                    </div>
                    <div class="campo">
                        <label for="fecha_fin">Fecha Final</label>
                        <input type="date" name="fecha_fin">
                    </div>
                    <button id="btn-descargar" class="boton-excel" type="submit"><i
                            class="file alternate outline icon"></i>Descargar
                        Excel</button>
                </div>
            </fieldset>
        </form>

        <!-- Gráfico de Pagos por Gestor -->
        <h2>Pagos mensual por Gestor</h2>
        <div class="grafico">
            <h3 id="total-general"></h3>
            <canvas id="grafico-pagos"></canvas>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('grafico-pagos').getContext('2d');

        // Datos enviados desde el backend
        const gestores = <?php echo json_encode($gestores); ?>;
        const totales = <?php echo json_encode($totales); ?>;

        // Calcula el total general
        const totalGeneral = totales.reduce((acc, val) => acc + val, 0);

        // Muestra el total general encima del gráfico
        document.getElementById('total-general').textContent = `Total General Recuperado: L ${totalGeneral.toLocaleString()}`;

        console.log(gestores); // Verifica los datos en la consola
        console.log(totales);

        // Configuración del gráfico
        const data = {
            labels: gestores,
            datasets: [{
                label: 'Total Recuperado (por Gestor)',
                data: totales,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        const chart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Recuperado (L)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Gestores'
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>