<?php

namespace Model;

class ActiveRecord
{
    // Conexiones a bases de datos
    protected static $mysql_db;
    protected static $sqlsrv_db;
    protected static $sqlsrv_db2; // Nueva conexión
    protected static $sqlsrv_db3;
    protected static $active_db;
    protected static $is_sqlsrv = false; // Determina si estamos usando SQL Server (PDO)

    protected static $tabla = '';
    protected static $columnasDB = [];
    protected static $alertas = [];

    // Definir las conexiones
    public static function setMySQLDB($database)
    {
        self::$mysql_db = $database;
        self::$active_db = $database; // MySQL es la conexión activa por defecto
        self::$is_sqlsrv = false;
    }

    public static function setSQLSrvDB($database)
    {
        self::$sqlsrv_db = $database;
    }

    // Nueva función para la tercera conexión a SQL Server
    public static function setSQLSrvDB2($database)
    {
        self::$sqlsrv_db2 = $database;
    }

    public static function setSQLSrvDB3($database)
    {
        self::$sqlsrv_db3 = $database;
    }

    // Cambiar la conexión activa a MySQL
    public static function useMySQL()
    {
        self::$active_db = self::$mysql_db;
        self::$is_sqlsrv = false;
    }

    // Cambiar la conexión activa a la primera conexión de SQL Server
    public static function useSQLSrv()
    {
        self::$active_db = self::$sqlsrv_db;
        self::$is_sqlsrv = true;
    }

    // Cambiar la conexión activa a la segunda conexión de SQL Server
    public static function useSQLSrv2()
    {
        self::$active_db = self::$sqlsrv_db2;
        self::$is_sqlsrv = true;
    }

