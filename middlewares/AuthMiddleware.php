<?php
namespace Middlewares;


class AuthMiddleware
{
    public static function verificarRol($rolesPermitidos)
    {
        session_start();

        if (!isset($_SESSION['PORTAL_COBROS']['id'])) {
            header('Location: ' . BASE_URL);
            exit();
        }

        // Obtener el rol del usuario desde la sesión
        $rolUsuario = $_SESSION['PORTAL_COBROS']['rol'] ?? null;

        if (!in_array($rolUsuario, $rolesPermitidos)) {
            http_response_code(403); // Prohibido
            die('Acceso denegado. No tienes permisos para ver esta página.');
        }
    }
}
