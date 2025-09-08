<?php

namespace Model;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ClientesPrestamos extends ActiveRecord
{
    protected static $tabla = 'SIFCO.CrPrestamos';
    protected static $columnasDB = ['ClReferencia', 'PreNombre', 'ClNumID', 'PreNumero', 'PreFecAprobacion', 'PreSalCapital', 'PreComentario', 'SerieChasis'];

    const ClReferencia = 'ClReferencia';

    public function __construct($args = [])
    {
        $this->ClReferencia = $args['ClReferencia'] ?? null;
        $this->PreNombre = $args['PreNombre'] ?? '';
        $this->ClNumID = $args['ClNumID'] ?? '';
        $this->PreNumero = $args['PreNumero'] ?? '';
        $this->PreFecAprobacion = $args['PreFecAprobacion'] ?? '';
        $this->PreSalCapital = $args['PreSalCapital'] ?? '';
        $this->PreComentario = $args['PreComentario'] ?? '';
        $this->SerieChasis = $args['SerieChasis'] ?? '';
    }

    public static function buscarCreditosClientes($dni = null, $nombre = null, $prenumero = null)
    {
        self::useSQLSrv2();

        $sql = "SELECT 
                cc.ClReferencia AS ClReferencia, 
                cp.PreNombre AS PreNombre, 
                cc.ClNumID AS ClNumID, 
                cp.PreNumero AS PreNumero, 
                FORMAT(cp.PreFecAprobacion, 'dd-MM-yyyy') AS PreFecAprobacion,
                CASE WHEN cp.PreSalCapital = 0 THEN 'Cancelado' ELSE 'Vigente' END AS PreSalCapital, 
                cp.PreComentario AS PreComentario,
                serie.CrCalVAlfa AS SerieChasis,  -- 🔹 Última serie
                pg.nombregestor
            FROM " . static::$tabla . " AS cp
            INNER JOIN SIFCO.ClClientes AS cc 
                ON cp.PreCliCod = cc.ClCliCod
            LEFT JOIN [192.168.1.3].MOVESAWEB.dbo.prestamosGestor AS pg 
                ON pg.prenumero = cp.PreNumero
            OUTER APPLY (
                SELECT TOP 1 s.CrCalVAlfa
                FROM SIFCO.CrCalSPLevel1 s
                WHERE s.CrCalNumero = cp.PreNumero
                  AND s.ApCalCod = 16
                ORDER BY s.CrCalCorre DESC   -- 🔹 última serie registrada
            ) serie
            WHERE 1=1";

        $params = [];

        if ($dni) {
            $sql .= " AND cc.ClNumID = :dni";
            $params[':dni'] = $dni;
        }
        if ($nombre) {
            $sql .= " AND cp.PreNombre LIKE :nombre";
            $params[':nombre'] = '%' . $nombre . '%';
        }
        if ($prenumero) {
            $sql .= " AND cp.PreNumero = :prenumero";
            $params[':prenumero'] = $prenumero;
        }

        $sql .= " ORDER BY cp.PreFecAprobacion DESC";

        return self::consultarSQL($sql, $params);
    }

    public static function getInfoClientes($serie = null)
    {
        // Cambiar a la conexión de la base de datos donde se encuentra el procedimiento almacenado
        self::useMySQL();

        // Definir el llamado al procedimiento almacenado con `CALL` y utilizar `?` para los parámetros
        $sql = "CALL sp_BuscarPrestamoSerie(?)";

        // Orden de los parámetros debe coincidir con el procedimiento
        $params = [
            $serie
        ];

        // Llamar a consultarSQL indicando que es un procedimiento almacenado
        return self::consultarSQL($sql, $params, true);
    }


    public static function getSaldoClientes($prenumero)
    {

        self::useSQLSrv2();

        $sql = "EXEC spSaldoCuentaDia @prenumero =  ?";
        $params = [$prenumero];

        return self::consultarSQL($sql, $params, true);
    }

