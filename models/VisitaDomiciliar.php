<?php
namespace Model;

class VisitaDomiciliar extends ActiveRecord
{
    protected static $tabla = 'visitas_domiciliares';

    protected static $columnasDB = [
        'id',
        'prenumero',
        'direccion_visitada',
        'fecha_visita',
        'foto_maps',
        'foto_lugar',
        'creado_por',

    ];

    public $id;
    public $prenumero;
    public $direccion_visitada;
    public $fecha_visita;
    public $foto_maps;
    public $foto_lugar;
    public $creado_por;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? '';
        $this->direccion_visitada = $args['direccion_visitada'] ?? '';
        $this->fecha_visita = $args['fecha_visita'] ?? '';
        $this->foto_maps = $args['foto_maps'] ?? '';
        $this->foto_lugar = $args['foto_lugar'] ?? '';
        $this->creado_por = $args['creado_por'] ?? '';

    }

}
