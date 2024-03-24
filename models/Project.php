<?php

namespace Model;

use Model\ActiveRecord;

/**
 * La clase Project representa un proyecto en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'projects'.
 */
class Project extends ActiveRecord
{
    protected static $tabla = 'projects'; // Nombre de la tabla en la base de datos
    protected static $columnasDB = ['id', 'user_id', 'name', 'description', 'deadline', 'url']; // Columnas de la tabla en la base de datos
    public $id; // ID del proyecto
    public $user_id; // ID del usuario asociado al proyecto
    public $name; // Nombre del proyecto
    public $description; // Descripción del proyecto
    public $deadline; // Fecha límite del proyecto
    public $url; // URL del proyecto

    /**
     * Constructor de la clase Project.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->user_id = $args['user_id'] ?? '';
        $this->name = $args['name'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->deadline = $args['deadline'] ?? '';
        $this->url = $args['url'] ?? '';
    }

    /**
     * Valida los datos del proyecto.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validateProject()
    {
        if (!$this->name) {
            self::$alertas['error'][] = 'El nombre del proyecto es obligatorio';
        }
        return self::$alertas;
    }
}
