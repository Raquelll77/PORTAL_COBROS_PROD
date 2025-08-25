<?php
namespace Model;
class ReferenciaPrestamo extends ActiveRecord
{
    protected static $tabla = 'referencias_prestamo';
    protected static $columnasDB = ['id', 'prenumero', 'nombre', 'relacion', 'celular', 'creado_por'];

    public $id;
    public $prenumero;
    public $nombre;
    public $relacion;
    public $celular;
    public $creado_por;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->relacion = $args['relacion'] ?? '';
        $this->celular = $args['celular'] ?? '';
        $this->creado_por = $args['creado_por'] ?? '';
    }
}