    public static function useSQLSrv3()
    {
        self::$active_db = self::$sqlsrv_db3;
        self::$is_sqlsrv = true;
    }

    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    public static function getAlertas()
    {
        return static::$alertas;
    }

    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    public function guardar()
    {
        if (!is_null($this->id)) {
            // Si el objeto ya tiene un ID, realiza una actualización
            return $this->actualizar();
        } else {
            // Si no tiene ID, crea un nuevo registro
            $resultado = $this->crear();

            if ($resultado) {
                // Después de crear, recupera el registro para sincronizar atributos
                $nuevoRegistro = static::find($this->id);
                if ($nuevoRegistro) {
                    $this->sincronizar((array) $nuevoRegistro);
                }
            }

            return $resultado;
        }
    }


    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        return self::consultarSQL($query);
    }

    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = :id";
        return self::consultarSQL($query, ['id' => $id])[0] ?? null;
    }
    public static function where($columna, $valor, $opcionesAdicionales = '')
    {
        // Construir la consulta base
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} = :valor " . $opcionesAdicionales;

        // Ejecutar la consulta
        $resultado = self::consultarSQL($query, [':valor' => $valor]);

        // Devolver un único registro (el primero) o null si no hay resultados
        return $resultado[0] ?? null;
    }

    public static function whereAll($columna, $valor, $opcionesAdicionales = '')
    {
        // Construir la consulta base
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} = :valor " . $opcionesAdicionales;

        // Ejecutar la consulta
        $resultado = self::consultarSQL($query, [':valor' => $valor]);

        // Devolver todos los registros (como array de objetos)
        return $resultado;
    }



    public function crear()
    {
        $atributos = $this->sanitizarAtributos();
        $keys = array_keys($atributos);
        $query = "INSERT INTO " . static::$tabla . " (" . join(', ', $keys) . ") VALUES (:" . join(', :', $keys) . ")";

        $resultado = self::executeSQL($query, $atributos);

        if ($resultado) {
            // Obtener el último ID generado
            if (self::$is_sqlsrv) {
                $stmt = self::$active_db->query("SELECT SCOPE_IDENTITY() AS id");
                $this->id = $stmt->fetch(\PDO::FETCH_ASSOC)['id'];
            } else {
                $this->id = self::$active_db->lastInsertId();
            }

            // Sincronizar el objeto con los valores reales desde la base de datos
            $nuevoRegistro = static::find($this->id);
            $this->sincronizar((array) $nuevoRegistro);
        }

        return $resultado;
    }



    public function actualizar()
    {
        $atributos = $this->sanitizarAtributos();
        $valores = [];
        foreach (array_keys($atributos) as $key) {
            $valores[] = "$key = :$key";
        }

        $query = "UPDATE " . static::$tabla . " SET " . join(', ', $valores) . " WHERE id = :id";

        $atributos['id'] = $this->id;
        return self::executeSQL($query, $atributos);
    }

    public function eliminar()
    {
        $query = "DELETE FROM " . static::$tabla . " WHERE id = :id";
        return self::executeSQL($query, ['id' => $this->id]);
    }

    // Eliminar todos los registros de la tabla
    public static function eliminarTodos()
    {
        $query = "DELETE FROM " . static::$tabla;

        if (self::$is_sqlsrv) {
            $stmt = self::$active_db->prepare($query);
            return $stmt->execute();
        } else {
            return self::$active_db->query($query);
        }
    }

    // Eliminar todos los registros de un usuario específico
    public static function eliminarPorUsuario($columnaUsuario, $valor)
    {
        $query = "DELETE FROM " . static::$tabla . " WHERE {$columnaUsuario} = :valor";

        if (self::$is_sqlsrv) {
            $stmt = self::$active_db->prepare($query);
            return $stmt->execute([':valor' => $valor]);
        } else {
            $stmt = self::$active_db->prepare("DELETE FROM " . static::$tabla . " WHERE {$columnaUsuario} = ?");
            $stmt->bind_param("s", $valor);
            return $stmt->execute();
        }
    }


    public static function consultarSQL($query, $params = [], $isStoredProcedure = false)
    {
        if (self::$is_sqlsrv) {
            $stmt = self::$active_db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // Preparar la consulta con mysqli
            $stmt = self::$active_db->prepare($query);

            // Verificar si la preparación fue exitosa
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . self::$active_db->error);
            }

            // Enlazar los parámetros solo si hay y es procedimiento almacenado
            if (!empty($params) && $isStoredProcedure) {
                $stmt->bind_param(str_repeat('s', count($params)), ...array_values($params));
            }

            // Ejecutar la consulta
            $stmt->execute();

            // Obtener los resultados
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Cerrar la consulta
            $stmt->close();
        }

        // Si es un procedimiento almacenado, devuelve un arreglo; de lo contrario, mapea a objetos
        return $isStoredProcedure ? $result : array_map([static::class, 'crearObjeto'], $result);
    }


    protected static function crearObjeto($registro)
    {
        $objeto = new static;
        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            } else {
                // Mapear cualquier columna adicional no definida en el modelo
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna !== 'id' && $columna !== 'fecha_creacion' && $columna !== 'ultima_modificacion') {
                $atributos[$columna] = $this->$columna;
            }
        }
        return $atributos;

    }

    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();
        foreach ($atributos as $key => $value) {
            $atributos[$key] = self::$is_sqlsrv ? $value : self::$active_db->real_escape_string($value);
        }
        return $atributos;
    }

    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    private static function executeSQL($query, $params = [])
    {
        if (self::$is_sqlsrv) {
            $stmt = self::$active_db->prepare($query);
            return $stmt->execute($params);
        } else {
            $stmt = self::$active_db->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bind_param(is_int($val) ? 'i' : 's', $params[$key]);
            }
            return $stmt->execute();
        }
    }
    public static function obtenerPorRangoFechas($columnaFecha, $fechaInicio, $fechaFin)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columnaFecha} BETWEEN :fechaInicio AND :fechaFin";
        $params = [
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin,
        ];

        return self::consultarSQL($query, $params);
    }
    public static function getActiveDB()
    {
        return self::$active_db;
    }


}
