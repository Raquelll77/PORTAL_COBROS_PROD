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

        <form id="form-busqueda" class="contenedor-95">
            <input type="hidden" name="tab" id="hidden-tab" value="busqueda-clientes">
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
            <input class="boton-submit" type="submit" id="buscar" value="Buscar">
        </form>

        <div class="tabla-contenedor">
            <table id="tabla-busqueda" class="display">
                <thead>
                    <tr id="tabla-busqueda-head"></tr>
                </thead>
                <tbody id="tabla-busqueda-body"></tbody>
            </table>
        </div>

    </div>

    <div class="tab-content <?= $tab === 'clientes-asignados' ? 'active' : '' ?>" id="clientes-asignados">
        <h1 class="text-second">Clientes Asignados</h1>
        <div class="tabla-contenedor">
            <div class="filtros-contenedor">
                <label>
                    Segmento:
                    <select id="filtro-segmento">
                        <option value="">Todos</option>
                    </select>
                </label>

                <label>
                    Día de Pago:
                    <input type="number" id="filtro-dia" placeholder="ej: 15">
                </label>

                <label>
                    Pagos:
                    <select id="filtro-pagos">
                        <option value="">Todos</option>
                        <option value="con">Con Pagos</option>
                        <option value="sin">Sin Pagos</option>
                    </select>
                </label>
            </div>
            <br>
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
                        <th>Serie</th>
                        <th>Departamento</th>
                        <th>Municipio</th>
                        <th>Cod Resultado</th>
                        <th>Fecha Revisión</th>
                        <th>Fecha Promesa</th>
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





