<?php

namespace Model;

/**
 * La clase Comment representa un comentario asociado a una tarea en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'comments'.
 */
class Comment extends ActiveRecord
{
    protected static $tabla = 'comments'; // Nombre de la tabla en la base de datos
    protected static $columnasDB = ['id', 'task_id', 'user_id', 'text']; // Columnas de la tabla en la base de datos
    public $id; // ID del comentario
    public $task_id; // ID de la tarea asociada al comentario
    public $user_id; // ID del usuario que hizo el comentario
    public $text; // Texto del comentario

    /**
     * Constructor de la clase Comment.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null; // Asignación del ID del comentario o null si no se proporciona
        $this->task_id = $args['task_id'] ?? ''; // Asignación del ID de la tarea o cadena vacía si no se proporciona
        $this->user_id = $args['user_id'] ?? ''; // Asignación del ID del usuario o cadena vacía si no se proporciona
        $this->text = $args['text'] ?? ''; // Asignación del texto del comentario o cadena vacía si no se proporciona
    }
}

