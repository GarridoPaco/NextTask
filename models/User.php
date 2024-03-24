<?php

namespace Model;

/**
 * La clase User representa un usuario en la base de datos.
 * Extiende la clase ActiveRecord para realizar operaciones CRUD en la tabla 'users'.
 */
class User extends ActiveRecord
{
    protected static $tabla = 'users'; // Nombre de la tabla en la base de datos
    protected static $columnasDB = ['id', 'name', 'last_name', 'password', 'image', 'token', 'verified', 'admin', 'email']; // Columnas de la tabla en la base de datos
    public $id; // ID del usuario
    public $name; // Nombre del usuario
    public $last_name; // Apellidos del usuario
    public $password; // Contraseña del usuario
    public $repeatPassword; // Contraseña repetida (usada en validación)
    public $actualPassword; // Contraseña actual (usada en cambio de contraseña)
    public $newPassword; // Nueva contraseña (usada en cambio de contraseña)
    public $repeatNewPassword; // Nueva contraseña repetida (usada en cambio de contraseña)
    public $image; // Imagen del usuario
    public $token; // Token del usuario
    public $verified; // Estado de verificación del usuario (0 o 1)
    public $admin; // Estado de administrador del usuario (0 o 1)
    public $email; // Correo electrónico del usuario

    /**
     * Constructor de la clase User.
     *
     * @param array $args Los argumentos para inicializar los atributos de la instancia.
     */
    public function __construct($args = [])
    {
        // Asignación de valores predeterminados o recibidos a los atributos de la instancia
        $this->id = $args['id'] ?? null;
        $this->name = $args['name'] ?? '';
        $this->last_name = $args['last_name'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->repeatPassword = $args['repeatPassword'] ?? '';
        $this->actualPassword = $args['actualPassword'] ?? '';
        $this->newPassword = $args['newPassword'] ?? '';
        $this->repeatNewPassword = $args['repeatNewPassword'] ?? '';
        $this->image = $args['image'] ?? 'user_default';
        $this->token = $args['token'] ?? '';
        $this->verified = $args['verified'] ?? 0;
        $this->email = $args['email'] ?? '';
    }

    /**
     * Valida los datos de inicio de sesión del usuario.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validateLogin(): array
    {
        // Verifica si el correo electrónico del usuario está vacío
        if (!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        // Verifica si el correo electrónico del usuario es válido
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        // Verifica si la contraseña del usuario está vacía
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }

        return self::$alertas;
    }

    /**
     * Realiza la validación de los datos para la creación de nuevas cuentas de usuario.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validateNewCount(): array
    {
        // Verifica si el nombre del usuario está vacío
        if (!$this->name) {
            self::$alertas['error'][] = 'El nombre de usuario es obligatorio';
        }
        // Verifica si los apellidos del usuario están vacíos
        if (!$this->last_name) {
            self::$alertas['error'][] = 'Los apellidos del usuario son obligatorios';
        }
        // Verifica si el correo electrónico del usuario está vacío
        if (!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        // Verifica si la contraseña del usuario está vacía
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }
        // Verifica si la contraseña tiene al menos 6 caracteres
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }
        // Verifica si las contraseñas ingresadas y repetidas coinciden
        if ($this->password !== $this->repeatPassword) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        // Devuelve el array de alertas con los mensajes de error
        return self::$alertas;
    }

    /**
     * Valida la dirección de correo electrónico.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validateEmail(): array
    {
        // Verifica si el correo electrónico es obligatorio y está vacío
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        // Verifica si el formato del correo electrónico es válido
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        // Devuelve el array de alertas con los mensajes de error
        return self::$alertas;
    }

    /**
     * Valida el campo de contraseña.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validatePassword(): array
    {
        // Verifica si el campo de contraseña está vacío
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }
        // Verifica si la contraseña tiene al menos 6 caracteres
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }
        // Verifica si el campo de repetir contraseña coincide con la contraseña
        if ($this->password !== $this->repeatPassword) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        // Devuelve el array de alertas con los mensajes de error
        return self::$alertas;
    }

    /**
     * Valida los campos de nombre y correo electrónico en el perfil de usuario.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function validateProfile()
    {
        // Verifica si el campo de nombre está vacío
        if (!$this->name) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        // Verifica si el campo de correo electrónico está vacío
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        // Devuelve el array de alertas con los mensajes de error
        return self::$alertas;
    }

    /**
     * Valida los campos de contraseña actual y nueva contraseña.
     *
     * @return array Un array que contiene mensajes de error si existen.
     */
    public function newPassword(): array
    {
        // Verifica si el campo de contraseña actual está vacío
        if (!$this->actualPassword) {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        // Verifica si el campo de nueva contraseña está vacío
        if (!$this->newPassword) {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        // Verifica si la nueva contraseña tiene al menos 6 caracteres
        if (strlen($this->newPassword) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        // Devuelve el array de alertas con los mensajes de error
        return self::$alertas;
    }

    /**
     * Comprueba si la contraseña actual coincide con la contraseña almacenada en la base de datos.
     *
     * @return bool Devuelve true si la contraseña actual coincide con la almacenada, de lo contrario, devuelve false.
     */
    public function checkPassword(): bool
    {
        return password_verify($this->actualPassword, $this->password);
    }

    /**
     * Hashea la contraseña actual y la actualiza en el objeto usuario.
     */
    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    /**
     * Genera un token único y lo asigna al objeto usuario.
     */
    public function crearToken()
    {
        $this->token = uniqid();
    }
}
