<?php
$menuOpciones = [
    'TELECOBRO' => [

    ],
    'SUPERVISOR' => [

    ],
    'JEFECOBROS' => [
        ['ruta' => '/configuracion/subir_creditos', 'icono' => 'upload black icon', 'texto' => 'Subir Creditos X Gestor'],
    ],
    'ADMIN' => [
        ['ruta' => '/configuracion/subir_creditos', 'icono' => 'upload black icon', 'texto' => 'Subir Creditos X Gestor'],
        ['ruta' => '/configuracion/usuarios', 'icono' => 'user black icon', 'texto' => 'Usuarios'],
    ]
];

// Obtener el rol actual del usuario
$rolUsuario = $_SESSION['PORTAL_COBROS']['rol'] ?? 'INVITADO';

// Determinar qué opciones del menú mostrar según el rol
$opcionesMostrar = $menuOpciones[$rolUsuario] ?? [];


?>
<main class="contenido">

    <?php foreach ($opcionesMostrar as $opcion): ?>
        <a href="<?= BASE_URL . $opcion['ruta'] ?>" class="contenido-seccion">
            <i class="<?= $opcion['icono'] ?> icon" style="font-size: 8rem; margin-bottom: 1rem;"></i>
            <p class="texto-contenido"><?= $opcion['texto'] ?></p>
        </a>
    <?php endforeach; ?>

</main>