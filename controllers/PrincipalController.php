<?php

namespace Controllers;

use Model\ClientesPrestamos;
use MVC\Router;
use Model\Usuario;

class PrincipalController
{

    public static function principal(Router $router)
    {

        isAuth();

        $router->render('principal/index', [
            'titulo' => 'Principal'
        ]);
    }
    public static function buscarPrestamos(Router $router)
    {
        $prestamos = '';
        /*  $prestamoXGestor = ''; */
        isAuth();

        $prestamoXGestor = ClientesPrestamos::obtenerPrestamosPorGestor($_SESSION['usuario']);



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identidad = $_POST['identidad'] ?? null;
            $nombre = $_POST['nombre'] ?? null;
            $prenumero = $_POST['prenumero'] ?? null;

            $prestamos = ClientesPrestamos::buscarCreditosClientes($identidad, $nombre, $prenumero);

        }

        $router->render('principal/cobros', [
            'titulo' => 'Cobros',
            'prestamos' => $prestamos,
            'prestamoXGestor' => $prestamoXGestor,
            'tab' => $_POST['tab'] ?? $_GET["tab"] ?? 'busqueda-clientes'

        ]);
    }
}
