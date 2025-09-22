<?php
namespace Model;

class PrestamosXGestor extends ActiveRecord
{
    protected static $tabla = 'PrestamosGestor';
    protected static $columnasDB = ['id', 'prenumero', 'usuarioCobros', 'nombregestor', 'meta', 'segmento'];



    public $id;
    public $prenumero;
    public $usuarioCobros;
    public $nombregestor;
    public $meta;
    public $segmento;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? '';
        $this->usuarioCobros = $args['usuarioCobros'] ?? '';
        $this->nombregestor = $args['nombregestor'] ?? '';
        $this->meta = $args['meta'] ?? '';
        $this->segmento = $args['segmento'] ?? '';
    }

    public static function obtenerSegmentos()
    {
        $query = "SELECT DISTINCT segmento FROM " . static::$tabla;
        $resultados = parent::consultarSQL($query);

        $segmentos = [];
        foreach ($resultados as $row) {
            $segmentos[] = $row->segmento;
        }

        return $segmentos;
    }

    // Valores únicos de segmentos
    public static function obtenerUsuarios()
    {
        $query = "SELECT DISTINCT usuarioCobros, nombregestor FROM " . static::$tabla;
        $resultados = parent::consultarSQL($query);

        $usuarios = [];
        foreach ($resultados as $row) {
            $usuarios[] = [
                'usuarioCobros' => $row->usuarioCobros,
                'nombregestor' => $row->nombregestor,
            ];
        }

        return $usuarios;
    }
}

