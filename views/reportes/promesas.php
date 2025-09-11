<!-- Chart.js v4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<!-- DataTables con Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="titulo-pagina text-center">Dashboard de Promesas Mensual</h1>

        <!-- Gráfico por gestor -->
        <h2 class="mt-4">Promesas por Gestor</h2>
        <div style="width:100%; margin:auto;">
            <canvas id="graficoPromesas"></canvas>
        </div>

        <!-- Gráfico global -->
        <h2 class="text-center">Cumplidas vs Incumplidas (Global)</h2>
        <div style="width:400px; height:400px; margin:auto;">
            <canvas id="graficoGlobal"></canvas>
        </div>

        <!-- Detalle -->
        <h2 id="titulo-detalle" style="margin-top:30px;">Detalle de Promesas</h2>
        <div class="tabla-contenedor mt-3">
            <table id="tablaDetalle" class="table table-striped table-hover table-bordered" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>Prenumero</th>
                        <th>Fecha Creación</th>
                        <th>Fecha Promesa</th>
                        <th>Monto Promesa</th>
                        <th>Total Pagado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let tablaDetalle;
    let chartGlobal;

    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById("graficoPromesas").getContext("2d");
        const ctxGlobal = document.getElementById("graficoGlobal").getContext("2d");

        // Inicializar DataTable
        tablaDetalle = $("#tablaDetalle").DataTable({
            columns: [
                { data: "prenumero" },
                { data: "fecha_creacion" },
                { data: "fecha_promesa" },
                { data: "montoPromesa" },
                { data: "TotalPagado" },
                { data: "estado_promesa" }
            ],
            buttons: [
                { extend: 'copy', text: 'Copiar' },
                { extend: 'excel', text: 'Exportar a Excel' },
                { extend: 'pdf', text: 'Exportar a PDF' },
                { extend: 'print', text: 'Imprimir' },
                {
                    extend: 'colvis',
                    text: '<i class="columns icon"></i> Columnas',
                    collectionLayout: 'fixed two-column'
                }
            ]

        });

        // Cargar datos
        fetch("/PORTAL-COBROS/public/reportes-resumen-promesas")
            .then(r => r.json())
            .then(srcData => {
                const labels = srcData.map(d => d.Gestor);

                // totales por gestor
                const totales = srcData.map(d => parseInt(d.Cumplidas) + parseInt(d.Incumplidas));
                const cumplidasPct = srcData.map((d, i) => ((d.Cumplidas / totales[i]) * 100) || 0);
                const incumplidasPct = srcData.map((d, i) => ((d.Incumplidas / totales[i]) * 100) || 0);

                const totalCumplidas = srcData.reduce((a, d) => a + parseInt(d.Cumplidas), 0);
                const totalIncumplidas = srcData.reduce((a, d) => a + parseInt(d.Incumplidas), 0);

                // =========================
                // Gráfico por gestor (barras 100% apiladas)
                // =========================

                const chartData = {
                    labels: srcData.map(d => d.Gestor),
                    datasets: [
                        {
                            label: "Cumplidas",
                            data: cumplidasPct,
                            backgroundColor: "rgba(102, 187, 106, 0.7)", // verde pastel
                            borderColor: "rgba(27, 94, 32, 1)",          // borde verde fuerte
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: "Incumplidas",
                            data: incumplidasPct,
                            backgroundColor: "rgba(239, 83, 80, 0.7)",  // rojo pastel
                            borderColor: "rgba(183, 28, 28, 1)",        // borde rojo fuerte
                            borderWidth: 1,
                            borderRadius: 6
                        }
                    ]
                };
                console.log(chartData);
                new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                                beginAtZero: true,
                                max: 100,
                                ticks: { callback: v => v + "%" }
                            },
                            y: { stacked: true }
                        },
                        plugins: {
                            legend: {
                                labels: { font: { size: 14 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        const key = ctx.dataset.label;
                                        return `${key}: (${srcData[ctx.dataIndex][key]} promesas) (${ctx.raw.toFixed(1)})%`;
                                    }
                                }
                            }
                        },
                        onClick: (evt, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const gestor = labels[index];
                                cargarDetalle(gestor);
                            }
                        }
                    }
                });

                // =========================
                // Gráfico global (dona) clicable
                // =========================
                chartGlobal = new Chart(ctxGlobal, {
                    type: 'doughnut',
                    data: {
                        labels: ["Cumplidas", "Incumplidas"],
                        datasets: [{
                            data: [totalCumplidas, totalIncumplidas],
                            backgroundColor: [
                                "rgba(102, 187, 106, 0.7)",
                                "rgba(239, 83, 80, 0.7)"
                            ],
                            borderColor: [
                                "rgba(27, 94, 32, 1)",
                                "rgba(183, 28, 28, 1)"
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.raw;
                                        const percent = ((value / total) * 100).toFixed(1) + "%";
                                        return `${context.label}: ${value} (${percent})`;
                                    }
                                }
                            }
                        },
                        onClick: (evt, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const estado = chartGlobal.data.labels[index];
                                cargarDetalleGlobal(estado);
                            }
                        }
                    }
                });
            });
    });

    // 🔹 Cargar detalle de un gestor seleccionado
    function cargarDetalle(gestor) {
        document.getElementById("titulo-detalle").innerText = "Detalle de Promesas - " + gestor;

        fetch("/PORTAL-COBROS/public/reportes-detalle-promesas?gestor=" + encodeURIComponent(gestor))
            .then(r => r.json())
            .then(data => {
                tablaDetalle.clear().rows.add(data).draw();
            });
    }

    // 🔹 Cargar detalle global por estado (Cumplidas / Incumplidas)
    function cargarDetalleGlobal(estado) {
        document.getElementById("titulo-detalle").innerText = "Detalle de Promesas - " + estado;

        fetch("/PORTAL-COBROS/public/reportes-detalle-promesas?estado=" + encodeURIComponent(estado))
            .then(r => r.json())
            .then(data => {
                tablaDetalle.clear().rows.add(data).draw();
            });
    }
</script>