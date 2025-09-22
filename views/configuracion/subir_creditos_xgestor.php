<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="ui header">Subir creditos por gestor</h1>

        <form id="upload-form" action="<?= BASE_URL ?>/configuracion/subir_creditos" method="post"
            enctype="multipart/form-data" class="ui form">
            <div class="field">
                <label for="file">Selecciona el archivo Excel:</label>
                <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" required>
            </div>

            <button type="submit" class="ui primary button">
                <i class="upload icon"></i>
                Subir
            </button>

            <a href="#" id="btn-ver-estructura" class="ui button">
                <i class="eye icon"></i>
                Ver/Ocultar estructura de la plantilla
            </a>
        </form>
        <div id="estructura-plantilla" class="ui segment" style="margin-top: 20px; display: none;"></div>


    </div>
    <!-- Listado actual -->
    <div class="contenedor-95" style="margin-top:30px;">
        <h2 class="ui header">Cartera asignada actual</h2>

        <table class="ui celled table small" id="tabla-cartera">
            <thead>
                <tr>
                    <th>Prénumero</th>
                    <th>Usuario Cobros</th>
                    <th>Nombre Gestor</th>
                    <th>Meta</th>
                    <th>Segmento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="contenedor-95" style="margin-top:20px; text-align:right;">
            <button id="btn-eliminar-todos" class="ui red button">
                <i class="trash icon"></i>
                Borrar TODOS los créditos
            </button>

            <button id="btn-eliminar-usuario" class="ui orange button">
                <i class="user times icon"></i>
                Borrar TODOS los créditos por usuario
            </button>
        </div>
    </div>
</div>

