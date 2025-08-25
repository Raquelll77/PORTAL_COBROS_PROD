<?php include_once __DIR__ . '/../principal/header-dashboard.php' ?>

<div class="contenedor">
    <div class="contenedor-95">
        <form id="download-form" action="<?= BASE_URL ?>/reportes-deterioro" method="post">
            <button class="boton-excel" type="submit">
                <i class="file alternate outline icon"></i>
                Descargar Excel
            </button>
        </form>

        <h1 class="ui header center aligned">Deterioro Mes Actual</h1>
        <canvas id="graficaDeterioro" width="400" height="200"></canvas>

        <br>

        <h1 class="ui header center aligned">Deterioro de Cartera por Gestor y Segmento</h1>
        <canvas id="graficaDeterioro2" width="400" height="200"></canvas>
    </div>
</div>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php' ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"
    integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css"
    integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Obtener los datos de PHP
    const datosGrafica = <?= json_encode($datosGrafica) ?>;

    // Configurar la primera gráfica
    const ctx = document.getElementById('graficaDeterioro').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: datosGrafica.labels,
            datasets: datosGrafica.datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Obtener los datos de PHP para la segunda gráfica
    const datosGrafica2 = <?= json_encode($datosGrafica2) ?>;

    const ctx2 = document.getElementById('graficaDeterioro2').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: datosGrafica2,
        options: {
            responsive: true,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });

    // Interceptar el submit para mostrar loader y forzar descarga
    document.getElementById('download-form').addEventListener('submit', function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Generando archivo...',
            text: 'Por favor, espera mientras se procesa la descarga.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(this.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor.');
                return response.blob();
            })
            .then(blob => {
                Swal.close();

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Reporte_Deterioro.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'El archivo se ha descargado correctamente.'
                });
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            });
    });
</script>
