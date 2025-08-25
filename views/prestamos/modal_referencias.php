<!-- Botón para abrir el modal -->
<button id="btn-agregar-referencia" class="btn-abrir-modal">
    + Nueva Referencia
</button>

<!-- Modal -->
<div id="modal-referencia" class="modal">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <h3>Agregar Nueva Referencia</h3>
        <form id="form-referencia" onsubmit="return false;">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="campo">
                <label>Relación</label>
                <input type="text" name="relacion" required>
            </div>
            <div class="campo">
                <label>Celular</label>
                <input type="tel" name="celular" required>
            </div>
            <div class="acciones">
                <button type="button" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-guardar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="contenido-detalle-3">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Relación</th>
                <th>Celular</th>
            </tr>
        </thead>
        <tbody id="tabla-referencias">
            <?php include dirname(__FILE__) . '/secciones/referencias_agregadas.php'; ?>
        </tbody>
    </table>
</div>



<script>

    // modal

    const btnAbrir = document.getElementById("btn-agregar-referencia");
    const modal = document.getElementById("modal-referencia");
    const btnCerrar = document.querySelector(".cerrar-modal");
    const btnCancelar = document.querySelector(".btn-cancelar");

    btnAbrir.addEventListener("click", () => {
        modal.style.display = "block";
    });

    btnCerrar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    btnCancelar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Cerrar haciendo clic fuera del contenido
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    function cargarReferencias() {
        const prenumero = document.querySelector('input[name="prenumero"]').value;

        fetch("<?= BASE_URL ?>/prestamos/obtener-referencias?prenumero=" + encodeURIComponent(prenumero))
            .then(res => {
                if (!res.ok) throw new Error("Sesión expirada o error en el servidor");
                return res.text();
            })
            .then(html => {
                document.getElementById('tabla-referencias').innerHTML = html;
            })
            .catch(err => {
                console.error("Error al cargar referencias:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar referencias',
                    text: err.message,
                });
            });
    }

    document.getElementById("form-referencia").addEventListener("submit", function (e) {
        e.preventDefault();
        document.getElementById("modal-referencia").style.display = "none";

        const form = e.target;
        const formData = new FormData(form);
        formData.append("prenumero", "<?= htmlspecialchars($_GET['prenumero']) ?>");

        Swal.fire({
            title: 'Guardando referencia...',
            text: 'Por favor espera.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("<?= BASE_URL ?>/prestamos/guardar-referencia", {
            method: "POST",
            body: formData
        })
            .then(async res => {
                const tipo = res.headers.get("content-type") || "";
                if (!tipo.includes("application/json")) {
                    const texto = await res.text();
                    throw new Error("Respuesta no válida: " + texto);
                }
                return res.json();
            })
            .then(respuesta => {
                if (respuesta.status === 'success') {
                    Swal.fire('✅ Éxito', respuesta.mensaje, 'success');
                    form.reset();


                    if (typeof cargarReferencias === "function") {
                        cargarReferencias(); // recargar tabla si existe función
                    }
                } else {
                    Swal.fire('⚠️ Error', respuesta.mensaje || 'Error al guardar', 'warning');
                }
            })
            .catch(err => {
                console.error("⛔ Error en fetch:", err);
                Swal.fire('❌ Error', 'Error de conexión o respuesta inválida', 'error');
            });
    });

</script>