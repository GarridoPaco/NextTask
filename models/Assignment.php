<?php

namespace Model;

/**
 * Clase que representa las asignaciones de tareas a usuarios en un proyecto.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'assignments'.
 */
class Assignment extends ActiveRecord
{
    protected static $tabla = 'assignments';
    protected static $columnasDB = ['task_id', 'user_id', 'deadline'];
    public $user_id;
    public $task_id;
    public $deadline;
    public $project_id;

    /**
     * Constructor de la clase Assignment.
     *
     * @param array $args Los argumentos para inicializar la asignación.
     */
    public function __construct($args = [])
    {
        $this->user_id = $args['user_id'] ?? null;
        $this->task_id = $args['task_id'] ?? null;
        $this->deadline = $args['deadline'] ?? null;
        $this->project_id = $args['project_id'] ?? null;
    }

    /**
     * Obtiene todas las asignaciones de tareas para un proyecto específico.
     *
     * @param int $project_id El ID del proyecto.
     * @return mixed|array|bool Los resultados de la consulta o false si hay un error.
     */
    public static function getAssignment($project_id)
    {
        $query = "SELECT assignments.*, project_id 
        FROM assignments LEFT OUTER JOIN tasks ON assignments.task_id=tasks.id WHERE project_id = '$project_id'";
        $resultado = parent::SQL($query);
        return $resultado;
    }

    /**
     * Elimina una asignación de tarea.
     *
     * @return bool True si la asignación se eliminó correctamente, false en caso contrario.
     */
    public function deleteAssignment()
    {
        $query = "DELETE FROM "  . static::$tabla .
            " WHERE user_id = " . parent::$db->escape_string($this->user_id) .
            " AND " . "task_id = " . parent::$db->escape_string($this->task_id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
    
    /**
     * Busca una asignación de tarea específica.
     *
     * @return mixed|bool El resultado de la consulta o false si hay un error.
     */
    public function findAssignment()
    {
        $query = "SELECT * FROM "  . static::$tabla .
            " WHERE user_id = " . parent::$db->escape_string($this->user_id) .
            " AND " . "task_id = " . parent::$db->escape_string($this->task_id) . " LIMIT 1";
        $resultado = parent::$db->query($query);
        return $resultado;
    }
}
