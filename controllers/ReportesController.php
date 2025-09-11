<?php

namespace Controllers;

use Model\ClientesPrestamos;
use Model\PrestamosXGestor;
use Model\Promesas;
use MVC\Router;
use Model\Gestiones;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class ReportesController
{

    public static function index(Router $router)
    {

        isAuth();
        $router->render('reportes/index', [

        ]);
    }
    public static function gestiones(Router $router)
    {

        isAuth();

        // Obtener fecha seleccionada o usar la fecha actual
        $fechaSeleccionada = $_POST['fecha'] ?? date('Y-m-d');
        $fecha = date('Y-m-d H:i:s', strtotime($fechaSeleccionada));

        // Obtener gestiones y procesar datos
        $gestiones = Gestiones::ObtenerGestionesPorFechas($fecha);
        $datosProcesados = Gestiones::procesarDatosGestiones($gestiones);

        // Enviar datos a la vista
        $router->render('reportes/gestiones', [
            'titulo' => 'Reporte Gestiones',
            'fechaSeleccionada' => $fechaSeleccionada,
            'reporteGestionDiaria' => $gestiones,
            'datosProcesados' => $datosProcesados
        ]);
    }

    public static function descargarGestiones(Router $router)
    {

        isAuth();

        $errores = [];

        // Validar las fechas
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        if ($fechaInicio && $fechaFin) {
            try {
                $fechaInicio = (new \DateTime($fechaInicio))->format('Y-m-d\TH:i:s'); // Inicio del día
                $fechaFin = (new \DateTime($fechaFin))->setTime(23, 59, 59)->format('Y-m-d\TH:i:s'); // Fin del día
            } catch (\Exception $e) {
                $errores[] = 'Formato de fecha inválido.';
            }

            if ($fechaInicio > $fechaFin) {
                $errores[] = 'La fecha de inicio no puede ser posterior a la fecha final.';
            }
        } else {
            $errores[] = 'Fechas inválidas.';
        }

        // Consultar las gestiones en el rango de fechas
        Gestiones::useSQLSrv();
        $gestiones = Gestiones::obtenerPorRangoFechas('fecha_creacion', $fechaInicio, $fechaFin);

        if (empty($gestiones)) {
            $errores[] = 'No hay gestiones para el rango de fechas seleccionado.';
            $router->render('reportes/gestiones', [
                'errores' => $errores
            ]);
            return;
        }

        // Generar el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Historial de Gestiones');

        // Encabezados
        $encabezados = [
            'A1' => 'ID',
            'B1' => 'Prenumero',
            'D1' => 'Fecha Creación',
            'E1' => 'Fecha Revisión',
            'F1' => 'Fecha Promesa',
            'G1' => 'Número Contactado',
            'C1' => 'Código Resultado',
            'H1' => 'Comentario',
            'I1' => 'Creado Por',
            'J1' => 'Monto Promesa',
        ];


        foreach ($encabezados as $celda => $texto) {
            $sheet->setCellValue($celda, $texto);
        }

        // Agregar datos
        $fila = 2;
        foreach ($gestiones as $gestion) {
            $sheet->setCellValue("A{$fila}", $gestion->id ?? 'N/A');
            $sheet->setCellValue("B{$fila}", $gestion->prenumero ?? 'N/A');
            $sheet->setCellValue("D{$fila}", $gestion->fecha_creacion ?? 'N/A');
            $sheet->setCellValue("E{$fila}", $gestion->fecha_revision ?? 'N/A');
            $sheet->setCellValue("F{$fila}", $gestion->fecha_promesa ?? 'N/A');
            $sheet->setCellValue("G{$fila}", $gestion->numero_contactado ?? 'N/A');
            $sheet->setCellValue("C{$fila}", $gestion->codigo_resultado ?? 'N/A');
            $sheet->setCellValue("H{$fila}", $gestion->comentario ?? 'N/A');
            $sheet->setCellValue("I{$fila}", $gestion->creado_por ?? 'N/A');
            $sheet->setCellValue("J{$fila}", $gestion->montoPromesa ?? 0);
            $fila++;
        }

        // Enviar archivo al cliente
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="gestiones.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function recuperacion(Router $router)
    {

        isAuth();

        // Obtener el primer día del mes y el día actual
        $primerDiaMes = (new \DateTime('first day of this month'))->format('Y-m-d');
        $diaActual = (new \DateTime())->format('Y-m-d');

        // Consultar los pagos generales entre las fechas especificadas
        $pagosXGestor = ClientesPrestamos::obtenerPagosGeneral($primerDiaMes, $diaActual);

        // Inicializamos un arreglo para agrupar totales por gestor
        $totalesPorGestor = [];
        foreach ($pagosXGestor as $pago) {
            // Verifica si el valor de 'usuarioCobros' es '-' o vacío
            $gestor = $pago['usuarioCobros'];
            if (empty($gestor) || $gestor === '-') {
                $gestor = 'Sin Asignar';
            }

            // Suma los valores agrupados por gestor
            if (!isset($totalesPorGestor[$gestor])) {
                $totalesPorGestor[$gestor] = 0;
            }
            $totalesPorGestor[$gestor] += $pago['CrMoValor'];
        }


        // Convertimos los datos para ser usados en Chart.js
        $gestores = array_keys($totalesPorGestor); // Nombres de los gestores (incluyendo "Sin Asignar")
        $totales = array_values($totalesPorGestor); // Totales recuperados por cada gestor

        // Debugging opcional
        /* debuguear($gestores);
           debuguear($totales); */

        // Renderizamos la vista con los datos preparados
        $router->render('reportes/recuperacion', [
            'titulo' => 'Reportes de Recuperacion',
            'gestores' => $gestores,
            'totales' => $totales
        ]);
    }


    public static function descargarPagos(Router $router)
    {

        isAuth();

        $errores = [];

        // Validar las fechas
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        if ($fechaInicio && $fechaFin) {
            try {
                $fechaInicio = (new \DateTime($fechaInicio))->format('Y-m-d\TH:i:s'); // Inicio del día
                $fechaFin = (new \DateTime($fechaFin))->setTime(23, 59, 59)->format('Y-m-d\TH:i:s'); // Fin del día
            } catch (\Exception $e) {
                $errores[] = 'Formato de fecha inválido.';
            }

            if ($fechaInicio > $fechaFin) {
                $errores[] = 'La fecha de inicio no puede ser posterior a la fecha final.';
            }
        } else {
            $errores[] = 'Fechas inválidas.';
        }

        // Consultar las gestiones en el rango de fechas
        ClientesPrestamos::useSQLSrv();
        $pagos = ClientesPrestamos::obtenerPagosDetalle($fechaInicio, $fechaFin);

        if (empty($pagos)) {
            $errores[] = 'No hay pagos para el rango de fechas seleccionado.';
            $router->render('/reportes/recuperacion', [
                'errores' => $errores
            ]);
            return;
        }

        // Generar el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Historial de pagos');

        // Encabezados
        $encabezados = [
            'A1' => 'Prenumero',
            'B1' => 'Nombre',
            'C1' => 'Pago',
            'D1' => 'Fecha de Pago',
            'E1' => 'Fecha de Aplicacion',
            'F1' => 'Gestor'

        ];


        foreach ($encabezados as $celda => $texto) {
            $sheet->setCellValue($celda, $texto);
        }

        // Agregar datos
        $fila = 2;
        foreach ($pagos as $pago) {
            $sheet->setCellValue("A{$fila}", $pago['PreNumero'] ?? 'N/A');
            $sheet->setCellValue("B{$fila}", $pago['PreNombre'] ?? 'N/A');
            $sheet->setCellValue("C{$fila}", $pago['CrMoValor'] ?? 0);
            $sheet->setCellValue("D{$fila}", $pago['FechaValor'] ?? 'N/A');
            $sheet->setCellValue("E{$fila}", $pago['FechaTransaccion'] ?? 'N/A');
            $sheet->setCellValue("F{$fila}", $pago['usuarioCobros'] ?? 'Sin Asignar');

            $fila++;
        }

        // Enviar archivo al cliente
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReportePagos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function deterioro(Router $router)
    {

        isAuth();

        $datosGrafica = ClientesPrestamos::deterioroCartera();

        // Obtener los datos procesados
        $deterioroDatosGestor = ClientesPrestamos::obtenerDeterioroPorGestorYSegmento();

        // Definir la paleta de colores para cada segmento
        $coloresSegmentos = [
            'Vigente' => 'rgba(0, 0, 255, 0.7)',      // Azul
            '0-30' => 'rgba(0, 128, 255, 0.7)',       // Azul claro
            '31-60' => 'rgba(0, 255, 255, 0.7)',      // Cian
            '61-90' => 'rgba(0, 255, 128, 0.7)',      // Verde claro
            '91-120' => 'rgba(255, 255, 0, 0.7)',     // Amarillo
            '+120' => 'rgba(200, 0, 0, 0.7)',         // Rojo
        ];

        // Preparar los datos para la gráfica
        $gestores = array_keys($deterioroDatosGestor);
        $segmentos = array_keys($coloresSegmentos); // Usar los segmentos definidos en la paleta de colores
        $datasets = [];

        // Crear un dataset para cada segmento
        foreach ($segmentos as $segmento) {
            $data = [];
            foreach ($gestores as $gestor) {
                $data[] = $deterioroDatosGestor[$gestor][$segmento] ?? 0;
            }
            $datasets[] = [
                'label' => $segmento,
                'data' => $data,
                'backgroundColor' => $coloresSegmentos[$segmento],
            ];
        }

        // Renderizar la vista con los datos de la gráfica
        $router->render('reportes/deterioro', [
            'titulo' => 'Reporte de Deterioro',
            'datosGrafica' => $datosGrafica,
            'datosGrafica2' => [
                'labels' => $gestores,
                'datasets' => $datasets,
            ],
        ]);
    }
    /* 
        public static function descargarReporteDeterioro()
        {
            // Verificar autenticación
            session_name("PORTAL-COBROS");session_start();
            isAuth();

            // Generar el reporte
            $reporte = ClientesPrestamos::generarReporteDeterioroExcel();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $reporte['nombre'] . '"');
            header('Content-Length: ' . filesize($reporte['ruta']));

            readfile($reporte['ruta']);

            // Eliminar el archivo temporal
            unlink($reporte['ruta']);

        } */

    public static function descargarReporteDeterioro()
    {
        // Verificar autenticación

        isAuth();

        // Generar el reporte
        $reporte = ClientesPrestamos::generarReporteDeterioroExcel();

        // Verificar si el archivo fue generado correctamente
        if (file_exists($reporte['ruta'])) {
            // Establecer encabezados para la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($reporte['nombre']) . '"');
            header('Content-Length: ' . filesize($reporte['ruta']));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            // Leer el archivo y enviarlo al navegador
            readfile($reporte['ruta']);

            // Eliminar el archivo temporal
            unlink($reporte['ruta']);
            exit;
        } else {
            // Manejar el error si el archivo no existe
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo generar el reporte.']);
        }
    }

    public static function asignacionCartera(Router $router)
    {
        isAuth();

        $asignacion = new PrestamosXGestor();

        PrestamosXGestor::useSQLSrv();

        $prestamosXGestor = PrestamosXGestor::all();
        $prestamosRecuperacion = ClientesPrestamos::obtenerPrestamosGeneral();


        $router->render(
            'reportes/asignacionCartera',
            [
                'titulo' => 'Asignacion Cartera',
                'prestamoXGestor' => $prestamosXGestor,
                'prestamosRecuperacion' => $prestamosRecuperacion
            ]

        );
    }

    public static function vistaPromesas(Router $router)
    {
        isAuth();

        $data = Promesas::obtenerResumen();
        $router->render('reportes/promesas', [
            'titulo' => 'Dashboard Promesas',
            'dataJson' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        ]);
    }


    public static function resumenPromesas(Router $router)
    {
        $data = Promesas::obtenerResumen();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function detallePromesas(Router $router)
    {
        $gestor = $_GET['gestor'] ?? null;
        $estado = $_GET['estado'] ?? null;

        if ($gestor) {
            $data = Promesas::obtenerDetallePorGestor($gestor);
        } elseif ($estado) {
            $data = Promesas::obtenerDetallePorEstado($estado);
        } else {
            $data = [];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }




}