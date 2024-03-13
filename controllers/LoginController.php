<?php

namespace Controllers;

use Classes\Email;
use Model\User;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new User($_POST);
            $alertas = $auth->validateLogin();
            
            if(empty($alertas)) {
                // Verificar que el usuario existe
                $usuario = User::where('email', $auth->email);
                
                if(!$usuario || !$usuario->verified) {
                    User::setAlerta('error', 'El usuario no existe o no está confirmado');
                } else {
                    // El usuario existe
                    if(password_verify($_POST['password'], $usuario->password) && $usuario->admin == 0){
                        // Iniciar sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['name'] = $usuario->name;
                        $_SESSION['last_name'] = $usuario->last_name;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['image'] = $usuario->image;
                        $_SESSION['login'] = true;
                        $_SESSION['admin'] = false;

                        
                        //Redireccionar
                        header('Location: /dashboard');
                    }elseif(password_verify($_POST['password'], $usuario->password) && $usuario->admin == 1){
                        // Iniciar sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['name'] = $usuario->name;
                        $_SESSION['last_name'] = $usuario->last_name;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['image'] = $usuario->image;
                        $_SESSION['login'] = true;
                        $_SESSION['admin'] = true;
                        
                        //Redireccionar
                        header('Location: /admin');
                    } else {
                        User::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }
        $alertas = User::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas

        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function create(Router $router)
    {
        $alertas = [];
        $user = new User;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->sincronizar($_POST);
            $alertas = $user->validateNewCount();
            if (empty($alertas)) {
                $existeUsuario = User::where('email', $user->email);
                if ($existeUsuario) {
                    User::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = User::getAlertas();
                }else{
                    // Hashear el password
                    $user->hashPassword();

                    // Eliminar el campo del password repetido
                    unset($user->repeatPassword);

                    // Generar el token
                    $user->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $user->guardar();

                    //Enviar email
                    $email = new Email($user->email, $user->name, $user->last_name, $user->token);
                    $email->enviarConfirmacion();
                    if($resultado) {
                        header('Location: /message');
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/create', [
            'titulo' => 'Crear cuenta',
            'user' => $user,
            'alertas' => $alertas

        ]);
    }

    public static function forget(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new User($_POST);
            $alertas = $usuario->validateEmail();

            if(empty($alertas)) {
                //Buscar el usuario
                $usuario = User::where('email', $usuario->email);
                //debuguear($usuario);
                if($usuario && $usuario->verified) {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->repeatPassword);
                    // Actualizar el usuario
                    $usuario->guardar();
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->name,$usuario->last_name, $usuario->token);
                    $email->enviarInstrucciones();
                    // Generar alerta
                    User::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    User::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }
        }

        $alertas = User::getAlertas();
        // Render a la vista
        $router->render('auth/forget', [
            'titulo' => 'Recuperar contraseña',
            'alertas' => $alertas

        ]);
    }

    public static function reset(Router $router)
    {
        $token = s($_GET['token']);
        $showForm = true;
        if(!$token) header('Location: /');

        // Identificar usuario
        $usuario = User::where('token', $token);
        
        if(empty($usuario)) {
            User::setAlerta('error', 'Token no válido');
            $showForm = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir el nuevo password
            $usuario->sincronizar($_POST);
            // Validar el password
            $alertas = $usuario->validatePassword();

            if(empty($alertas)) {
                // Hashear el nuevo password
                $usuario->hashPassword();
                //Eliminar el token
                $usuario->token = null;
                //Eliminar el password repetido
                unset($usuario->repeatPassword);
                //Guardar el usuario en la BD
                $resultado = $usuario->guardar();
                //Redireccionar al usuario al login
                if($resultado) header('Location: /');
            }
        }

        $alertas = User::getAlertas();
        // Render a la vista
        $router->render('auth/reset', [
            'titulo' => 'Resetear contraseña',
            'alertas' => $alertas,
            'showForm' => $showForm

        ]);
    }

    public static function message(Router $router)
    {
        // Render a la vista
        $router->render('auth/message', [
            'titulo' => 'Cuenta creada con éxito'

        ]);
    }

    public static function confirm(Router $router)
    {

        $token = s($_GET['token']);

        if(!$token){
            header('location: /');
        }

        //Encontrar al usuario de este token
        $usuario = User::where('token', $token);

        if(empty($usuario)) {
            User::setAlerta('error', 'Token no válido');
        } else {
            // Confirmar la cuenta
            $usuario->verified = 1;
            $usuario->token = null;
            unset($usuario->repeatPassword);
            // Guardar en la BD
            $usuario->guardar();

            User::setAlerta('exito', 'Cuenta verificada con éxito');
        }

        $alertas = User::getAlertas();
        // Render a la vista
        $router->render('auth/confirm', [
            'titulo' => 'Confirmar cuenta',
            'alertas' => $alertas

        ]);
    }
}
