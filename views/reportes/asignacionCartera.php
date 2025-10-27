<div class="contenedor">
    <br>
    <h1 class="titulo-pagina"><?= htmlspecialchars($titulo) ?></h1>

    <!-- 🔹 Filtros -->
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

    <!-- 🔹 Tabla principal -->
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
                    // Aplicar filtros
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
                        <td><?= number_format($p->meta, 2) ?></td>
                        <td><?= htmlspecialchars($p->segmento) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 🔹 Resumen: Cumplimiento Meta por Segmento -->
    <div class="contenedor-95">
        <h2 class="ui header">Cumplimiento Meta por Segmento</h2>

        <?php
        // Agrupar por gestor
        $gestores = [];
        foreach ($prestamosRecuperacion as $row) {
            $gestor = $row['nombregestor'] ?? 'Sin Gestor';
            $seg = $row['segmento'] ?? 'Sin segmento';

            if (!isset($gestores[$gestor])) {
                $gestores[$gestor] = [
                    'meta' => 0,
                    'pagos' => 0,
                    'segmentos' => []
                ];
            }

            $meta = (float) ($row['meta'] ?? 0);
            $pagos = (float) ($row['total_pagos_mes_actual'] ?? 0);

            $gestores[$gestor]['meta'] += $meta;
            $gestores[$gestor]['pagos'] += $pagos;

            if (!isset($gestores[$gestor]['segmentos'][$seg])) {
                $gestores[$gestor]['segmentos'][$seg] = ['meta' => 0, 'pagos' => 0];
            }

            $gestores[$gestor]['segmentos'][$seg]['meta'] += $meta;
            $gestores[$gestor]['segmentos'][$seg]['pagos'] += $pagos;
        }

        // Calcular y ordenar cumplimiento
        $cumplimientos = [];
        foreach ($gestores as $g => $info) {
            $cumpl = $info['meta'] > 0 ? round(($info['pagos'] / $info['meta']) * 100, 2) : 0;
            $cumplimientos[$g] = $cumpl;
        }
        arsort($cumplimientos);
        ?>

        <!-- 🟩 Grid con 2 columnas -->
        <div class="ui two column grid">
            <?php foreach ($cumplimientos as $gestor => $cumplimientoGestor): ?>
                <?php
                $info = $gestores[$gestor];
                $color = $cumplimientoGestor >= 80 ? "green" : ($cumplimientoGestor >= 50 ? "yellow" : "red");
                ?>
                <div class="column">
                    <div class="ui segment">
                        <h3 class="ui header">
                            <?= htmlspecialchars($gestor) ?>
                            <div class="ui label <?= $color ?>"><?= $cumplimientoGestor ?>%</div>
                        </h3>
                        <h4>
                            <strong>Meta:</strong> L <?= number_format($info['meta'], 2) ?><br>
                            <strong>Pagos:</strong> L <?= number_format($info['pagos'], 2) ?>
                        </h4>

                        <table class="ui celled table small">
                            <thead>
                                <tr>
                                    <th>Segmento</th>
                                    <th>Meta</th>
                                    <th>Pagos</th>
                                    <th>Cumplimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($info['segmentos'] as $seg => $datos): ?>
                                    <?php
                                    $cumpl = $datos['meta'] > 0
                                        ? round(($datos['pagos'] / $datos['meta']) * 100, 2)
                                        : 0;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($seg) ?></td>
                                        <td>L <?= number_format($datos['meta'], 2) ?></td>
                                        <td>L <?= number_format($datos['pagos'], 2) ?></td>
                                        <td><?= $cumpl ?> %</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 🔹 DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabla-cartera').DataTable({
            pageLength: 25,
            order: [[0, 'desc']],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    previous: "Anterior",
                    next: "Siguiente"
                }
            }
        });
    });
</script>