<script>
    let tablaAsignados = null; // aún no inicializado

    // Inicializar DataTable solo cuando abres la pestaña
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');

            // Cambiar pestaña activa
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');

            if (targetTab === "clientes-asignados" && !tablaAsignados) {
                // Aquí inicializamos DataTable por primera vez
                tablaAsignados = $('#clientes-asignados-table').DataTable({
                    processing: true,
                    serverSide: false,
                    stateSave: true,
                    ajax: {
                        url: "<?= BASE_URL ?>/cobros/listar-asignados",
                        dataSrc: function (json) {
                            // console.log("JSON recibido:", json);
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
                        { data: "SerieChasis", title: "Serie", defaultContent: "" },
                        { data: "Departamento", title: "Departamento", defaultContent: "" },
                        { data: "Municipio", title: "Municipio", defaultContent: "" },
                        { data: "codigo_resultado", title: "Cod Resultado", defaultContent: "" },
                        { data: "fecha_revision", title: "Fecha Revisión", defaultContent: "" },
                        { data: "fecha_promesa", title: "Fecha Promesa", defaultContent: "" },
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
                    // buttons: [
                    //     {
                    //         extend: 'colvis',
                    //         text: '<i class="columns icon"></i> Columnas',
                    //         collectionLayout: 'fixed two-column',
                    //         className: 'buttonColumn'
                    //     }
                    // ],

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
                            + "?prenumero=" + encodeURIComponent(data.PreNumero || "")
                            + "&serie=" + encodeURIComponent(data.SerieChasis || "")
                            + "&fecha=" + encodeURIComponent(data.PreFecAprobacion || "")
                            + "&tab=clientes-asignados";

                        $(row)
                            .addClass('clickable-row')
                            .attr('data-href', href);
                    }
                });

                // Resto de listeners que dependen de DataTable
                tablaAsignados.on('xhr', function () {
                    const data = tablaAsignados.ajax.json().data;
                    const segmentos = [...new Set(data.map(item => item.segmento || "Sin segmento"))];
                    const select = $('#filtro-segmento');
                    select.empty().append('<option value="">Todos</option>');
                    segmentos.forEach(seg => {
                        select.append(`<option value="${seg}">${seg}</option>`);
                    });
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    if (settings.nTable.id !== 'clientes-asignados-table') return true;

                    const filtroSegmento = $('#filtro-segmento').val();
                    const filtroDia = $('#filtro-dia').val();
                    const filtroPagos = $('#filtro-pagos').val();

                    const segmento = data[5] || '';
                    const diaPago = data[17] || '';
                    const pagos = parseFloat(data[14]) || 0;

                    if (filtroSegmento && segmento !== filtroSegmento) return false;
                    if (filtroDia && parseInt(diaPago) !== parseInt(filtroDia)) return false;
                    if (filtroPagos === 'con' && pagos <= 0) return false;
                    if (filtroPagos === 'sin' && pagos > 0) return false;

                    return true;
                });

                $('#filtro-segmento, #filtro-dia, #filtro-pagos').on('change keyup', function () {
                    tablaAsignados.draw();
                });

                // Redirigir con clic
                $('#clientes-asignados-table tbody').on('click', 'tr.clickable-row', function () {
                    const href = $(this).data('href');
                    if (href) window.open(href, '_blank');
                });

                // Resumen dinámico
                tablaAsignados.on('draw', function () {
                    const data = tablaAsignados.rows({ search: 'applied' }).data().toArray();
                    const resumen = {};
                    data.forEach(row => {
                        const seg = row.segmento || 'Sin segmento';
                        if (!resumen[seg]) resumen[seg] = { meta: 0, pagos: 0 };
                        resumen[seg].meta += parseFloat(row.meta || 0);
                        resumen[seg].pagos += parseFloat(row.total_pagos_mes_actual || 0);
                    });

                    let html = "";
                    for (const seg in resumen) {
                        const meta = resumen[seg].meta;
                        const pagos = resumen[seg].pagos;
                        const cumplimiento = meta > 0 ? ((pagos / meta) * 100).toFixed(2) : 0;

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
                            </div>`;
                    }

                    document.getElementById("resumen-segmentos").innerHTML = html;
                });

                // Ajustar columnas después de mostrar el tab
                setTimeout(() => {
                    tablaAsignados.columns.adjust().draw();
                }, 200);
            }
        });
    });

    // Mantener tab activo al enviar formulario
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form.contenedor-95');
        if (form) {
            form.addEventListener('submit', function () {
                const activeBtn = document.querySelector('.tab-button.active');
                const activeTab = activeBtn ? activeBtn.dataset.tab : 'busqueda-clientes';
                document.getElementById('hidden-tab').value = activeTab;
            });
        }
    });


    document.getElementById('form-busqueda').addEventListener('submit', function (e) {
        e.preventDefault(); // evita recargar toda la página
        mostrarLoader(true);

        const formData = new FormData(this);

        fetch("<?= BASE_URL ?>/cobros/buscar-prestamos", {
            method: "POST",
            body: formData
        })
            .then(r => r.json())
            .then(data => {
                // console.log("Resultados búsqueda:", data);

                // Construir headers dinámicos
                const headRow = document.getElementById("tabla-busqueda-head");
                const body = document.getElementById("tabla-busqueda-body");
                headRow.innerHTML = "";
                body.innerHTML = "";

                if (data.length > 0) {
                    const headers = Object.keys(data[0]);
                    headers.forEach(h => {
                        headRow.innerHTML += `<th>${h}</th>`;
                    });

                    data.forEach(item => {
                        let row = "<tr class='clickable-row' " +
                            "data-href='<?= BASE_URL ?>/prestamos/detalle" +
                            "?prenumero=" + encodeURIComponent(item.PreNumero ?? "") +
                            "&serie=" + encodeURIComponent(item.SerieChasis ?? "") +
                            "&fecha=" + encodeURIComponent(item.PreFecAprobacion ?? "") +
                            "&tab=busqueda-clientes'>";

                        headers.forEach(h => {
                            row += `<td>${item[h] ?? ""}</td>`;
                        });
                        row += "</tr>";
                        body.innerHTML += row;
                    });
                    // Activar redirección al hacer click
                    document.querySelectorAll("#tabla-busqueda .clickable-row").forEach(tr => {
                        tr.addEventListener("click", function () {
                            const href = this.dataset.href;
                            if (href) window.open(href, "_blank");
                        });
                    });

                } else {
                    body.innerHTML = `<tr><td colspan="10">No se encontraron resultados</td></tr>`;
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                mostrarLoader(false);
            });
    });




</script>