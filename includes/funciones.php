<?php

function debuguear($variable): string
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html): string
{
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth(): void
{
    if (!isset($_SESSION['PORTAL_COBROS']['login'])) {
        // Detectar si es una petición AJAX o fetch()
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        $acceptsJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

        if ($isAjax || $acceptsJson) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'expired',
                'mensaje' => 'Sesión expirada. Por favor inicia sesión de nuevo.'
            ]);
        } else {
            // Usar BASE_URL para redirigir correctamente
            header('Location: ' . BASE_URL . '/');
        }

        exit;
    }
}

function url($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
