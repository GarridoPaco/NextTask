<?php

namespace Model;

class CommentUser extends ActiveRecord
{
    protected static $tabla = 'comments';
    protected static $columnasDB = ['id', 'task_id', 'user_id', 'text', 'Timestamp', 'user_name', 'user_image'];
    public $id;
    public $task_id;
    public $user_id;
    public $text;
    public $Timestamp;
    public $user_name;
    public $user_image;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->task_id = $args['task_id'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->text = $args['text'] ?? '';
        $this->Timestamp = $args['Timestamp'] ?? '';
        $this->user_name = $args['user_name'] ?? '';
        $this->user_image = $args['user_image'] ?? '';
    }

    // Buscar comentarios y datos del usuario por el id de la tarea
    public static function findComments($task_id)
    {
        $query = "SELECT comments.*, CONCAT(users.name, ' ', users.last_name) AS user_name, 
                users.image AS user_image FROM comments LEFT OUTER JOIN users ON comments.user_id=users.id 
                WHERE task_id = ". parent::$db->escape_string($task_id);
        $resultado = parent::SQL($query);
        return $resultado;
    }

}
