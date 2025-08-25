<?php
namespace Clases;

use PhpOffice\PhpSpreadsheet\IOFactory;

class Upload
{
    protected $file;
    protected $db;
    protected $progressFile;

    public function __construct($file, $db)
    {
        $this->file = $file;
        $this->db = $db;
        $this->progressFile = sys_get_temp_dir() . '/progress_' . session_id() . '.json';

        if (!$this->db) {
            throw new \Exception('No hay una conexión activa configurada.');
        }
    }

    public function processUpload()
    {
        ini_set('max_execution_time', 0); // Sin límite de tiempo
        set_time_limit(0);

        $filePath = $this->file['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $totalRows = count($rows) - 1; // Excluir encabezado
        $processedRows = 0;

        $batchSize = 500;
        $batch = [];
        $insertedRows = 0;

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue; // Saltar encabezado
            }

            $prenumero = $row['A'] ?? null;
            $usuarioCobros = $row['B'] ?? null;
            $nombreGestor = $row['C'] ?? null;

            if ($prenumero && $usuarioCobros && $nombreGestor) {
                $batch[] = [
                    ':prenumero' => $prenumero,
                    ':usuarioCobros' => $usuarioCobros,
                    ':nombregestor' => $nombreGestor,
                ];
            }

            if (count($batch) >= $batchSize) {
                $insertedRows += $this->insertBatch($batch);
                $batch = [];
            }

            $processedRows++;
        }

        if (!empty($batch)) {
            $insertedRows += $this->insertBatch($batch);
        }

        // Verificar si el archivo existe antes de eliminarlo
        if (file_exists($this->progressFile)) {
            unlink($this->progressFile);
        }

        // Devolver solo el número de filas insertadas
        return $insertedRows;
    }


    protected function insertBatch($batch)
    {
        try {
            $this->db->beginTransaction();

            $query = "
                MERGE prestamosGestor AS target
                USING (VALUES (:prenumero, :usuarioCobros, :nombregestor)) 
                AS source (prenumero, usuarioCobros, nombregestor)
                ON target.prenumero = source.prenumero
                WHEN MATCHED THEN
                    UPDATE SET usuarioCobros = source.usuarioCobros, nombregestor = source.nombregestor
                WHEN NOT MATCHED THEN
                    INSERT (prenumero, usuarioCobros, nombregestor)
                    VALUES (source.prenumero, source.usuarioCobros, source.nombregestor);";

            $stmt = $this->db->prepare($query);

            foreach ($batch as $params) {
                $stmt->execute($params);
            }

            $this->db->commit();
            return count($batch);
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error en la transacción: " . $e->getMessage());
            return 0;
        }
    }
}
