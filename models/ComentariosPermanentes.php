<?php
namespace Model;

class ComentariosPermanentes extends ActiveRecord
{
    protected static $tabla = 'ComentariosPermanentes';
    protected static $columnasDB = ['id', 'prenumero', 'comentario', 'fecha_creacion', 'ultima_modificacion'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? null;
        $this->comentario = $args['comentario'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
        $this->ultima_modificacion = $args['ultima_modificacion'] ?? null;
    }
}
