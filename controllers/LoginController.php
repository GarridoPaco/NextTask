<?php

namespace Controllers;

use Classes\Email;
use Model\User;
use MVC\Router;

/**
 * Clase controladora para la gestión del logueado de usuarios
 */
class LoginController
{

    /**
     * Maneja el proceso de inicio de sesión de un usuario.
     * 
     * Si se envía una solicitud POST, se valida el formulario de inicio de sesión.
     * Si la validación es exitosa, se verifica la existencia del usuario y se comprueba la contraseña.
     * Si las credenciales son correctas y el usuario no es un administrador, se inicia sesión y se redirige al dashboard del usuario.
     * Si las credenciales son correctas y el usuario es un administrador, se inicia sesión y se redirige al panel de administración.
     * Si hay errores en la validación o las credenciales son incorrectas, se muestra un mensaje de error.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function login(Router $router)
    {
        // Inicializar arreglo para alertas
        $alertas = [];

        // Verificar si se está realizando una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear instancia de usuario con los datos recibidos
            $auth = new User($_POST);
            // Validar los datos del formulario de inicio de sesión
            $alertas = $auth->validateLogin();

            // Si no hay errores en la validación
            if (empty($alertas)) {
                // Buscar al usuario por su correo electrónico
                $usuario = User::where('email', $auth->email);

                // Si el usuario no existe o no está verificado
                if (!$usuario || !$usuario->verified) {
                    User::setAlerta('error', 'El usuario no existe o no está confirmado');
                } else {
                    // Si el usuario existe y está verificado
                    // Verificar la contraseña
                    if (password_verify($_POST['password'], $usuario->password) && $usuario->admin == 0) {
                        // Iniciar sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['name'] = $usuario->name;
                        $_SESSION['last_name'] = $usuario->last_name;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['image'] = $usuario->image;
                        $_SESSION['login'] = true;
                        $_SESSION['admin'] = false;


                        //Redireccionar al dashboard del usuario
                        header('Location: /dashboard');
                    } elseif (password_verify($_POST['password'], $usuario->password) && $usuario->admin == 1) {
                        // Iniciar sesión del usuario administrador
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['name'] = $usuario->name;
                        $_SESSION['last_name'] = $usuario->last_name;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['image'] = $usuario->image;
                        $_SESSION['login'] = true;
                        $_SESSION['admin'] = true;

                        //Redireccionar al panel de administración
                        header('Location: /admin');
                    } else {
                        // Contraseña incorrecta
                        User::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }
        // Obtener las alertas existentes
        $alertas = User::getAlertas();

        // Renderizar la vista de inicio de sesión
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas

        ]);
    }

    /**
     * Cierra la sesión del usuario y redirige a la página de inicio.
     * 
     * Elimina todas las variables de sesión y luego redirige al usuario a la página de inicio.
     * 
     * @return void
     */
    public static function logout()
    {
        // Iniciar sesión
        session_start();
        // Limpiar todas las variables de sesión
        $_SESSION = [];
        // Redirigir a la página de inicio
        header('Location: /');
    }

