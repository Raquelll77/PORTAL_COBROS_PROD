<?php
include_once __DIR__ . '/../../includes/menu.php';
?>

<h1 class="titulo-pagina">¿Qué desea hacer?</h1>
<main class="contenido">

    <?php if (in_array('/reportes', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="<?= BASE_URL ?>/reportes" class="contenido-seccion">
            <img src="<?= BASE_URL ?>/build/img/reporte-gestiones.png" alt="cartera-reportes">
            <p class="texto-contenido">Reportes</p>
        </a>
    <?php endif; ?>

    <?php if (in_array('/cobros', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="<?= BASE_URL ?>/cobros" class="contenido-seccion">
            <img src="<?= BASE_URL ?>/build/img/reporte-recuperacion.png" alt="cartera-cobro">
            <p class="texto-contenido">Cobros</p>
        </a>
    <?php endif; ?>

    <?php if (in_array('/configuracion', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="<?= BASE_URL ?>/configuracion" class="contenido-seccion">
            <img src="<?= BASE_URL ?>/build/img/reporte-deterioro.png" alt="cartera-gestion">
            <p class="texto-contenido">Configuración</p>
        </a>
    <?php endif; ?>

</main>
