<div class="contenedor">


    <!-- Pestañas -->
    <div class="tabs">
        <button class="tab-button <?= $tab === 'busqueda-clientes' ? 'active' : '' ?>"
            data-tab="busqueda-clientes">Busqueda Clientes</button>
        <button class="tab-button <?= $tab === 'clientes-asignados' ? 'active' : '' ?>"
            data-tab="clientes-asignados">Clientes Asignados</button>



    </div>



    <div class="tab-content <?= $tab === 'busqueda-clientes' ? 'active' : '' ?>" id="busqueda-clientes">

        <h1 class="titulo-pagina">Busqueda de Clientes</h1>

        <form action="" method="POST" class="contenedor-95">
            <input type="hidden" name="tab" id="hidden-tab" value="<?= htmlspecialchars($tab) ?>">
            <div class="contenido">
                <div class="campo">
                    <label for="identidad">Busqueda por identidad</label>
                    <input type="text" maxlength="14" name="identidad" id="identidad" placeholder="ejem: 0403199809081">
                </div>
                <div class="campo">
                    <label for="nombre">Busqueda por nombre</label>
                    <input type="text" maxlength="30" name="nombre" id="nombre"
                        placeholder="ejem: Maria Cristina Gonzales">
                </div>
                <div class="campo">
                    <label for="prenumero">Busqueda por Prestamo</label>
                    <input type="text" maxlength="14" name="prenumero" id="prenumero"
                        placeholder="ejem: 01022000209232">
                </div>
            </div>

            <input class="boton-submit" type="submit" id="buscar" value="Buscar" name="buscar">

        </form>


        <div class="tabla-contenedor">
            <?php if (!empty($prestamos)) { ?>
                <table>
                    <thead>
                        <tr>
                            <?php
                            // Tomamos las claves (nombres de columna) del primer registro
                            $headers = array_keys((array) $prestamos[0]);
                            foreach ($headers as $header) {
                                echo "<th>" . htmlspecialchars($header) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr class="clickable-row" data-href="<?= BASE_URL ?>/prestamos/detalle?
                        prenumero=<?= urlencode($prestamo->PreNumero) ?>&
                        identidad=<?= urlencode($prestamo->ClNumID) ?>&
                        fecha=<?= urlencode($prestamo->PreFecAprobacion) ?>&
                        tab=busqueda-clientes">
                                <?php foreach ((array) $prestamo as $campo => $valor): ?>
                                    <td><?= htmlspecialchars($valor) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No se encontraron resultados.</p>
            <?php } ?>

        </div>

    </div>

    <div class="tab-content <?= $tab === 'clientes-asignados' ? 'active' : '' ?>" id="clientes-asignados">
        <h1 class="text-second">Clientes Asignados</h1>
        <div class="tabla-contenedor">
            <table id="clientes-asignados-table" class="display">
                <thead>
                    <tr>
                        <th>CardCode</th>
                        <th>Nombre</th>
                        <th>Identidad</th>
                        <th>PreNúmero</th>
                        <th>Fecha de Aprobación</th>
                        <th>Estatus</th>
                        <th>Comentario</th>
                        <th>Departamento</th>
                        <th>Municipio</th>
                        <th>Cod Resultado</th>
                        <th>Fecha Revisión</th>
                        <th>Meta</th>
                        <th>Pagos</th>
                        <th>Atraso</th>
                        <th>Cuotas Atraso</th>
                        <th>Fecha de Pago</th>
                        <?php if ($_SESSION['PORTAL_COBROS']['rol'] !== 'TELECOBRO') { ?>
                            <th>Gestor</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Vacío: lo llenará DataTables con AJAX -->
                </tbody>
            </table>
        </div>


        <div id="resumen-segmentos" class="ui three stackable statistics"></div>

    </div>






</div>

<?php include_once 'footer-dashboard.php' ?>

<script src="<?= BASE_URL ?>/build/js/tabs.js"></script>
<script src="<?= BASE_URL ?>/build/js/app.js"></script>



<!-- Incluir DataTables y el script -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<script>
    // Inicializar DataTable
    var tablaAsignados = $('#clientes-asignados-table').DataTable({
        processing: true,
        serverSide: false,
        stateSave: true,
        ajax: {
            url: "<?= BASE_URL ?>/cobros/listar-asignados",
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                return json.data;
            }
        },
        pageLength: 20,
        scrollX: true,
        autoWidth: false,
        deferRender: true,
        columns: [
            { data: "ClReferencia", title: "CardCode", defaultContent: "" },
            { data: "PreNombre", title: "Nombre", defaultContent: "" },
            { data: "ClNumID", title: "Identidad", defaultContent: "" },
            { data: "PreNumero", title: "PreNúmero", defaultContent: "" },
            { data: "PreFecAprobacion", title: "Fecha de Aprobación", defaultContent: "" },
            { data: "segmento", title: "Segmento", defaultContent: "" },
            { data: "PreComentario", title: "Comentario", defaultContent: "" },
            { data: "Departamento", title: "Departamento", defaultContent: "" },
            { data: "Municipio", title: "Municipio", defaultContent: "" },
            { data: "codigo_resultado", title: "Cod Resultado", defaultContent: "" },
            { data: "fecha_revision", title: "Fecha Revisión", defaultContent: "" },
            { data: "meta", title: "Meta", defaultContent: "" },
            { data: "total_pagos_mes_actual", title: "Pagos", defaultContent: "" },
            { data: "MaxDiasAtraso", title: "Atraso", defaultContent: "" },
            { data: "CuotasEnAtraso", title: "Cuotas Atraso", defaultContent: "" },
            { data: "DiaPagoCuota", title: "Fecha de Pago", defaultContent: "" }
            <?php if ($_SESSION['PORTAL_COBROS']['rol'] !== 'TELECOBRO') { ?>,
                { data: "nombregestor", title: "Gestor", defaultContent: "" }
            <?php } ?>

        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="columns icon"></i> Columnas',
                collectionLayout: 'fixed two-column',
                className: 'buttonColumn'
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
                + "?prenumero=" + encodeURIComponent(data.PreNumero || "")
                + "&identidad=" + encodeURIComponent(data.ClNumID || "")
                + "&fecha=" + encodeURIComponent(data.PreFecAprobacion || "")
                + "&tab=clientes-asignados";

            $(row)
                .addClass('clickable-row')
                .attr('data-href', href);
        }
    });

    // 🔹 Forzar que el header se ajuste cuando se muestra el tab
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');
            if (targetTab === "clientes-asignados") {
                setTimeout(() => {
                    tablaAsignados.columns.adjust().draw();
                }, 200); // pequeño delay para que ya esté visible el contenedor
            }
        });
    });

    // 🔹 También al terminar la primera carga
    tablaAsignados.on('init', function () {
        setTimeout(() => {
            tablaAsignados.columns.adjust().draw();
        }, 200);
    });



    // Asignar evento de clic a las filas después de inicializar DataTables
    $('#clientes-asignados-table tbody').on('click', 'tr.clickable-row', function () {
        const href = $(this).data('href');
        if (href) {
            window.location.href = href; // Redirigir al enlace especificado en data-href
        }
    });

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.querySelector('form.contenedor-95');
        if (form) {
            form.addEventListener('submit', function () {
                const activeBtn = document.querySelector('.tab-button.active');
                const activeTab = activeBtn ? activeBtn.dataset.tab : 'busqueda-clientes';
                document.getElementById('hidden-tab').value = activeTab;
            });
        }
        // Cambiar la pestaña activa al hacer clic
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-tab');
                // Actualizar URL sin recargar
                const url = new URL(window.location.href);
                url.searchParams.set('tab', targetTab);
                window.history.replaceState({}, '', url);

                // Cambiar la pestaña activa
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });
    });


    tablaAsignados.on('draw', function () {
        const data = tablaAsignados.rows({ search: 'applied' }).data().toArray();

        const resumen = {};
        data.forEach(row => {
            const seg = row.segmento || 'Sin segmento';
            if (!resumen[seg]) {
                resumen[seg] = { meta: 0, pagos: 0 };
            }
            resumen[seg].meta += parseFloat(row.meta || 0);
            resumen[seg].pagos += parseFloat(row.total_pagos_mes_actual || 0);
        });

        let html = "";
        for (const seg in resumen) {
            const meta = resumen[seg].meta;
            const pagos = resumen[seg].pagos;
            const cumplimiento = meta > 0 ? ((pagos / meta) * 100).toFixed(2) : 0;

            // Color dinámico según cumplimiento
            let colorClass = "red";
            if (cumplimiento >= 80) colorClass = "green";
            else if (cumplimiento >= 50) colorClass = "yellow";

            html += `
            <div class="statistic ${colorClass}">
                <div class="value">${cumplimiento}%</div>
                <div class="label">
                    <strong>${seg}</strong><br>
                    Meta: L ${meta.toLocaleString()}<br>
                    Pagos: L ${pagos.toLocaleString()}
                </div>
            </div>
        `;
        }

        document.getElementById("resumen-segmentos").innerHTML = html;
    });


</script>