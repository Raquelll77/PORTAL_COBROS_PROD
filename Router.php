<?php

namespace MVC;
use Middlewares\AuthMiddleware;

class Router
{
    public $getRoutes = [];
    public $postRoutes = [];

    public function get($url, $fn, $roles = [])
    {
        $this->getRoutes[$url] = ['fn' => $fn, 'roles' => $roles];
    }

    public function post($url, $fn, $roles = [])
    {
        $this->postRoutes[$url] = ['fn' => $fn, 'roles' => $roles];
    }

    public function comprobarRutas()
    {
        // Obtener URL actual
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $currentUrl = strtok($currentUrl, '?');

        // Quitar el prefijo BASE_URL (definido en app.php)
        $basePath = rtrim(BASE_URL, '/'); // ej: /portal_cobros/public
        if (strpos($currentUrl, $basePath) === 0) {
            $currentUrl = substr($currentUrl, strlen($basePath));
        }

        // Normalizar: si queda vacío → raíz
        if ($currentUrl === '' || $currentUrl === false) {
            $currentUrl = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        // Buscar ruta
        $ruta = $method === 'GET'
            ? ($this->getRoutes[$currentUrl] ?? null)
            : ($this->postRoutes[$currentUrl] ?? null);

        if ($ruta) {
            $fn = $ruta['fn'];
            $rolesPermitidos = $ruta['roles'];

            // Verificar roles si aplica
            if (!empty($rolesPermitidos)) {
                AuthMiddleware::verificarRol($rolesPermitidos);
            }

            // Ejecutar
            if (is_callable($fn)) {
                call_user_func($fn, $this);
            } elseif (is_array($fn) && count($fn) === 2) {
                [$controller, $method] = $fn;
                call_user_func([new $controller, $method], $this);
            } else {
                echo "Error: La ruta no tiene una función válida";
            }
        } else {
            http_response_code(404);
            echo "404 - Página no encontrada";
        }
    }

    public function render($view, $datos = [])
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start();

        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean();

        include_once __DIR__ . '/views/layout.php';
    }
}

