<?php

namespace Model;

class CodigosResultado extends ActiveRecord
{
    protected static $tabla = 'codigos_resultado';
    protected static $columnasDB = ['id' . 'codigo', 'positivo'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->codigo = $args['codigo'] ?? '';
        $this->positivo = $args['positivo'] ?? null;
    }

    public static function obtenerPositivos()
    {
        self::useSQLSrv();
        $sql = "SELECT codigo, positivo FROM " . static::$tabla . " WHERE positivo = 1";
        return self::consultarSQL($sql, [], true);
    }
}
