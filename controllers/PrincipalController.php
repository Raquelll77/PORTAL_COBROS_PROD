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
        isAuth();

        $prestamos = '';
        $prestamoXGestor = [];

        // Determinar pestaña activa
        $tab = $_POST['tab'] ?? $_GET['tab'] ?? 'busqueda-clientes';

        // Solo cargar clientes asignados si se abre esa pestaña
        if ($tab === 'clientes-asignados') {
            $prestamoXGestor = ClientesPrestamos::obtenerPrestamosPorGestor($_SESSION['PORTAL_COBROS']['usuario']);
        }

        // Si es búsqueda de clientes
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
            'tab' => $tab
        ]);
    }

    public static function listarAsignados()
    {
        isAuth();

        $prestamoXGestor = ClientesPrestamos::obtenerPrestamosPorGestor($_SESSION['PORTAL_COBROS']['usuario']);

        $data = array_map(function ($item) {
            $fila = (array) $item;

            return [
                "ClReferencia" => mb_convert_encoding($fila['ClReferencia'] ?? '', 'UTF-8', 'auto'),
                "PreNombre" => mb_convert_encoding($fila['PreNombre'] ?? '', 'UTF-8', 'auto'),
                "ClNumID" => mb_convert_encoding($fila['ClNumID'] ?? '', 'UTF-8', 'auto'),
                "PreNumero" => mb_convert_encoding($fila['PreNumero'] ?? '', 'UTF-8', 'auto'),
                "PreFecAprobacion" => mb_convert_encoding($fila['PreFecAprobacion'] ?? '', 'UTF-8', 'auto'),
                "segmento" => mb_convert_encoding($fila['segmento'] ?? '', 'UTF-8', 'auto'),
                "PreComentario" => mb_convert_encoding($fila['PreComentario'] ?? '', 'UTF-8', 'auto'),
                "codigo_resultado" => mb_convert_encoding($fila['codigo_resultado'] ?? '', 'UTF-8', 'auto'),
                "fecha_revision" => mb_convert_encoding($fila['fecha_revision'] ?? '', 'UTF-8', 'auto'),
                "meta" => $fila['meta'] ?? 0,
                "total_pagos_mes_actual" => $fila['total_pagos_mes_actual'] ?? 0,
                "MaxDiasAtraso" => $fila['MaxDiasAtraso'] ?? '',
                "CuotasEnAtraso" => $fila['CuotasEnAtraso'] ?? '',
                "DiaPagoCuota" => $fila['DiaPagoCuota'] ?? ''
            ];
        }, $prestamoXGestor);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }



}
