<?php

namespace Clases;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class generarExcel
{
    private $spreadsheet;
    private $sheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Establece los encabezados para el archivo Excel
     *
     * @param array $encabezados Array asociativo con clave como celda (A1, B1, ...) y valor como texto del encabezado
     */
    public function setEncabezados(array $encabezados)
    {
        foreach ($encabezados as $celda => $texto) {
            $this->sheet->setCellValue($celda, $texto);
        }
    }

    /**
     * Agrega los datos al archivo Excel
     *
     * @param array $datos Array de objetos o arrays que contienen los datos a agregar
     * @param array $columnas Array de columnas ordenadas para asociar con las claves de los datos
     */
    public function addDatos(array $datos, array $columnas)
    {
        $fila = 2; // Comienza después de los encabezados
        foreach ($datos as $dato) {
            $columna = 'A'; // Comienza desde la columna A
            foreach ($columnas as $clave) {
                $this->sheet->setCellValue("{$columna}{$fila}", $dato->$clave ?? 'N/A');
                $columna++;
            }
            $fila++;
        }
    }

    /**
     * Descarga el archivo Excel generado
     *
     * @param string $nombreArchivo Nombre del archivo a descargar
     */
    public function descargar(string $nombreArchivo)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
