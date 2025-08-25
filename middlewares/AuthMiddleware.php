<?php
namespace Middlewares;


class AuthMiddleware
{
    public static function verificarRol($rolesPermitidos)
    {
        session_start();

        if (!isset($_SESSION['id'])) {
            header('Location: ' . BASE_URL);
            exit();
        }

        // Obtener el rol del usuario desde la sesión
        $rolUsuario = $_SESSION['rol'] ?? null;

        if (!in_array($rolUsuario, $rolesPermitidos)) {
            http_response_code(403); // Prohibido
            die('Acceso denegado. No tienes permisos para ver esta página.');
        }
    }
}
