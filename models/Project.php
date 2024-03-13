<?php
namespace Model;

use Model\ActiveRecord;


class Project extends ActiveRecord {
    protected static $tabla = 'projects';
    protected static $columnasDB = ['id', 'user_id', 'name', 'description', 'deadline', 'url'];
    public $id;
    public $user_id;
    public $name;
    public $description;
    public $deadline;
    public $url;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->user_id = $args['user_id'] ?? '';
        $this->name = $args['name'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->deadline = $args['deadline'] ?? '';
        $this->url = $args['url'] ?? '';
    }

    public function validarProyecto()
    {
        if(!$this->name) {
            self::$alertas['error'][] = 'El nombre del proyecto es obligatorio';
        }
        return self::$alertas;
    }
}