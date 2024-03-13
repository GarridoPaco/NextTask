<?php

namespace Controllers;

use Classes\Email;
use Model\Assignment;
use Model\Collaboration;
use Model\Project;
use Model\Task;
use Model\User;
use MVC\Router;

class DashboardController
{
    public static function index(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];

        $user_id = $_SESSION['id'];

        $projects = Project::belongsTo('user_id', $user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project = new Project($_POST);

            // Validación
            $alertas = $project->validarProyecto();

            if (empty($alertas)) {
                // Generar una URL única
                $project->url = md5(uniqid());

                $project->user_id = $_SESSION['id'];
                $project->guardar();
                $projects = Project::belongsTo('user_id', $user_id);
                
                Project::setAlerta('exito', 'Proyecto creado correctamente');
                $alertas = Project::getAlertas();

                //header('Location: /dashboard');
            }
        }
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'alertas' => $alertas,
            'projects' => $projects
        ]);
    }

    public static function admin(Router $router)
    {
        session_start();
        isAuth();
        isAdmin();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = User::find($_POST['user_id']);
            // Verificación de que el usuario exista
            if (!$user) {
                User::setAlerta('error', 'El usuario no existe');
                $alertas = User::getAlertas();
            }
            if (empty($alertas)) {

                if ($user->eliminar()) {
                    User::setAlerta('exito', 'Usuario eliminado correctamente');
                    $alertas = User::getAlertas();
                } else {
                    User::setAlerta('error', 'El usuario no se ha podido eliminar');
                    $alertas = User::getAlertas();
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

    public static function project(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];
        $user_id = $_SESSION['id'];
        $user = User::find($user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $url = $_GET['url'];
            $project = Project::where('url', $url);
            $router->render('dashboard/project', [
                'titulo' => 'Vista General: ' . $project->name,
                'perfilImg' => $user->image,
                'project' => $project,
                'alertas' => $alertas
            ]);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project = Project::find($_POST['project_id']);
            // debuguear($project);
            // Verificación de que el proyecto exista
            if (!$project) {
                Project::setAlerta('error', 'El projecto no existe');
                $alertas = Project::getAlertas();
            }
            if (empty($alertas)) {

                if ($project->eliminar()) {
                    Project::setAlerta('exito', 'Proyecto eliminado correctamente');
                    $alertas = Project::getAlertas();
                } else {
                    Project::setAlerta('error', 'El proyecto no se ha podido eliminar');
                    $alertas = Project::getAlertas();
                }
            }
            $projects = Project::belongsTo('user_id', $user_id);
            $router->render('dashboard/index', [
                'titulo' => 'Proyectos',
                'perfilImg' => $user->image,
                'projects' => $projects,
                'alertas' => $alertas
            ]);
        }
    }

    public static function kanban(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];
        $url = $_GET['url'];
        $project = Project::where('url', $url);
        $router->render('dashboard/kanban', [
            'titulo' => 'Vista Kanban: ' . $project->name,
            'project' => $project,
            'alertas' => $alertas
        ]);
    }

    public static function collaboration(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];

        $user_id = $_SESSION['id'];
        $tasks = [];
        $assignments = Assignment::belongsTo('user_id', $user_id);
        foreach ($assignments as $assignment) {
            $tasks[] = Task::where('id', $assignment->task_id);
        }
        $users = User::all();
        $projects = Project::all();
        
        $router->render('dashboard/collaboration', [
            'titulo' => 'Colaboraciones',
            'assignments' => $assignments,
            'tasks' => $tasks,
            'users' => $users,
            'projects' => $projects
        ]);
    }

    public static function profile(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = User::find($_SESSION['id']);
        // var_dump($usuario);
        // var_dump($_SESSION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            $usuario->sincronizar($_POST);

            $alertas = $usuario->validateProfile();

            if (empty($alertas)) {
                // Crear carpeta de imagenes
                $imgDir = '../src/img/';

                if (!is_dir($imgDir)) {
                    mkdir($imgDir);
                }
                //  Subida de archivos
                if ($_FILES['image']['name']) {

                    //Elimino la imagen previa si es distinta a la imagen por defecto
                    if ($usuario->image !== 'user_default') {
                        unlink('../src/img/' . $usuario->image . ".jpg");
                        unlink('build/img/' . $usuario->image . ".webp");
                        unlink('build/img/' . $usuario->image . ".jpg");
                    }

                    $profileImg = $_FILES['image'];

                    // Generar un nombre único para cada imagen
                    $imgName = md5(uniqid(rand(), true));
                    move_uploaded_file($profileImg['tmp_name'], $imgDir . $imgName . ".jpg");

                    // Añadir la imagen al objeto usuario
                    $usuario->image = $imgName;
                }
                $existeUsuario = User::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Mensaje de error
                    User::setAlerta('error', 'Email no válido, ya pertenece a otra cuenta');
                    $alertas = $usuario->getAlertas();
                } else {
                    // Guardar el registro
                    $usuario->guardar();

                    User::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();

                    // Asignar el nombre nuevo a la barra
                    $_SESSION['name'] = $usuario->name;
                    $_SESSION['last_name'] = $usuario->last_name;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['image'] = $usuario->image;
                }
            }
        }

        $router->render('dashboard/profile', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'admin' => $usuario->admin
        ]);
    }

    public static function changePassword(Router $router)
    {
        session_start();
        isAuth();

        $alertas = [];
        $usuario = User::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->NewPassword();

            if (empty($alertas)) {
                $resultado = $usuario->checkPassword();

                if ($resultado) {
                    $usuario->password = $usuario->newPassword;

                    // Eliminar propiedades No necesarias
                    unset($usuario->actualPassword);
                    unset($usuario->newPassword);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    // Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        User::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else {
                    User::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/profile', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas,
            'admin' => $usuario->admin
        ]);
    }
    public static function collaborationKanban(Router $router)
    {
        session_start();
        isAuth();
        $user_id = $_SESSION['id'];
        $alertas = [];
        $url = $_GET['url'];
        $project = Project::where('url', $url);
        if ($user_id === $project->user_id) {
            $router->render('dashboard/project', [
                'titulo' => 'Vista General: ' . $project->name,
                'project' => $project,
                'alertas' => $alertas
            ]);
        } else {
            $router->render('dashboard/collaboration-kanban', [
                'titulo' => 'Vista Kanban: ' . $project->name,
                'project' => $project,
                'alertas' => $alertas
            ]);
        }
    }

    public static function invitation()
    {
        session_start();
        isAuth();

        $filterEmail = filter_var($_POST['invitationEmail'], FILTER_VALIDATE_EMAIL);
        // Enviar el email
        $email = new Email($_POST['invitationEmail'], $_SESSION['name'], $_SESSION['last_name'], '');
        if ($filterEmail) {
            if ($email->enviarInvitacion()) {
                $result = [
                    'type' => 'exito',
                    'message' => 'Invitación enviada correctamente'
                ];
            } else {
                $result = [
                    'type' => 'error',
                    'message' => 'La invitación no ha podido ser enviada'
                ];
            }
        } else {
            $result = [
                'type' => 'error',
                'message' => 'El email no es válido'
            ];
        }

        echo json_encode($result);
    }

    public static function deleteUser(Router $router)
    {
        session_start();
        isAuth();
        isAdmin();
        $alertas = [];
    }
}
