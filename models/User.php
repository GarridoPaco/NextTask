<?php

namespace Model;

class User extends ActiveRecord
{
    protected static $tabla = 'users';
    protected static $columnasDB = ['id', 'name', 'last_name', 'password', 'image', 'token', 'verified', 'admin', 'email'];
    public $id;
    public $name;
    public $last_name;
    public $password;
    public $repeatPassword;
    public $actualPassword;
    public $newPassword;
    public $repeatNewPassword;
    public $image;
    public $token;
    public $verified;
    public $admin;
    public $email;

    public function __construct($args = [])
    {
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

    //Validar login de usuario
    public function validateLogin(): array
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }

        return self::$alertas;
    }

    //Validación para cuentas nuevas
    public function validateNewCount(): array
    {
        if (!$this->name) {
            self::$alertas['error'][] = 'El nombre de usuario es obligatorio';
        }
        if (!$this->last_name) {
            self::$alertas['error'][] = 'Los apellidos del usuario son obligatorios';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }
        if ($this->password !== $this->repeatPassword) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        return self::$alertas;
    }

    // Validar un email
    public function validateEmail(): array
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }

    // Validar password
    public function validatePassword(): array
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'El password no puede estar vacío';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }
        if ($this->password !== $this->repeatPassword) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        return self::$alertas;
    }
    
    public function validateProfile() {
        if(!$this->name) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        return self::$alertas;
    }

    public function newPassword(): array
    {
        if (!$this->actualPassword) {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        if (!$this->newPassword) {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        if (strlen($this->newPassword) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Comprobar el password
    public function checkPassword(): bool
    {
        return password_verify($this->actualPassword, $this->password);
    }
    // Hasheas el password
    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar un token
    public function crearToken()
    {
        $this->token = uniqid();
    }
}