<!-- Incluye el CSS de SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Incluye el archivo JS de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let opcionesUsuarios = [];
    let opcionesSegmentos = [];

    document.getElementById('upload-form').addEventListener('submit', function () {
        // Mostrar SweetAlert de carga antes de enviar el formulario
        Swal.fire({
            title: 'Subiendo archivo...',
            text: 'Por favor, espera mientras se procesan los datos.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading(); // Mostrar spinner
            }
        });
    });

    // Mostrar mensajes después del procesamiento
    <?php if (!empty($message)): ?>
        Swal.fire({
            icon: '<?= $status === "success" ? "success" : "error" ?>',
            title: '<?= $status === "success" ? "¡Éxito!" : "Error" ?>',
            text: '<?= htmlspecialchars($message) ?>'
        });
    <?php endif; ?>

    document.getElementById('btn-ver-estructura').addEventListener('click', function (e) {
        e.preventDefault();

        const container = document.getElementById('estructura-plantilla');

        if (container.style.display === 'none' || container.style.display === '') {
            const tabla = `
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>prenumero</th>
                        <th>usuarioCobros</th>
                        <th>nombregestor</th>
                        <th>meta</th>
                        <th>segmento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01011001018781</td>
                        <td>CC0004</td>
                        <td>JUDIT</td>
                        <td>10000</td>
                        <td>VIGENTE</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="center aligned">← Escribe los datos aquí</td>
                    </tr>
                </tbody>
            </table>
            <div class="ui message info">
                Esta es la estructura que debe tener el archivo Excel que vas a subir.
            </div>
        `;
            container.innerHTML = tabla;
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });


    document.addEventListener("DOMContentLoaded", () => {
        const table = new DataTable('#tabla-cartera');
        function cargarCreditos() {
            fetch("<?= BASE_URL ?>/configuracion/listar-creditos")
                .then(res => res.json())
                .then(data => {
                    table.clear();
                    data.forEach(item => {
                        table.row.add([
                            item.prenumero,
                            item.usuarioCobros,
                            item.nombregestor,
                            item.meta,
                            item.segmento,
                            `
                        <button class="ui icon button blue btn-edit" data-id="${item.id}">
                            <i class="edit icon"></i>
                        </button>
                        <button class="ui icon button red btn-delete" data-id="${item.id}">
                            <i class="trash icon"></i>
                        </button>
                    `
                        ]);
                    });
                    table.draw();
                });

        }

        cargarOpciones().then(() => {
            cargarCreditos(); // solo después de tener opciones cargadas
        });

        document.addEventListener("click", e => {
            if (e.target.closest(".btn-delete")) {
                const id = e.target.closest(".btn-delete").dataset.id;

                Swal.fire({
                    title: "¿Eliminar?",
                    text: "Esta acción no se puede deshacer",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`<?= BASE_URL ?>/configuracion/eliminar-credito`, {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "id=" + encodeURIComponent(id)
                        })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.success) {
                                    Swal.fire("Eliminado", resp.message, "success");
                                    cargarCreditos(); // recargar la tabla
                                } else {
                                    Swal.fire("Error", resp.message, "error");
                                }
                            });
                    }
                });
            }
        });

        function cargarOpciones() {
            return fetch("<?= BASE_URL ?>/configuracion/opciones-creditos")
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        opcionesUsuarios = data.usuarios;
                        opcionesSegmentos = data.segmentos;
                    } else {
                        console.error("No se pudieron cargar las opciones");
                    }
                })
                .catch(err => console.error("Error cargando opciones:", err));
        }


        //ACTUALIZAR
        document.addEventListener("click", e => {
            if (e.target.closest(".btn-edit")) {
                const fila = e.target.closest("tr");
                const id = e.target.closest(".btn-edit").dataset.id;

                const prenumero = fila.children[0].textContent;
                const usuarioActual = fila.children[1].textContent;
                const nombreActual = fila.children[2].textContent;
                const meta = fila.children[3].textContent;
                const segmentoActual = fila.children[4].textContent;

                // Construir opciones de usuario
                let htmlUsuarios = "";
                opcionesUsuarios.forEach(u => {
                    const selected = u.usuarioCobros === usuarioActual ? "selected" : "";
                    htmlUsuarios += `<option value="${u.usuarioCobros}" data-nombre="${u.nombregestor}" ${selected}>${u.usuarioCobros}</option>`;
                });

                // Construir opciones de segmento
                let htmlSegmentos = "";
                opcionesSegmentos.forEach(seg => {
                    const selected = seg === segmentoActual ? "selected" : "";
                    htmlSegmentos += `<option value="${seg}" ${selected}>${seg}</option>`;
                });

                // Abrir modal
                Swal.fire({
                    title: "Editar crédito",
                    html: `
                <input id="swal-prenumero" class="swal2-input" value="${prenumero}" placeholder="Prénumero" readonly>
                <select id="swal-usuario" class="swal2-input swal2-select">${htmlUsuarios}</select>
                <input id="swal-nombre" class="swal2-input" value="${nombreActual}" placeholder="Nombre Gestor" readonly>
                <input id="swal-meta" class="swal2-input" value="${meta}" placeholder="Meta">
                <select id="swal-segmento" class="swal2-input swal2-select">${htmlSegmentos}</select>
            `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: "Guardar",
                    didOpen: () => {
                        // Cuando cambia el usuario, actualizar automáticamente el nombre
                        const selectUsuario = document.getElementById("swal-usuario");
                        const inputNombre = document.getElementById("swal-nombre");

                        selectUsuario.addEventListener("change", () => {
                            const selectedOption = selectUsuario.options[selectUsuario.selectedIndex];
                            inputNombre.value = selectedOption.dataset.nombre;
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const formData = new URLSearchParams();
                        formData.append("id", id);
                        formData.append("prenumero", document.getElementById("swal-prenumero").value);
                        formData.append("usuarioCobros", document.getElementById("swal-usuario").value);
                        formData.append("nombregestor", document.getElementById("swal-nombre").value);
                        formData.append("meta", document.getElementById("swal-meta").value);
                        formData.append("segmento", document.getElementById("swal-segmento").value);

                        fetch("<?= BASE_URL ?>/configuracion/actualizar-credito", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: formData.toString()
                        })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.success) {
                                    Swal.fire("Actualizado", resp.message, "success");
                                    cargarCreditos();
                                } else {
                                    Swal.fire("Error", resp.message, "error");
                                }
                            });
                    }
                });
            }
        });


        // Eliminar todos los créditos
        document.getElementById("btn-eliminar-todos").addEventListener("click", () => {
            Swal.fire({
                title: "¿Eliminar todos?",
                text: "Se eliminarán TODOS los créditos de la cartera.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar todo",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (result.isConfirmed) {
                    fetch("<?= BASE_URL ?>/configuracion/eliminar-todos-creditos", {
                        method: "POST"
                    })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                Swal.fire("Eliminados", resp.message, "success");
                                cargarCreditos();
                            } else {
                                Swal.fire("Error", resp.message, "error");
                            }
                        });
                }
            });
        });

        // Eliminar créditos por usuario
        document.getElementById("btn-eliminar-usuario").addEventListener("click", () => {
            Swal.fire({
                title: "Eliminar por usuario",
                input: "text",
                inputLabel: "Código del usuario (usuarioCobros)",
                inputPlaceholder: "Ejemplo: CC0004",
                showCancelButton: true,
                confirmButtonText: "Eliminar"
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    const formData = new URLSearchParams();
                    formData.append("usuarioCobros", result.value);

                    fetch("<?= BASE_URL ?>/configuracion/eliminar-por-usuario", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: formData.toString()
                    })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                Swal.fire("Eliminados", resp.message, "success");
                                cargarCreditos();
                            } else {
                                Swal.fire("Error", resp.message, "error");
                            }
                        });
                }
            });
        });


    });



</script>