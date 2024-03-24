<?php

namespace Model;

/**
 * La clase CommentUser representa un comentario junto con información del usuario asociado en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'comments'.
 */
class CommentUser extends ActiveRecord
{
    protected static $tabla = 'comments'; // Nombre de la tabla en la base de datos
    protected static $columnasDB = ['id', 'task_id', 'user_id', 'text', 'Timestamp', 'user_name', 'user_image']; // Columnas de la tabla en la base de datos
    public $id; // ID del comentario
    public $task_id; // ID de la tarea asociada al comentario
    public $user_id; // ID del usuario que hizo el comentario
    public $text; // Texto del comentario
    public $Timestamp; // Marca de tiempo del comentario
    public $user_name; // Nombre completo del usuario
    public $user_image; // Imagen del usuario

    /**
     * Constructor de la clase CommentUser.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
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

    /**
     * Busca comentarios y datos del usuario asociado por el ID de la tarea.
     *
     * @param int $task_id El ID de la tarea.
     * @return array|false Un array de objetos CommentUser o false si no se encuentran comentarios.
     */
    public static function findComments($task_id)
    {
        // Consulta SQL para buscar comentarios y datos del usuario asociado
        $query = "SELECT comments.*, CONCAT(users.name, ' ', users.last_name) AS user_name, 
                users.image AS user_image FROM comments LEFT OUTER JOIN users ON comments.user_id=users.id 
                WHERE task_id = " . parent::$db->escape_string($task_id);
        // Ejecutar la consulta SQL
        $resultado = parent::SQL($query);
        // Devolver el resultado de la consulta
        return $resultado;
    }
}
