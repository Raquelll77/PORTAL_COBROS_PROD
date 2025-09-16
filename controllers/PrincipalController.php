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

    /**
     * Render de la vista principal de Cobros con pestañas.
     */
    public static function buscarPrestamos(Router $router)
    {
        isAuth();

        $prestamos = [];
        $prestamoXGestor = [];

        // Determinar pestaña activa
        $tab = $_POST['tab'] ?? $_GET['tab'] ?? 'busqueda-clientes';

        // Solo si se abre la pestaña "clientes-asignados" traemos esos datos
        if ($tab === 'clientes-asignados') {
            $prestamoXGestor = ClientesPrestamos::obtenerPrestamosPorGestor(
                $_SESSION['PORTAL_COBROS']['usuario']
            );
        }

        // Si estamos en la pestaña de búsqueda
        if ($tab === 'busqueda-clientes') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $identidad = $_POST['identidad'] ?? null;
                $nombre = $_POST['nombre'] ?? null;
                $prenumero = $_POST['prenumero'] ?? null;

                $prestamos = ClientesPrestamos::buscarCreditosClientes($identidad, $nombre, $prenumero);

                // Guardar en sesión solo para esta pestaña
                $_SESSION['PORTAL_COBROS']['ultimos_prestamos'] = $prestamos;
            } else {
                $prestamos = $_SESSION['PORTAL_COBROS']['ultimos_prestamos'] ?? [];
            }
        }

        $router->render('principal/cobros', [
            'titulo' => 'Cobros',
            'prestamos' => $prestamos,
            'prestamoXGestor' => $prestamoXGestor,
            'tab' => $tab
        ]);
    }

    /**
     * Endpoint para DataTables de Clientes Asignados
     */
    public static function listarAsignados()
    {
        isAuth();

        $rol = $_SESSION['PORTAL_COBROS']['rol'] ?? '';
        $usuario = $_SESSION['PORTAL_COBROS']['usuario'] ?? '';

        if ($rol === 'TELECOBRO') {
            $prestamos = ClientesPrestamos::obtenerPrestamosPorGestor($usuario);
        } else {
            $prestamos = ClientesPrestamos::obtenerPrestamosGeneral();
        }

        $data = array_map(function ($item) use ($rol) {
            $fila = (array) $item;

            $base = [
                "ClReferencia" => mb_convert_encoding($fila['ClReferencia'] ?? '', 'UTF-8', 'auto'),
                "PreNombre" => mb_convert_encoding($fila['PreNombre'] ?? '', 'UTF-8', 'auto'),
                "ClNumID" => mb_convert_encoding($fila['ClNumID'] ?? '', 'UTF-8', 'auto'),
                "PreNumero" => mb_convert_encoding($fila['PreNumero'] ?? '', 'UTF-8', 'auto'),
                "PreFecAprobacion" => mb_convert_encoding($fila['PreFecAprobacion'] ?? '', 'UTF-8', 'auto'),
                "segmento" => mb_convert_encoding($fila['segmento'] ?? '', 'UTF-8', 'auto'),
                "PreComentario" => mb_convert_encoding($fila['PreComentario'] ?? '', 'UTF-8', 'auto'),
                "SerieChasis" => mb_convert_encoding($fila['SerieChasis'] ?? '', 'UTF-8', 'auto'),
                "Departamento" => mb_convert_encoding($fila['Departamento'] ?? '', 'UTF-8', 'auto'),
                "Municipio" => mb_convert_encoding($fila['Municipio'] ?? '', 'UTF-8', 'auto'),
                "codigo_resultado" => mb_convert_encoding($fila['codigo_resultado'] ?? '', 'UTF-8', 'auto'),
                "fecha_revision" => mb_convert_encoding($fila['fecha_revision'] ?? '', 'UTF-8', 'auto'),
                "fecha_promesa" => mb_convert_encoding($fila['fecha_promesa'] ?? '', 'UTF-8', 'auto'),
                "meta" => $fila['meta'] ?? 0,
                "total_pagos_mes_actual" => $fila['total_pagos_mes_actual'] ?? 0,
                "MaxDiasAtraso" => $fila['MaxDiasAtraso'] ?? '',
                "CuotasEnAtraso" => $fila['CuotasEnAtraso'] ?? '',
                "DiaPagoCuota" => $fila['DiaPagoCuota'] ?? ''
            ];

            if (isset($fila['nombregestor'])) {
                $base["nombregestor"] = mb_convert_encoding($fila['nombregestor'] ?? '', 'UTF-8', 'auto');
            }

            return $base;
        }, $prestamos);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Endpoint AJAX para búsqueda de clientes (no recarga la vista completa).
     */
    public static function buscarPrestamosAjax()
    {
        isAuth();

        $identidad = $_POST['identidad'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $prenumero = $_POST['prenumero'] ?? null;

        $prestamos = ClientesPrestamos::buscarCreditosClientes($identidad, $nombre, $prenumero);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($prestamos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
