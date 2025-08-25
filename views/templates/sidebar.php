<?php include_once __DIR__ . '/../../includes/menu.php'; ?>

<aside class="sidebar">
    <a href="<?= BASE_URL ?>/principal">
        <h1>SKG</h1>
    </a>
    <nav class="sidebar-nav">
        <?php if (!empty($opcionesMostrar) && is_array($opcionesMostrar)): ?>
            <?php foreach ($opcionesMostrar as $opcion): ?>
                <?php
                // URL completa con BASE_URL
                $rutaCompleta = BASE_URL . $opcion['ruta'];

                // Verifica si la URL actual contiene la ruta del menú
                $activeClass = (strpos($_SERVER['REQUEST_URI'], $opcion['ruta']) !== false) ? 'active' : '';
                ?>
                <a href="<?= htmlspecialchars($rutaCompleta) ?>" class="<?= $activeClass ?>">
                    <i class="<?= htmlspecialchars($opcion['icono']) ?>" style="visibility: visible;"></i>
                    <?= htmlspecialchars($opcion['texto']) ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes accesos asignados.</p>
        <?php endif; ?>
    </nav>
</aside>
