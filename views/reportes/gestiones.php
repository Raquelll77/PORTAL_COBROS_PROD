<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="ui header center aligned">Reporte de Gestiones Diario<? //php echo $titulo ?? 'Reporte Gestiones'; ?>
        </h1>

        <!-- <div class="grid-2"> -->
        <article>

            <!-- Formulario para seleccionar la fecha -->
            <form method="POST" action="<?= BASE_URL ?>/reportes-gestiones">
                <div class="campo">
                    <label for="fecha">Seleccione una fecha:</label>
                    <input type="date" id="fecha" name="fecha"
                        value="<?php echo htmlspecialchars($fechaSeleccionada ?? date('Y-m-d')); ?>" required>
                </div>
                <input class="boton-submit" type="submit" value="Ver Reporte">
            </form>

            <!-- Tabla para mostrar los datos -->
            <?php if (!empty($reporteGestionDiaria)): ?>

                <?php
                // Obtener dinámicamente los nombres de las columnas desde los datos
                $columnas = array_keys($reporteGestionDiaria[0]); // Extrae las claves del primer registro
            
                // Inicializar la variable para almacenar los totales de cada columna
                $sumaTotal = array_fill_keys($columnas, 0);

                // Recorrer los datos y sumar cada columna
                foreach ($reporteGestionDiaria as $gestion) {
                    foreach ($columnas as $columna) {
                        $sumaTotal[$columna] += (float) $gestion[$columna];
                    }
                }

                // Siempre mantener la columna "GESTOR" aunque su suma sea 0
                $columnaGestor = 'gestor';
                $columnasConDatos = array_filter($sumaTotal, function ($valor, $columna) use ($columnaGestor) {
                    return $valor > 0 || $columna === $columnaGestor; // Mantiene "gestor" siempre visible
                }, ARRAY_FILTER_USE_BOTH);

                // Si solo queda la columna "gestor" y ninguna más, mostrar mensaje
                if (count($columnasConDatos) === 1 && isset($columnasConDatos[$columnaGestor])) {
                    echo "<p>No hay datos relevantes para mostrar.</p>";
                    return;
                }
                ?>

                <table class="ui celled striped cyan table" border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <?php foreach ($columnasConDatos as $columna => $total): ?>
                                <th><?php echo htmlspecialchars(strtoupper($columna)); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteGestionDiaria as $gestion): ?>
                            <tr>
                                <?php foreach ($columnasConDatos as $columna => $total): ?>
                                    <td><?php echo htmlspecialchars($gestion[$columna]); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php foreach ($columnasConDatos as $columna => $total): ?>
                                <?php if ($columna === 'gestor'): ?>
                                    <th>Total:</th> <!-- Siempre mostrar "Total" en la primera columna -->
                                <?php else: ?>
                                    <th><?php echo ($total > 0) ? number_format($total) : ''; ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>

            <?php else: ?>
                <p>No hay gestiones para la fecha seleccionada.</p>
            <?php endif; ?>



        </article>

        <div class="grid-2">
            <article>
                <canvas id="graficoGestiones" width="400" height="200" style="margin-top: 20px;"></canvas>
            </article>
            <article style=" width: 50%; margin: 0 auto;">
                <h1>Resumen gestion</h1>
                <canvas id="graficoPastel" width="150" height="150" style="margin-top: 20px;"></canvas>
            </article>

        </div>
        <article style=" width: 40%;">
            <h1>Codigo de Resultado</h1>
            <canvas id="graficoCodigos" width="400" height="200" style="margin-top: 20px;"></canvas>
        </article>




        <h1>Historico Gestiones</h1>

        <article>
        <form id="form-descargar" action="<?= BASE_URL ?>/descargar-gestiones" method="POST">
                <div class="grid-3">
                    <div class="campo">
                        <label for="">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio">
                    </div>
                    <div class="campo">
                        <label for="">Fecha Final</label>
                        <input type="date" name="fecha_fin">
                    </div>
                    <button id="btn-descargar" class="boton-excel" type="submit"><i
                            class="file alternate outline icon"></i>Descargar
                        Excel</button>
                </div>
            </form>
            <?php if (!empty($errores)) { ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores Encontrados',
                            html: `
                                                                                                                                                                                                                                            <ul style="text-align: left;">
                                                                                                                                                                                                                                                <?php foreach ($errores as $error): ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                                                                                                                                                                                                                                <?php endforeach; ?>
                                                                                                                                                                                                                                            </ul>
                                                                                                                                                                                                                                        `,
                            confirmButtonText: 'Aceptar'
                        });
                    });
                </script>
            <?php } ?>


        </article>

    </div>