    /**
     * Procesa la creación de una nueva cuenta de usuario.
     *
     * Si se envía una solicitud POST, valida los datos de entrada del formulario de registro.
     * Si los datos son válidos, crea un nuevo usuario en la base de datos y envía un correo electrónico de confirmación.
     * Si la creación de la cuenta es exitosa, redirige al usuario a una página de confirmación.
     * Si hay errores en la validación de datos o el usuario ya está registrado, muestra los mensajes de error correspondientes.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function create(Router $router)
    {
        $alertas = [];
        $user = new User;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizar datos del usuario con los datos recibidos del formulario
            $user->sincronizar($_POST);

            // Validar los datos de entrada del formulario
            $alertas = $user->validateNewCount();

            if (empty($alertas)) {
                // Verificar si el usuario ya está registrado
                $existeUsuario = User::where('email', $user->email);

                if ($existeUsuario) {
                    // Si el usuario ya está registrado, mostrar un mensaje de error
                    User::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = User::getAlertas();
                } else {
                    // Hashear la contraseña antes de guardarla en la base de datos
                    $user->hashPassword();

                    // Eliminar el campo de contraseña repetida para evitar guardarla accidentalmente
                    unset($user->repeatPassword);

                    // Generar un token para la confirmación de la cuenta
                    $user->crearToken();

                    // Guardar el nuevo usuario en la base de datos
                    $resultado = $user->guardar();

                    // Enviar un correo electrónico de confirmación al usuario
                    $email = new Email($user->email, $user->name, $user->last_name, $user->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        // Redirigir al usuario a una página de confirmación después de que se haya creado la cuenta
                        header('Location: /message');
                    }
                }
            }
        }

        // Renderizar la vista del formulario de registro
        $router->render('auth/create', [
            'titulo' => 'Crear cuenta',
            'user' => $user,
            'alertas' => $alertas

        ]);
    }

    /**
     * Procesa la solicitud para restablecer la contraseña de un usuario.
     *
     * Si se envía una solicitud POST, valida la dirección de correo electrónico proporcionada.
     * Si la dirección de correo electrónico es válida y corresponde a un usuario verificado,
     * se genera un nuevo token de restablecimiento de contraseña, se actualiza el usuario en la base de datos
     * y se envía un correo electrónico con instrucciones para restablecer la contraseña.
     * Se muestran mensajes de éxito o error según el resultado de la operación.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function forget(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear una instancia del usuario con los datos del formulario
            $usuario = new User($_POST);

            // Validar la dirección de correo electrónico proporcionada
            $alertas = $usuario->validateEmail();

            if (empty($alertas)) {
                // Buscar el usuario en la base de datos por su dirección de correo electrónico
                $usuario = User::where('email', $usuario->email);

                if ($usuario && $usuario->verified) {
                    // Generar un nuevo token de restablecimiento de contraseña
                    $usuario->crearToken();
                    // Eliminar el campo de repetición de contraseña para evitar guardarlo accidentalmente
                    unset($usuario->repeatPassword);
                    // Actualizar los datos del usuario en la base de datos
                    $usuario->guardar();
                    // Enviar un correo electrónico al usuario con instrucciones para restablecer la contraseña
                    $email = new Email($usuario->email, $usuario->name, $usuario->last_name, $usuario->token);
                    $email->enviarInstrucciones();
                    // Mostrar un mensaje de éxito al usuario
                    User::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    // Mostrar un mensaje de error si el usuario no existe o no está verificado
                    User::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }
        }

        // Obtener las alertas almacenadas y renderizar la vista de recuperación de contraseña
        $alertas = User::getAlertas();
        $router->render('auth/forget', [
            'titulo' => 'Recuperar contraseña',
            'alertas' => $alertas
        ]);
    }

    /**
     * Procesa la solicitud para restablecer la contraseña de un usuario mediante un token de restablecimiento.
     *
     * Si se accede a través de una solicitud GET con un token válido en la URL, se muestra el formulario
     * de restablecimiento de contraseña al usuario para que ingrese su nueva contraseña.
     * Si el token no es válido o no se proporciona, se redirige al usuario a la página de inicio.
     * Si se envía una solicitud POST desde el formulario de restablecimiento de contraseña,
     * se sincronizan los datos del formulario con el usuario correspondiente y se valida la nueva contraseña.
     * Si la contraseña es válida, se actualiza en la base de datos, se elimina el token de restablecimiento
     * y se redirige al usuario a la página de inicio.
     * Se muestran mensajes de error o éxito según el resultado de la operación.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function reset(Router $router)
    {
        // Escapar y satinizar el token recibido
        $token = s($_GET['token']);
        // Variable para indicar en la vista si se muestra el formulario para resetear la contraseña
        $showForm = true;

        // Redireccionar al usuario a la página de inicio si no se proporciona un token válido
        if (!$token) header('Location: /');

        // Identificar al usuario utilizando el token de restablecimiento
        $usuario = User::where('token', $token);

        if (empty($usuario)) {
            // Mostrar un mensaje de error si el token no es válido
            User::setAlerta('error', 'Token no válido');
            $showForm = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizar los datos del formulario con el usuario correspondiente
            $usuario->sincronizar($_POST);
            // Validar la nueva contraseña ingresada por el usuario
            $alertas = $usuario->validatePassword();

            if (empty($alertas)) {
                // Hashear la nueva contraseña antes de guardarla en la base de datos
                $usuario->hashPassword();
                // Eliminar el token de restablecimiento ya que la contraseña se ha actualizado
                $usuario->token = null;
                // Eliminar el campo de repetición de contraseña para evitar guardarlo accidentalmente
                unset($usuario->repeatPassword);
                // Guardar los cambios en la base de datos
                $resultado = $usuario->guardar();
                // Redireccionar al usuario a la página de inicio después de restablecer la contraseña
                if ($resultado) header('Location: /');
            }
        }

        // Obtener las alertas almacenadas y renderizar la vista de restablecimiento de contraseña
        $alertas = User::getAlertas();
        $router->render('auth/reset', [
            'titulo' => 'Resetear contraseña',
            'alertas' => $alertas,
            'showForm' => $showForm

        ]);
    }

    /**
     * Renderiza la vista de mensaje de éxito después de crear una cuenta.
     *
     * Esta función muestra un mensaje de éxito después de que el usuario haya creado una cuenta
     * con éxito. No procesa ninguna solicitud ni realiza operaciones adicionales.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function message(Router $router)
    {
        // Renderizar la vista de mensaje con el título apropiado
        $router->render('auth/message', [
            'titulo' => 'Cuenta creada con éxito'

        ]);
    }

    /**
     * Procesa la confirmación de la cuenta de usuario a través de un token.
     *
     * Esta función se encarga de procesar la confirmación de la cuenta de usuario
     * utilizando un token proporcionado en la URL. Verifica la validez del token
     * y, si es válido, marca la cuenta como verificada en la base de datos.
     * Después de procesar la confirmación, muestra un mensaje de éxito o error.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function confirm(Router $router)
    {
        // Escapar y satinizar el token recibido
        $token = s($_GET['token']);

        if (!$token) {
            header('location: /');
        }

        //Encontrar al usuario de este token
        $usuario = User::where('token', $token);

        if (empty($usuario)) {
            // Si el token no es válido, establecer una alerta de error
            User::setAlerta('error', 'Token no válido');
        } else {
            // Confirmar la cuenta del usuario
            $usuario->verified = 1;
            $usuario->token = null;
            unset($usuario->repeatPassword);
            // Guardar los cambios en la base de datos
            $usuario->guardar();

            // Establecer una alerta de éxito
            User::setAlerta('exito', 'Cuenta verificada con éxito');
        }

        $alertas = User::getAlertas();
        // Renderizar la vista de confirmación con el título y las alertas apropiadas
        $router->render('auth/confirm', [
            'titulo' => 'Confirmar cuenta',
            'alertas' => $alertas

        ]);
    }
}