    public static function ObtenerPagosCliente($prenumero)
    {

        self::useSQLSrv2();

        $sql = " EXEC ObtenerPagosCliente @PreNumero = ?";
        $params = [$prenumero];

        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPrestamosPorGestor($usuario)
    {

        self::useSQLSrv();

        $sql = "EXEC spObtenerPrestamosGestor @usuarioCobros = ? ";
        $params = [$usuario];

        return self::consultarSQL($sql, $params, true);

    }

    public static function obtenerPrestamosPorGeneral($usuario)
    {

        self::useSQLSrv();

        $sql = "EXEC spObtenerPrestamosGeneral";
        $params = [$usuario];

        return self::consultarSQL($sql, null, true);

    }

    public static function obtenerPagosGeneral($fechaInicial, $fechaFinal)
    {

        self::useSQLSrv();

        $sql = "EXEC sp_ObtenerPagos @fechaInicial = ? , @fechaFinal = ?";
        $params = [$fechaInicial, $fechaFinal];

        return self::consultarSQL($sql, $params, true);

    }

    public static function obtenerPagosDetalle($fechaInicial, $fechaFinal)
    {

        self::useSQLSrv();

        $sql = "EXEC sp_ObtenerPagosDetalle @fechaInicial = ? , @fechaFinal = ?";
        $params = [$fechaInicial, $fechaFinal];

        return self::consultarSQL($sql, $params, true);

    }

    public static function pagosXGestor($fechaInicial, $fechaFinal, $usuarioGestor)
    {

        self::useSQLSrv();

        $sql = "EXEC sp_ObtenerPagos @fechaInicial = ? , @fechaFinal = ?,  @usuariogestor = ?";
        $params = [$fechaInicial, $fechaFinal, $usuarioGestor];

        return self::consultarSQL($sql, $params, true);

    }

    public static function deterioroCartera()
    {
        self::useSQLSrv2();

        $sql = "EXEC deterioroCartera";
        $params = [];

        $data = self::consultarSQL($sql, $params, true);

        // Inicializar la estructura para agrupar los datos por segmento y deterioro
        $resultados = [
            "Vigente" => ["Sí" => 0, "No" => 0],
            "0-30" => ["Sí" => 0, "No" => 0],
            "31-60" => ["Sí" => 0, "No" => 0],
            "61-90" => ["Sí" => 0, "No" => 0],
            "91-120" => ["Sí" => 0, "No" => 0],
            "+120" => ["Sí" => 0, "No" => 0],
        ];

        // Recorrer los datos obtenidos y agruparlos según el segmento de mora y deterioro
        foreach ($data as $fila) {
            $segmento = $fila["Segmento de Mora Inicio Mes"] ?? "Vigente";
            $deterioro = $fila["Deterioro"] ?? "No";

            // Asegurar que el segmento y el deterioro existan en la estructura
            if (isset($resultados[$segmento]) && isset($resultados[$segmento][$deterioro])) {
                $resultados[$segmento][$deterioro]++;
            }
        }

        // Preparar los datos en el formato requerido por Chart.js
        $labels = array_keys($resultados);
        $datasets = [
            [
                "label" => "Deterioro Sí",
                "backgroundColor" => "rgba(255, 99, 132, 0.5)",
                "borderColor" => "rgba(255, 99, 132, 1)",
                "borderWidth" => 1,
                "data" => array_column($resultados, "Sí"),
            ],
            [
                "label" => "Deterioro No",
                "backgroundColor" => "rgba(54, 162, 235, 0.5)",
                "borderColor" => "rgba(54, 162, 235, 1)",
                "borderWidth" => 1,
                "data" => array_column($resultados, "No"),
            ],
        ];

        return [
            "labels" => $labels,
            "datasets" => $datasets,
        ];

    }

    public static function obtenerDeterioroPorGestorYSegmento()
    {
        self::useSQLSrv2();

        $sql = "EXEC deterioroCartera";
        $params = [];

        $resultados = self::consultarSQL($sql, $params, true);

        // Inicializar un array para almacenar los totales por gestor y segmento
        $totales = [];

        // Recorrer los resultados y agrupar por gestor y segmento
        foreach ($resultados as $fila) {
            $gestor = $fila['Nombre Gestor'];
            $segmento = $fila['Segmento de Mora Inicio Mes'];

            // Inicializar el contador si no existe
            if (!isset($totales[$gestor])) {
                $totales[$gestor] = [];
            }
            if (!isset($totales[$gestor][$segmento])) {
                $totales[$gestor][$segmento] = 0;
            }

            // Incrementar el contador de créditos deteriorados
            if ($fila['Deterioro'] === 'Sí') {
                $totales[$gestor][$segmento]++;
            }
        }

        return $totales;
    }


    public static function generarReporteDeterioroExcel()
    {
        self::useSQLSrv2();

        // Ejecutar el procedimiento almacenado
        $sql = "EXEC deterioroCartera";
        $params = [];
        $datos = self::consultarSQL($sql, $params, true);

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Deterioro');

        // Definir encabezados
        $encabezados = [
            'PreNumero',
            'Nombre Cliente',
            'Saldo Capital',
            'Capital en Atraso',
            'Interes en Atraso',
            'Interes Moratorio',
            'Total en Atraso',
            'Días en Atraso',
            'Segmento de Mora Inicio Mes',
            'Segmento de Mora',
            'Fecha de Último Pago en Atraso',
            'Cuotas en Atraso Inicio Mes',
            'Cuotas en Atraso Actual',
            'Nombre Gestor',
            'Deterioro'
        ];

        // Agregar encabezados a la hoja
        $sheet->fromArray($encabezados, null, 'A1');

        // Agregar datos a la hoja
        $fila = 2;
        foreach ($datos as $dato) {
            $sheet->fromArray(array_values($dato), null, 'A' . $fila);
            $fila++;
        }

        // Crear el escritor y guardar el archivo en una ubicación temporal
        $writer = new Xlsx($spreadsheet);
        $nombreArchivo = 'Reporte_Deterioro_' . date('Ymd_His') . '.xlsx';
        $rutaArchivo = sys_get_temp_dir() . '/' . $nombreArchivo;
        $writer->save($rutaArchivo);

        // Devolver la ruta y nombre del archivo
        return [
            'ruta' => $rutaArchivo,
            'nombre' => $nombreArchivo
        ];
    }

    public static function obtenerEstadoCuenta($prenumero)
    {
        self::useSQLSrv2();
        $sql = "EXEC sp_GetEstadoCuentaSKG @prenumero = ?";
        $params = [$prenumero];
        return self::consultarSQL($sql, $params, /* storedProc */ true); // array de filas
    }

    /** Datos generales del cliente/préstamo (cabecera) */
    public static function obtenerInfoCliente($prenumero)
    {
        self::useSQLSrv2();
        $sql = "EXEC spObtenerdatosClientes @prenumero = ?";
        $params = [$prenumero];
        return self::consultarSQL($sql, $params, true);
    }

    /** Totales al día (mora, capital vencido, total fecha, etc.) */
    public static function obtenerSaldoDia($prenumero)
    {
        self::useSQLSrv2();
        $sql = "EXEC spSaldoCuentaDia_modificado @prenumero = ?";
        $params = [$prenumero];
        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPrestamosGeneral()
    {
        self::useSQLSrv();
        $sql = "EXEC spObtenerPrestamosGeneral";
        return self::consultarSQL($sql, null, true);
    }




}
