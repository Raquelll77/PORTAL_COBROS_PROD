<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="titulo-pagina text-center">Dashboard de Promesas Mensual</h1>
        <button id="descargarExcel" class="ui button green">
            <i class="file excel icon"></i>Descargar Excel
        </button>
        <button id="descargarPDF" class="ui button red">
            <i class="file pdf icon"></i>Descargar PDF
        </button>

        <div style="width:95%; margin:auto;">
            <canvas id="graficoPromesas"></canvas>
        </div>

        <h2 class="text-center" style="margin-top:30px;">Cumplidas vs Incumplidas (Global)</h2>
        <div style="width:400px; height:400px; margin:auto;">
            <canvas id="graficoGlobal"></canvas>
        </div>

        <!-- Detalle -->
        <h2 id="titulo-detalle" style="margin-top:30px;">Detalle de Promesas</h2>
        <div class="tabla-contenedor mt-3">

            <table id="tablaDetalle" class="ui celled striped table" style="width:100%">
                <thead>
                    <tr>
                        <th>Prenumero</th>
                        <th>Fecha Creación</th>
                        <th>Fecha Promesa</th>
                        <th>Monto Promesa</th>
                        <th>Total Pagado</th>
                        <th>Estado</th>
                        <th>Nombre Gestor</th>
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

        tablaDetalle = $("#tablaDetalle").DataTable({
            dom: 'Bfrtip',
            columns: [
                { data: "prenumero" },
                { data: "fecha_creacion" },
                { data: "fecha_promesa" },
                { data: "montoPromesa" },
                { data: "TotalPagado" },
                { data: "estado_promesa" },
                { data: "nombregestor", defaultContent: "" }
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
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros en total)",
                search: "Buscar:",
                paginate: {
                    previous: "Anterior",
                    next: "Siguiente"
                }
            },

            createdRow: function (row, data) {
                const href = "<?= BASE_URL ?>/prestamos/detalle"
                    + "?prenumero=" + encodeURIComponent(data.prenumero || "")
                    + "&serie=" + encodeURIComponent(data.serie || "")

                $(row)
                    .addClass('clickable-row')
                    .attr('data-href', href);
            }

        });

        $('#tablaDetalle tbody').on('click', 'tr.clickable-row', function () {
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        });

        // Cargar datos
        const srcData = <?= $dataJson ?>;
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
                    title: {
                        display: true,
                        text: "Porcentaje de Cumplimiento de Promesas por Gestor",
                        font: { size: 16, weight: "bold" }
                    },
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

    function mostrarLoader(show) {
        if (show) {
            $("#loader-detalle").addClass("active");
        } else {
            $("#loader-detalle").removeClass("active");
        }
    }



    // Cargar detalle de un gestor seleccionado
    function cargarDetalle(gestor) {
        document.getElementById("titulo-detalle").innerText = "Detalle de Promesas - " + gestor;
        mostrarLoader(true);

        fetch("/PORTAL-COBROS/public/reportes-detalle-promesas?gestor=" + encodeURIComponent(gestor))
            .then(r => r.json())
            .then(data => {
                tablaDetalle.clear().rows.add(data).draw();
            })
            .finally(() => {
                mostrarLoader(false);
            });
    }

    // Cargar detalle global por estado (Cumplidas / Incumplidas)
    function cargarDetalleGlobal(estado) {
        const estadoSQL = estado.toUpperCase().slice(0, -1);
        document.getElementById("titulo-detalle").innerText = "Detalle de Promesas - " + estado;

        mostrarLoader(true);

        fetch("/PORTAL-COBROS/public/reportes-detalle-promesas?estado=" + encodeURIComponent(estadoSQL))
            .then(r => r.json())
            .then(data => {
                tablaDetalle.clear().rows.add(data).draw();
            })
            .finally(() => {
                mostrarLoader(false);
            });
    }

    // Botón Excel -> descarga TODO
    document.getElementById("descargarExcel").addEventListener("click", function () {
        mostrarLoader(true);
        fetch("/PORTAL-COBROS/public/reportes-descargar-promesas")
            .then(r => r.json())
            .then(data => {
                const header = [
                    "Prenumero",
                    "Numero contactado",
                    "Fecha Creación",
                    "Fecha Promesa",
                    "Codigo resultado",
                    "Monto Promesa",
                    "Total Pagado",
                    "Estado",
                    "Nombre Gestor"
                ];

                const rows = data.map(d => [
                    d.prenumero || "",
                    d.numero_contactado || "",
                    d.fecha_creacion || "",
                    d.fecha_promesa || "",
                    d.codigo_resultado || "",
                    parseFloat(d.montoPromesa) || 0,   // número
                    parseFloat(d.TotalPagado) || 0,
                    d.estado_promesa || "",
                    d.creado_por || ""
                ]);

                const worksheet = XLSX.utils.aoa_to_sheet([header, ...rows]);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Promesas");

                XLSX.writeFile(workbook, "reporte_promesas.xlsx");
            })
            .finally(() => {
                mostrarLoader(false);
            });
    });


    // Botón PDF -> descarga TODO
    document.getElementById("descargarPDF").addEventListener("click", function () {
        mostrarLoader(true);
        fetch("/PORTAL-COBROS/public/reportes-descargar-promesas")
            .then(r => r.json())
            .then(data => {
                const header = [
                    "Prenumero",
                    "Numero contactado",
                    "Fecha Creación",
                    "Fecha Promesa",
                    "Codigo resultado",
                    "Monto Promesa",
                    "Total Pagado",
                    "Estado",
                    "Nombre Gestor"
                ];

                const body = [header];
                data.forEach(d => {
                    body.push([
                        d.prenumero || "",
                        d.numero_contactado || "",
                        d.fecha_creacion || "",
                        d.fecha_promesa || "",
                        d.codigo_resultado || "",
                        parseFloat(d.montoPromesa) || 0,   // número
                        parseFloat(d.TotalPagado) || 0,
                        d.estado_promesa || "",
                        d.creado_por || ""
                    ]);
                });

                const docDefinition = {
                    pageOrientation: 'landscape',
                    content: [
                        { text: "Reporte Detalle de Promesas", style: 'header', alignment: 'center' },
                        { text: new Date().toLocaleString(), alignment: 'right', margin: [0, 0, 0, 10] },
                        {
                            table: {
                                headerRows: 1,
                                widths: ["auto", "auto", "auto", "auto", "auto", "auto", "auto", "auto", "*"],
                                body: body
                            },
                            layout: 'lightHorizontalLines'
                        }
                    ],
                    styles: {
                        header: {
                            fontSize: 16,
                            bold: true,
                            margin: [0, 0, 0, 10]
                        }
                    }
                };

                pdfMake.createPdf(docDefinition).download("reporte_promesas.pdf");
            }).finally(() => {
                mostrarLoader(false);
            });
    });


</script>