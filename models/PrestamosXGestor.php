<?php
namespace Model;

class PrestamosXGestor extends ActiveRecord
{
    protected static $tabla = 'prestamosGestor';
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
        $this->nombre = $args['usuarioCobros'] ?? '';
        $this->relacion = $args['nombregestor'] ?? '';
        $this->celular = $args['meta'] ?? '';
        $this->creado_por = $args['segmento'] ?? '';
    }
}
