<?php

namespace Model;

/**
 * La clase ActiveRecord proporciona funcionalidades básicas para interactuar con la base de datos.
 */
class ActiveRecord
{

    /** @var mixed Identificador del registro en la base de datos */
    protected $id;

    /** @var mixed Conexión a la base de datos */
    protected static $db;

    /** @var string Nombre de la tabla en la base de datos */
    protected static $tabla = '';

    /** @var array Columnas de la tabla en la base de datos */
    protected static $columnasDB = [];

    /** @var array Alertas y mensajes */
    protected static $alertas = [];

    /**
     * Establece la conexión a la base de datos.
     *
     * @param mixed $database Conexión a la base de datos
     */
    public static function setDB($database)
    {
        self::$db = $database;
    }

    /**
     * Agrega una alerta.
     *
     * @param string $tipo    Tipo de alerta
     * @param string $mensaje Mensaje de la alerta
     */
    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    /**
     * Obtiene las alertas.
     *
     * @return array Alertas
     */
    public static function getAlertas()
    {
        return static::$alertas;
    }

    /**
     * Realiza la validación.
     *
     * @return array Alertas
     */
    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    /**
     * Guarda un registro en la base de datos.
     *
     * @return mixed Resultado de la operación
     */
    public function guardar()
    {
        $resultado = '';
        if (!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    /**
     * Obtiene todos los registros de la tabla.
     *
     * @return array Resultado de la consulta
     */
    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    /**
     * Busca un registro por su ID.
     *
     * @param int $id ID del registro
     * @return mixed Registro encontrado
     */
    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla  . " WHERE id = $id";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * Obtiene un número específico de registros.
     *
     * @param int $limite Número máximo de registros a obtener
     * @return mixed Registro encontrado
     */
    public static function get($limite)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT $limite";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * Realiza una búsqueda por columna y valor.
     *
     * @param string $columna Nombre de la columna
     * @param mixed $valor    Valor a buscar en la columna
     * @return mixed Resultado de la búsqueda
     */
    public static function where($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * Realiza una búsqueda de todos los registros que pertenecen a un valor dado en una columna.
     *
     * @param string $columna Nombre de la columna
     * @param mixed $valor    Valor a buscar en la columna
     * @return array Resultado de la búsqueda
     */
    public static function belongsTo($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    /**
     * Ejecuta una consulta SQL avanzada.
     *
     * @param string $consulta Consulta SQL
     * @return mixed Resultado de la consulta
     */
    public static function SQL($consulta)
    {
        $query = $consulta;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    /**
     * Crea un nuevo registro en la base de datos.
     *
     * @return array Resultado de la operación de inserción
     */
    public function crear()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ('";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        // Resultado de la consulta
        $resultado = self::$db->query($query);

        return [
            'resultado' =>  $resultado,
            'id' => self::$db->insert_id
        ];
    }

    /**
     * Actualiza el registro en la base de datos.
     *
     * @return bool Resultado de la operación de actualización
     */
    public function actualizar()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        $resultado = self::$db->query($query);
        return $resultado;
    }

    /**
     * Elimina el registro de la base de datos.
     *
     * @return bool Resultado de la operación de eliminación
     */
    public function eliminar()
    {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

    /**
     * Ejecuta una consulta SQL y devuelve un array de objetos ActiveRecord.
     *
     * @param string $query Consulta SQL
     * @return array Array de objetos ActiveRecord
     */
    public static function consultarSQL($query)
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $array;
    }

    /**
     * Crea un objeto ActiveRecord a partir de un registro de base de datos.
     *
     * @param array $registro Registro de base de datos
     * @return ActiveRecord Objeto ActiveRecord creado
     */
    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }



    /**
     * Obtiene los atributos del objeto ActiveRecord.
     *
     * @return array Atributos del objeto
     */
    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    /**
     * Sanitiza los atributos del objeto ActiveRecord.
     *
     * @return array Atributos sanitizados
     */
    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    /**
     * Sincroniza los atributos del objeto ActiveRecord con los valores proporcionados.
     *
     * @param array $args Valores a sincronizar
     * @return void
     */
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