</div>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gráfico de Barras
        const ctxBar = document.getElementById('graficoGestiones').getContext('2d');
        const gestores = <?php echo json_encode($datosProcesados['gestores']); ?>;
        const totales = <?php echo json_encode($datosProcesados['totales']); ?>;

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: gestores,
                datasets: [{
                    label: 'Gestiones Totales',
                    data: totales,
                    backgroundColor: 'rgba(0, 191, 255, 0.2)',
                    borderColor: 'rgba(0, 191, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 60 // Eje Y configurado para un máximo de 60 gestiones
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Gráfico de Pastel Global
        const ctxPie = document.getElementById('graficoPastel').getContext('2d');
        const pastelLabels = ['Realizadas', 'Pendientes'];
        const pastelData = [
            <?php echo $datosProcesados['totalGestiones']; ?>,
            <?php echo (60 * count($datosProcesados['gestores'])) - $datosProcesados['totalGestiones']; ?>
        ];

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: pastelLabels,
                datasets: [{
                    data: pastelData,
                    backgroundColor: ['rgba(0, 191, 255, 0.8)', 'rgba(191, 191, 191, 0.8)']
                }]
            }
        });

        // Gráfico de Pastel por Código
        const ctxCodigos = document.getElementById('graficoCodigos').getContext('2d');
        const pastelLabelsCodigos = <?php echo json_encode(array_keys($datosProcesados['codigoResultados'])); ?>;
        const pastelDataCodigos = <?php echo json_encode(array_values($datosProcesados['codigoResultados'])); ?>; // Totales en lugar de porcentajes

        new Chart(ctxCodigos, {
            type: 'doughnut',
            data: {
                labels: pastelLabelsCodigos,
                datasets: [{
                    data: pastelDataCodigos, // Usa los totales en lugar de porcentajes
                    backgroundColor: [
                        'rgba(0, 191, 255, 0.8)',    // PAGO
                        'rgba(191, 64, 191, 0.8)',   // ABONO
                        'rgba(64, 191, 64, 0.8)',    // PROMESA DE PAGO
                        'rgba(255, 191, 64, 0.8)',   // CANCELACIÓN
                        'rgba(64, 64, 191, 0.8)',    // DECOMISO
                        'rgba(191, 64, 64, 0.8)',    // PARA DECOMISO
                        'rgba(191, 191, 64, 0.8)',   // SE NIEGA A PAGAR
                        'rgba(255, 99, 132, 0.8)',   // PRESTO EL CRÉDITO
                        'rgba(54, 162, 235, 0.8)',   // SE FUE DEL PAÍS
                        'rgba(255, 206, 86, 0.8)',   // CAMBIO DE DOMICILIO
                        'rgba(75, 192, 192, 0.8)',   // FRAUDE
                        'rgba(153, 102, 255, 0.8)',  // ZONA DE RIESGO
                        'rgba(255, 159, 64, 0.8)',   // ILOCALIZABLE
                        'rgba(199, 199, 199, 0.8)',  // PERFIL DE RIESGO
                        'rgba(83, 102, 255, 0.8)',   // DIFUNTO
                        'rgba(255, 153, 86, 0.8)',   // EXCEPCIÓN
                        'rgba(99, 132, 192, 0.8)',   // ROBO
                        'rgba(102, 159, 255, 0.8)'   // TRÁNSITO
                    ]
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Cambia el texto del tooltip para mostrar totales
                            label: function (tooltipItem) {
                                const label = tooltipItem.label || '';
                                const value = tooltipItem.raw || 0; // Obtiene el valor del dataset
                                return `${label}: ${value} gestiones`; // Muestra el total de gestiones
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
</script>