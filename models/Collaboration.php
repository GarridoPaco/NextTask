<?php

namespace Model;

/**
 * La clase Collaboration representa la relación entre un usuario y un proyecto en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'collaborations'.
 */
class Collaboration extends ActiveRecord
{
    // Nombre de la tabla en la base de datos
    protected static $tabla = 'collaborations';
    // Columnas de la tabla en la base de datos
    protected static $columnasDB = ['project_id', 'user_id'];
    // ID del usuario
    public $user_id;
    // ID del proyecto
    public $project_id;

    /**
     * Constructor de la clase Collaboration.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
    public function __construct($args = [])
    {
        $this->user_id = $args['user_id'] ?? null;
        $this->project_id = $args['project_id'] ?? null;
    }

    /**
     * Elimina la relación de colaboración entre un usuario y un proyecto.
     *
     * @return bool Devuelve true si la eliminación se realiza con éxito, de lo contrario, false.
     */
    public function deleteCollaboration()
    {
        // Construcción de la consulta SQL para eliminar la colaboración
        $query = "DELETE FROM "  . static::$tabla .
            " WHERE user_id = " . parent::$db->escape_string($this->user_id) .
            " AND " . "project_id = " . parent::$db->escape_string($this->project_id) . " LIMIT 1";
        // Ejecución de la consulta SQL y retorno del resultado
        $resultado = parent::$db->query($query);
        return $resultado;
    }
}
