

<?php 

foreach ($visitas as $v): ?>
    <tr>
        <td><?= htmlspecialchars($v->fecha_visita) ?></td>
        <td><?= htmlspecialchars($v->direccion_visitada) ?></td>

                <td>
            <a href="<?= BASE_URL . $v->foto_maps ?>" target="_blank">
                <img src="<?= BASE_URL . $v->foto_maps ?>" alt="Foto Maps" style="height: 50px; border-radius: 4px;">
            </a>
        </td>

        <td>
            <a href="<?= BASE_URL . $v->foto_lugar ?>" target="_blank">
                <img src="<?= BASE_URL . $v->foto_lugar ?>" alt="Foto Lugar" style="height: 50px; border-radius: 4px;">
            </a>
        </td>

        <td><?= htmlspecialchars($v->creado_por) ?></td>
    </tr>
<?php endforeach; ?>
