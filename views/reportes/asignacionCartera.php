<div class="contenedor">
    <br>
    <h1 class="titulo-pagina"><?= htmlspecialchars($titulo) ?></h1>

    <!-- Filtros -->
    <form method="GET" class="contenedor-95 filtros">
        <div class="contenido-detalle">
            <div class="campo">
                <label for="nombregestor">Gestor</label>
                <input type="text" name="nombregestor" id="nombregestor" placeholder="Ej: Juan Pérez"
                    value="<?= htmlspecialchars($_GET['nombregestor'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="segmento">Segmento</label>
                <input type="text" name="segmento" id="segmento" placeholder="Ej: Oro"
                    value="<?= htmlspecialchars($_GET['segmento'] ?? '') ?>">
            </div>
            <div class="campo">
                <button type="submit" class="boton-submit">Filtrar</button>
            </div>

        </div>
    </form>

    <!-- Tabla -->
    <div class="tabla-contenedor">
        <table id="tabla-cartera" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PreNúmero</th>
                    <th>Usuario Cobros</th>
                    <th>Nombre Gestor</th>
                    <th>Meta</th>
                    <th>Segmento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamoXGestor as $p): ?>
                    <?php
                    // Aplico filtros si existen
                    if (!empty($_GET['nombregestor']) && stripos($p->nombregestor, $_GET['nombregestor']) === false) {
                        continue;
                    }
                    if (!empty($_GET['segmento']) && stripos($p->segmento, $_GET['segmento']) === false) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p->id) ?></td>
                        <td><?= htmlspecialchars($p->prenumero) ?></td>
                        <td><?= htmlspecialchars($p->usuarioCobros) ?></td>
                        <td><?= htmlspecialchars($p->nombregestor) ?></td>
                        <td><?= htmlspecialchars($p->meta) ?></td>
                        <td><?= htmlspecialchars($p->segmento) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Estilos DataTable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabla-cartera').DataTable({
            pageLength: 25,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_",
                paginate: {
                    previous: "Anterior",
                    next: "Siguiente"
                }
            }
        });
    });
</script>