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
</div>

<!-- Incluye el CSS de SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Incluye el archivo JS de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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
</script>