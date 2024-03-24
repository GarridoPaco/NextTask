<?php

namespace Controllers;

use Model\User;
use Model\Project;

/**
 * Clase controladora para los usuarios.
 */
class userController
{

    /**
     * Obtiene y devuelve en formato JSON los usuarios registrados en la aplicación.
     * Este método se accede a través de una solicitud HTTP GET.
     * Se espera que el cliente proporcione la URL única del proyecto como parámetro de la solicitud.
     * Si la URL no está definida en la solicitud, se redirige al usuario.
     * Los usuarios solo son accesibles por el usuario que creó el proyecto.
     * Solo se devuelven los usuarios verificados y se filtra el usuario administrador.
     *
     * @return void
     */
    public static function users()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) {
            header('Location: /dashboard');
            exit;
        }

        // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
        $project = Project::where('url', $projectURL);
        session_start();
        if (!$project || $project->user_id !== $_SESSION['id']) {
            header('Location: /404');
            exit;
        }

        // Solo usuarios verificados
        $users = User::belongsTo('verified', 1);
        // Filtrar el usuario administrador
        $filterUsers = array_filter($users, function ($user) {
            return $user->admin !== '1';
        });
        // Reindexar el array para asegurarme de que los índices sean secuenciales
        $filterUsers = array_values($filterUsers);

        echo json_encode(['users' => $filterUsers]);
    }

    /**
     * Obtiene y devuelve en formato JSON los detalles del usuario actualmente autenticado.
     * Este método se accede a través de una solicitud HTTP GET.
     * Se espera que el cliente proporcione la URL única del proyecto como parámetro de la solicitud.
     * Si la URL no está definida en la solicitud, se redirige al usuario.
     *
     * @return void
     */
    public static function user()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) {
            header('Location: /dashboard');
            exit;
        }

        session_start();

        $user = User::find($_SESSION['id']);

        echo json_encode(['user' => $user]);
    }
}
