<?php

namespace Model;

class Collaboration extends ActiveRecord
{
    protected static $tabla = 'collaborations';
    protected static $columnasDB = ['project_id', 'user_id'];
    public $user_id;
    public $project_id;

    public function __construct($args = [])
    {
        $this->user_id = $args['user_id'] ?? null;
        $this->project_id = $args['project_id'] ?? null;
    }

    // Eliminar un colaborador
    public function eliminarColaborador()
    {
        $query = "DELETE FROM "  . static::$tabla . 
                " WHERE user_id = " . parent::$db->escape_string($this->user_id) . 
                " AND " . "project_id = " . parent::$db->escape_string($this->project_id) . " LIMIT 1";
        $resultado = parent::$db->query($query);
        return $resultado;
    }
}
