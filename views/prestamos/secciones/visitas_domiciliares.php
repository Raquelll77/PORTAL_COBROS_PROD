<div class="tab-content" id="registrar-visita">
    <h2>Registrar Visita</h2>

    <form id="form-visita" enctype="multipart/form-data">
        <input type="hidden" name="prenumero" value="<?= htmlspecialchars($_GET['prenumero']) ?>">

        <div class="campo">
            <label>Dirección exacta visitada:</label>
            <textarea name="direccion_visitada" required></textarea>
        </div>

        <div class="campo">
            <label>Fecha de visita:</label>
            <input type="date" name="fecha_visita" max="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="campo">
            <label>Foto de Google Maps:</label>
            <input type="file" name="foto_maps" accept="image/*" required>
        </div>

        <div class="campo">
            <label>Foto del lugar:</label>
            <input type="file" name="foto_lugar" accept="image/*" required>
        </div>

        <button type="submit" class="boton-submit">Guardar Visita</button>
    </form>
</div>

<div class="tab-content" id="historial-visitas">
    <h2>Historial de Visitas</h2>
    <table class="ui celled stackable table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Dirección</th>
                <th>Foto Maps</th>
                <th>Foto Lugar</th>
                <th>Creado por</th>
            </tr>
        </thead>
        <tbody id="historial-visitas-container">
            <?php include __DIR__ . '/historial_visitas.php'; ?>
        </tbody>
    </table>
</div>

<script>
    function cargarHistorialVisitas() {
        const prenumero = document.querySelector('input[name="prenumero"]').value;

        fetch("<?= BASE_URL ?>/prestamos/obtener-historial-visitas?prenumero=" + encodeURIComponent(prenumero))
            .then(res => {
                if (!res.ok) throw new Error("Sesión expirada o error en el servidor");
                return res.text();
            })
            .then(html => {
                document.getElementById('historial-visitas-container').innerHTML = html;
            })
            .catch(err => {
                console.error("Error al cargar historial:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar historial',
                    text: err.message,
                });
            });
    }

    document.getElementById('form-visita').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        Swal.fire({
            title: 'Guardando visita...',
            text: 'Por favor espera mientras se suben las fotos y se guarda la información.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        fetch("<?= BASE_URL ?>/prestamos/guardar-visita", {
            method: 'POST',
            body: data
        })
            .then(async res => {
                const contentType = res.headers.get("content-type") || "";
                if (!contentType.includes("application/json")) {
                    const texto = await res.text();
                    throw new Error("Respuesta no válida del servidor: " + texto);
                }
                return res.json();
            })
            .then(respuesta => {
                if (respuesta.status === 'success') {
                    Swal.fire('✅ Éxito', respuesta.mensaje, 'success');
                    form.reset();
                    cargarHistorialVisitas(); // Recargar tabla
                } else {
                    Swal.fire('⚠️ Error', respuesta.mensaje || 'Ocurrió un problema', 'warning');
                }
            })
            .catch(err => {
                console.error("⛔ Error en fetch:", err);
                Swal.fire('❌ Error', 'Error en la conexión o respuesta inválida', 'error');
            });
    });
</script>
