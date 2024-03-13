<?php

namespace Model;

class Comment extends ActiveRecord
{
    protected static $tabla = 'comments';
    protected static $columnasDB = ['id', 'task_id', 'user_id', 'text'];
    public $id;
    public $task_id;
    public $user_id;
    public $text;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->task_id = $args['task_id'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->text = $args['text'] ?? '';
    }

}
