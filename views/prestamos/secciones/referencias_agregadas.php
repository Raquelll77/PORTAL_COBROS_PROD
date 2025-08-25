<?php if (!empty($referencias) && is_array($referencias)) { ?>
    <?php foreach ($referencias as $ref) { ?>
        <tr>
            <td><?= htmlspecialchars($ref->nombre) ?></td>
            <td><?= htmlspecialchars($ref->relacion) ?></td>
            <td><?= htmlspecialchars($ref->celular) ?></td>
        </tr>
    <?php }
    ; ?>
<?php } else { ?>
    <tr>
        <td colspan="3" style="text-align:center;">Sin referencias registradas</td>
    </tr>
<?php } ?>