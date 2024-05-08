<?php

namespace Controllers;

use Classes\Email;
use Model\Assignment;
use Model\Project;
use Model\Task;
use Model\User;
use MVC\Router;

/**
 * Clase controladora para el panel de control (dashboard)
 */
class DashboardController
{

    /**
     * Método para la página principal del panel de control.
     * Este método se accede a través de una solicitud HTTP GET 
     * para mostrar el listado de proyectos del usuario con sesión iniciada.
     * Permite agregar nuevos proyectos a través de una soliciutd HTTP POST
     * Se envía a la vista una cadena con el título, 
     * un array con las alertas y otro con los proyectos.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function index(Router $router)
    {
        session_start();
        // Arreglo para almacenar alertas
        $alertas = [];

        // Verificación que el usuario esté identificado
        isAuth();


        $user_id = $_SESSION['id'];

        $projects = Project::belongsTo('user_id', $user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project = new Project($_POST);

            // Validación del proyecto a añadir
            $alertas = $project->validateProject();

            if (empty($alertas)) {

                // Generar una URL única para cada nuevo proyecto
                $project->url = md5(uniqid());

                $project->user_id = $_SESSION['id'];

                // Verificación de que el proyecto se guarda correctamente
                if ($project->guardar()) {
                    $projects = Project::belongsTo('user_id', $user_id);

                    Project::setAlerta('exito', 'Proyecto creado correctamente');
                    $alertas = Project::getAlertas();
                } else {
                    Project::setAlerta('error', 'El proyecto no se ha podido crear');
                    $alertas = Project::getAlertas();
                }
            }
        }
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'alertas' => $alertas,
            'projects' => $projects
        ]);
    }

    /**
     * Maneja las operaciones relacionadas con la administración de usuarios en el panel de control.
     * El usuario tiene que estar autenticado y ser administrador.
     * 
     * Si se accede a través de una solicitud HTTP GET se manda a la vista un listado de 
     * los usuarios que no son administrador.
     * 
     * A través de una soliciutd HTTP POST se eliminan usuarios de la aplicación,
     * se espera que el cliente administrador proporcione como parámetro de la solicitud 
     * el ID del usuario a eliminar.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function admin(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();
        // Verificar si el usuario actual es un administrador
        isAdmin();
        // Arreglo para almacenar alertas
        $alertas = [];

        // Procesar solicitud de eliminación de usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = User::find($_POST['user_id']);

            // Verificación de que el usuario exista
            if (!$user) {
                // Si el usuario no existe, establecer una alerta de error
                User::setAlerta('error', 'El usuario no existe');
                $alertas = User::getAlertas();
            } else {
                // Si el usuario existe, intentar eliminarlo
                if (empty($alertas)) {
                    if ($user->eliminar()) {
                        // Eliminar la foto de perfil asociada al usuario
                        unlink('build/img/profile/' . $user->image . ".jpg");
                        // Si se elimina correctamente, establecer una alerta de éxito
                        User::setAlerta('exito', 'Usuario eliminado correctamente');
                        $alertas = User::getAlertas();
                    } else {

                        // Si no se puede eliminar, establecer una alerta de error
                        User::setAlerta('error', 'El usuario no se ha podido eliminar');
                        $alertas = User::getAlertas();
                    }
                }
            }
        }

        $users = User::belongsTo('admin', '0');

        $router->render('dashboard/admin', [
            'titulo' => 'Usuarios',
            'alertas' => $alertas,
            'users' => $users
        ]);
    }

    /**
     * Maneja las operaciones relacionadas con los proyectos en el panel de control.
     * Permite ver la información detallada de un proyecto y eliminar un proyecto.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Si se accede a través de una solicitud HTTP GET, se muestra la vista con la información
     * detallada del proyecto cuya URL se proporciona como parámetro en la URL.
     * 
     * Si se accede a través de una solicitud HTTP POST, se espera que se proporcione el ID del proyecto
     * a eliminar como parámetro en la solicitud. Una vez recibido el ID, se intenta eliminar el proyecto
     * correspondiente.
     *
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function project(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Arreglo para almacenar alertas
        $alertas = [];

        // Buscar al usuario en la base de datos
        $user_id = $_SESSION['id'];
        $user = User::find($user_id);

        // Procesar solicitud GET para ver la información detallada de un proyecto
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Verificar si se proporciona la URL del proyecto en la solicitud
            if (!isset($_GET['url'])) {
                // Si no se proporciona, redirigir al panel de control
                header('Location: /dashboard');
                exit;
            }
            $url = $_GET['url'];
            // Buscar el proyecto por su URL
            $project = Project::where('url', $url);
            // Verificar si el proyecto existe y pertenece al usuario actual
            if (!$project || $project->user_id !== $user_id) {
                // Si no existe o no pertenece al usuario, redirigir al panel de control
                header('Location: /dashboard');
                exit;
            }
            // Renderizar la vista del proyecto
            $router->render('dashboard/project', [
                'titulo' => 'Vista General: ' . $project->name,
                'perfilImg' => $user->image,
                'project' => $project,
                'alertas' => $alertas
            ]);
        }

        // Procesar solicitud POST para eliminar un proyecto
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si se proporciona el ID del proyecto a eliminar en la solicitud
            if (!isset($_POST['project_id'])) {
                // Si no se proporciona, redirigir al panel de control
                header('Location: /dashboard');
                exit;
            }
            $project = Project::find($_POST['project_id']);
            // Verificación de que el proyecto exista
            if (!$project) {
                // Si el proyecto no existe, establecer una alerta de error
                Project::setAlerta('error', 'El projecto no existe');
                $alertas = Project::getAlertas();
            } elseif ($project->user_id !== $user_id) {
                // Verificar si el proyecto pertenece al usuario actual
                // Si no pertenece, establecer una alerta de error
                Project::setAlerta('error', 'No tienes permisos para eliminar este proyecto');
                $alertas = Project::getAlertas();
            }
            // Si el proyecto existe y pertenece al usuario actual, intentar eliminarlo
            if (empty($alertas)) {

                if ($project->eliminar()) {
                    // Si se elimina correctamente, establecer una alerta de éxito
                    Project::setAlerta('exito', 'Proyecto eliminado correctamente');
                    $alertas = Project::getAlertas();
                } else {
                    // Si no se puede eliminar, establecer una alerta de error
                    Project::setAlerta('error', 'El proyecto no se ha podido eliminar');
                    $alertas = Project::getAlertas();
                }
            }
            // Obtener los proyectos del usuario actual después de la eliminación
            $projects = Project::belongsTo('user_id', $user_id);
            // Renderizar la vista del panel de control de proyectos
            $router->render('dashboard/index', [
                'titulo' => 'Proyectos',
                'perfilImg' => $user->image,
                'projects' => $projects,
                'alertas' => $alertas
            ]);
        }
    }

    /**
     * Maneja la vista de la tabla Kanban para un proyecto específico.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Muestra la tabla Kanban del proyecto cuya URL se proporciona como parámetro en la URL.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function kanban(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Arreglo para almacenar alertas
        $alertas = [];

        // Verificar si se proporciona la URL del proyecto en la solicitud
        if (!isset($_GET['url'])) {
            // Si no se proporciona, redirigir al panel de control
            header('Location: /dashboard');
            exit;
        }

        // Obtener la URL del proyecto de la solicitud
        $url = $_GET['url'];

        // Buscar el proyecto por su URL
        $project = Project::where('url', $url);

        // Verificar si el proyecto existe
        if (!$project) {
            // Si el proyecto no existe, redirigir al dashboard
            header('Location: /dashboard');
            exit;
        }

        // Renderizar la vista de la tabla Kanban del proyecto
        $router->render('dashboard/kanban', [
            'titulo' => 'Vista Kanban: ' . $project->name,
            'project' => $project,
            'alertas' => $alertas
        ]);
    }

    /**
     * Maneja la vista de colaboraciones para el usuario actual.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Muestra las colaboraciones del usuario actual, incluyendo las tareas asignadas.
     * 
     * Se envía a la vista los usuarios y proyectos.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function collaboration(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Obtener el ID del usuario actual
        $user_id = $_SESSION['id'];

        // Arreglo para almacenar las tareas asignadas al usuario
        $tasks = [];

        // Obtener todas las asignaciones del usuario actual
        $assignments = Assignment::belongsTo('user_id', $user_id);

        // Obtener las tareas correspondientes a cada asignación
        foreach ($assignments as $assignment) {
            $tasks[] = Task::where('id', $assignment->task_id);
        }

        // Obtener todos los usuarios y proyectos
        $users = User::all();
        $projects = Project::all();

        // Renderizar la vista de colaboraciones
        $router->render('dashboard/collaboration', [
            'titulo' => 'Colaboraciones',
            'assignments' => $assignments,
            'tasks' => $tasks,
            'users' => $users,
            'projects' => $projects
        ]);
    }

    /**
     * Maneja la vista y la actualización del perfil de usuario.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Si la solicitud HTTP es de tipo POST, actualiza la información del perfil del usuario, 
     * incluyendo la imagen de perfil si se proporciona una nueva.
     * La nueva imagen de perfil es comprimida, recortada con un aspect ratio de 1:1 y 
     * redimensionada a 100x100 píxeles
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function profile(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Arreglo para almacenar alertas
        $alertas = [];

        $usuario = User::find($_SESSION['id']);

        // Procesar solicitud de actualización del perfil
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sincronizar los datos del formulario con el objeto usuario
            $usuario->sincronizar($_POST);

            // Validar los datos enviados del perfil
            $alertas = $usuario->validateProfile();

            if (empty($alertas)) {
                // Crear directorio para imágenes de perfil si no existe
                $imgDir = 'build/img/profile/';
                if (!is_dir($imgDir)) {
                    mkdir($imgDir);
                }

                // Subir la imagen de perfil si se proporciona
                if ($_FILES['image']['name']) {

                    // Eliminar la imagen anterior si es distinta a la imagen por defecto
                    if ($usuario->image !== 'user_default') {
                        unlink('build/img/profile/' . $usuario->image . ".jpg");
                    }

                    $profileImg = $_FILES['image'];

                    // Generar un nombre único para cada imagen
                    $imgName = md5(uniqid(rand(), true));

                    // Ruta completa de la imagen
                    $imgPath = $imgDir . $imgName . ".jpg";

                    // Crear una nueva imagen a partir del archivo subido
                    $imgUploaded = imagecreatefromjpeg($profileImg['tmp_name']);

                    // Dimensiones originales de la imagen subida
                    $originalWidth = imagesx($imgUploaded);
                    $originalHeigth = imagesy($imgUploaded);

                    // Calcular las coordenadas de recorte para mantener el aspect ratio de 1:1
                    if ($originalWidth > $originalHeigth) {
                        $x = ($originalWidth - $originalHeigth) / 2;
                        $y = 0;
                        $widthCut = $originalHeigth;
                        $heigthCut = $originalHeigth;
                    } else {
                        $x = 0;
                        $y = ($originalHeigth - $originalWidth) / 2;
                        $widthCut = $originalWidth;
                        $heigthCut = $originalWidth;
                    }

                    // Crear una nueva imagen con las dimensiones deseadas
                    $newImg = imagecreatetruecolor(100, 100);

                    // Recortar la imagen original y copiarla en la nueva imagen
                    imagecopyresampled($newImg, $imgUploaded, 0, 0, $x, $y, 100, 100, $widthCut, $heigthCut);

                    // Guardar la imagen recortada en el servidor
                    imagejpeg($newImg, $imgPath, 75);

                    // Liberar memoria
                    imagedestroy($imgUploaded);
                    imagedestroy($newImg);

                    // Añadir el nombre de la imagen al objeto usuario
                    $usuario->image = $imgName;
                }

                // Verificar si el nuevo email ya está registrado en otra cuenta
                $existeUsuario = User::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Si el email ya está registrado en otra cuenta, mostrar mensaje de error
                    User::setAlerta('error', 'Email no válido, ya pertenece a otra cuenta');
                    $alertas = $usuario->getAlertas();
                } else {
                    // Guardar el registro
                    $usuario->guardar();

                    User::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();

                    // Actualizar los datos de sesión
                    $_SESSION['name'] = $usuario->name;
                    $_SESSION['last_name'] = $usuario->last_name;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['image'] = $usuario->image;
                }
            }
        }

        // Renderizar la vista de perfil con los datos y alertas correspondientes
        $router->render('dashboard/profile', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'admin' => isset($usuario) ? $usuario->admin : 0
        ]);
    }

    /**
     * Maneja la solicitud de cambio de contraseña del usuario.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Si la solicitud HTTP es de tipo POST, verifica la validez de la nueva contraseña 
     * y la actualiza si es válida. Luego renderiza la vista del perfil de usuario con 
     * las alertas correspondientes.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function changePassword(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Arreglo para almacenar alertas
        $alertas = [];

        // Obtener el usuario actual
        $usuario = User::find($_SESSION['id']);

        // Procesar solicitud de cambio de contraseña
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            // Validar la nueva contraseña
            $alertas = $usuario->NewPassword();

            if (empty($alertas)) {
                // Verificar si la contraseña actual es correcta
                $resultado = $usuario->checkPassword();

                if ($resultado) {
                    // Si la contraseña actual es correcta, actualizar la contraseña
                    $usuario->password = $usuario->newPassword;

                    // Eliminar propiedades no necesarias
                    unset($usuario->actualPassword);
                    unset($usuario->newPassword);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    // Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        // Si se guarda correctamente, establecer una alerta de éxito
                        User::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else {
                    // Si la contraseña actual es incorrecta, establecer una alerta de error
                    User::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        // Renderizar la vista de perfil con los datos y alertas correspondientes
        $router->render('dashboard/profile', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas,
            'admin' => $usuario->admin
        ]);
    }

    /**
     * Maneja la eliminación del perfil de usuario.
     * Se requiere que el usuario esté autenticado para acceder a esta función.
     * 
     * Si la solicitud HTTP es de tipo POST, intenta eliminar el perfil de usuario
     * junto con la foto de perfil asociada.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function deleteProfile(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Obtener el usuario a eliminar
        $user = User::find($_POST['user_id']);

        // Arreglo para almacenar alertas
        $alertas = [];

        // Procesar solicitud de eliminación de cuenta
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = User::find($_POST['user_id']);

            // Verificación de que el usuario exista
            if (!$user) {
                // Si el usuario no existe, establecer una alerta de error
                User::setAlerta('error', 'El usuario no existe');
                $alertas = User::getAlertas();
            } else {
                // Intentar eliminar el usuario
                if (empty($alertas)) {
                    if ($user->eliminar()) {

                        // Eliminar la foto de perfil asociada al usuario
                        unlink('build/img/profile/' . $user->image . ".jpg");

                        // Redirigir al usuario a la página de inicio
                        header('Location: /');
                        exit;
                    } else {

                        // Si no se puede eliminar, establecer una alerta de error
                        User::setAlerta('error', 'El usuario no se ha podido eliminar');
                        $alertas = User::getAlertas();
                    }
                }
            }
        }

        $router->render('dashboard/profile', [
            'titulo' => 'Perfil',
            'usuario' => $user,
            'alertas' => $alertas,
            'admin' => isset($user) ? $user->admin : 0
        ]);
    }

    /**
     * Maneja la visualización del tablero Kanban para colaboración en un proyecto.
     * 
     * Esta función requiere que el usuario esté autenticado.
     * 
     * Si el usuario es el propietario del proyecto, se muestra la vista general del proyecto.
     * De lo contrario, se muestra el tablero Kanban específico del proyecto para colaboración.
     * 
     * @param Router $router El enrutador de la aplicación.
     * @return void
     */
    public static function collaborationKanban(Router $router)
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Obtener el ID de usuario de la sesión actual
        $user_id = $_SESSION['id'];

