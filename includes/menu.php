<?php
$menuOpciones = [
    'TELECOBRO' => [
        ['ruta' => '/cobros', 'icono' => 'hand holding usd icon', 'texto' => 'Cobros'],
    ],
    'SUPERVISOR' => [
        ['ruta' => '/reportes', 'icono' => 'file alternate outline icon', 'texto' => 'Reportes'],
        ['ruta' => '/cobros', 'icono' => 'hand holding usd icon', 'texto' => 'Cobros'],
    ],
    'JEFECOBROS' => [
        ['ruta' => '/reportes', 'icono' => 'file alternate outline icon', 'texto' => 'Reportes'],
        ['ruta' => '/cobros', 'icono' => 'hand holding usd icon', 'texto' => 'Cobros'],
        ['ruta' => '/configuracion', 'icono' => 'cogs icon', 'texto' => 'Configuración'],
    ],
    'ADMIN' => [
        ['ruta' => '/reportes', 'icono' => 'file alternate outline icon', 'texto' => 'Reportes'],
        ['ruta' => '/cobros', 'icono' => 'hand holding usd icon', 'texto' => 'Cobros'],
        ['ruta' => '/configuracion', 'icono' => 'cogs icon', 'texto' => 'Configuración'],

    ]
];

// Obtener el rol actual del usuario
$rolUsuario = $_SESSION['PORTAL_COBROS']['rol'] ?? 'INVITADO';

// Determinar qué opciones del menú mostrar según el rol
$opcionesMostrar = $menuOpciones[$rolUsuario] ?? [];
?>