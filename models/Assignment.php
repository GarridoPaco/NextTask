<?php

namespace Model;

class Assignment extends ActiveRecord
{
    protected static $tabla = 'assignments';
    protected static $columnasDB = ['task_id', 'user_id', 'deadline'];
    public $user_id;
    public $task_id;
    public $deadline;
    public $project_id;

    public function __construct($args = [])
    {
        $this->user_id = $args['user_id'] ?? null;
        $this->task_id = $args['task_id'] ?? null;
        $this->deadline = $args['deadline'] ?? null;
        $this->project_id = $args['project_id'] ?? null;
    }

    // Get asignaciones
    public static function getAssingns($project_id)
    {
        $query = "SELECT assignments.*, project_id 
        FROM assignments LEFT OUTER JOIN tasks ON assignments.task_id=tasks.id WHERE project_id = '$project_id'";
        $resultado = parent::SQL($query);
        return $resultado;
    }

    // Eliminar asignación
    public function eliminarAsignacion()
    {
        $query = "DELETE FROM "  . static::$tabla .
            " WHERE user_id = " . parent::$db->escape_string($this->user_id) .
            " AND " . "task_id = " . parent::$db->escape_string($this->task_id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Buscar asignación
    public function buscarAsignacion()
    {
        $query = "SELECT * FROM "  . static::$tabla .
            " WHERE user_id = " . parent::$db->escape_string($this->user_id) .
            " AND " . "task_id = " . parent::$db->escape_string($this->task_id) . " LIMIT 1";
        $resultado = parent::$db->query($query);
        return $resultado;
    }
}
