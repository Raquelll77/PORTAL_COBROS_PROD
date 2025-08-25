<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear el archivo de plantilla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Plantilla Créditos");

// Encabezados
$sheet->setCellValue('A1', 'prenumero');
$sheet->setCellValue('B1', 'usuarioCobros');
$sheet->setCellValue('C1', 'nombregestor');

// Fila de ejemplo opcional (puedes eliminarla si no quieres datos de muestra)
$sheet->setCellValue('A2', '01011001018781');
$sheet->setCellValue('B2', 'CC0005');
$sheet->setCellValue('C2', 'JUDIT');

// Encabezados en negrita
$sheet->getStyle('A1:C1')->getFont()->setBold(true);

// Preparar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="plantilla_creditos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