        // Arreglo para almacenar alertas
        $alertas = [];

        // Obtener la URL del proyecto desde la solicitud GET
        $url = $_GET['url'];

        // Obtener el proyecto basado en la URL
        $project = Project::where('url', $url);

        // Verificar si el usuario es el propietario del proyecto
        if ($user_id === $project->user_id) {
            // Si el usuario es el propietario, renderizar la vista general del proyecto
            $router->render('dashboard/project', [
                'titulo' => 'Vista General: ' . $project->name,
                'project' => $project,
                'alertas' => $alertas
            ]);
        } else {
            // Si el usuario no es el propietario, renderizar el tablero Kanban para colaboración
            $router->render('dashboard/collaboration-kanban', [
                'titulo' => 'Vista Kanban: ' . $project->name,
                'project' => $project,
                'alertas' => $alertas
            ]);
        }
    }

    /**
     * Maneja el proceso de enviar una invitación por correo electrónico a 
     * un usuario para colaborar en un proyecto.
     * 
     * Esta función requiere que el usuario esté autenticado.
     * 
     * Recibe la dirección de correo electrónico del usuario 
     * al que se enviará la invitación a través de $_POST.
     * 
     * Si la dirección de correo electrónico es válida, se envía la invitación por correo electrónico.
     * 
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
     * 
     * @return void
     */
    public static function invitation()
    {
        // Iniciar sesión y verificar autenticación
        session_start();
        isAuth();

        // Filtrar y validar la dirección de correo electrónico proporcionada en la solicitud POST
        $filterEmail = filter_var($_POST['invitationEmail'], FILTER_VALIDATE_EMAIL);

        // Crear una instancia del objeto Email con los datos necesarios para enviar la invitación
        $email = new Email($_POST['invitationEmail'], $_SESSION['name'], $_SESSION['last_name'], '');

        // Verificar si la dirección de correo electrónico es válida
        if ($filterEmail) {
            // Si la dirección de correo electrónico es válida, intentar enviar la invitación
            if ($email->enviarInvitacion()) {
                // Si la invitación se envía correctamente, preparar un mensaje de éxito
                $result = [
                    'type' => 'exito',
                    'message' => 'Invitación enviada correctamente'
                ];
            } else {
                // Si la invitación no se puede enviar, preparar un mensaje de error
                $result = [
                    'type' => 'error',
                    'message' => 'La invitación no ha podido ser enviada'
                ];
            }
        } else {
            // Si la dirección de correo electrónico no es válida, preparar un mensaje de error
            $result = [
                'type' => 'error',
                'message' => 'El email no es válido'
            ];
        }
        // Enviar el resultado de la operación como respuesta en formato JSON
        echo json_encode($result);
    }
}
