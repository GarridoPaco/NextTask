<?php
namespace Model;
class Task extends ActiveRecord {
    protected static $tabla = 'tasks';
    protected static $columnasDB = ['id', 'project_id', 'title', 'description', 'priority', 'status', 'deadline'];
    public $id;
    public $project_id;
    public $title;
    public $description;
    public $priority;
    public $status;
    public $deadline;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->project_id = $args['project_id'] ?? '';
        $this->title = $args['title'] ?? '';
        $this->description = $args['description'] ?? null;
        $this->priority = $args['priority'] ?? 0;
        $this->status = $args['status'] ?? 0;
        $this->deadline = $args['deadline'] ?? '';
    }
}