<?php

namespace Model;

/**
 * La clase Task representa una tarea en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'tasks'.
 */
class Task extends ActiveRecord
{
    protected static $tabla = 'tasks'; // Nombre de la tabla en la base de datos
    protected static $columnasDB = ['id', 'project_id', 'title', 'description', 'priority', 'status', 'deadline']; // Columnas de la tabla en la base de datos
    public $id; // ID de la tarea
    public $project_id; // ID del proyecto al que pertenece la tarea
    public $title; // Título de la tarea
    public $description; // Descripción de la tarea
    public $priority; // Prioridad de la tarea
    public $status; // Estado de la tarea
    public $deadline; // Fecha límite de la tarea

    /**
     * Constructor de la clase Task.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
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
