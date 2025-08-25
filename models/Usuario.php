<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'TBL_USER_COBROS';
    protected static $columnasDB = ['id', 'nombre', 'usuario', 'password', 'rol', 'estado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->usuario = $args['usuario'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->rol = $args['rol'] ?? '';
        $this->estado = $args['estado'] ?? 1;
    }

    //validar el login de usuarios
    public function validarLogin()
    {
        if (!$this->usuario) {
            self::$alertas['error'][] = 'Ingrese el usuario correspondiente';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'Ingrese el password correspondiente';
        }

        return self::$alertas;
    }
}
