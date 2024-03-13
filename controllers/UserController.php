<?php

namespace Controllers;

use Model\User;
use Model\Project;

class userController
{
    public static function users()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);
        // Solo usuarios verificados
        $users = User::belongsTo('verified', 1);
        // Revisar para eliminar el usuario admin
        $filterUsers = array_filter($users, function ($user) {
            return $user->admin !== '1';
        });
        // Reindexo el array para asegurarme de que los índices sean secuenciales
        $filterUsers = array_values($filterUsers);

        session_start();
        if (!$project || $project->user_id !== $_SESSION['id'])
            header('Location/404');

        echo json_encode(['users' => $filterUsers]);
    }

    public static function user()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) header('Location: /dashboard');

        session_start();

        $user = User::find($_SESSION['id']);

        echo json_encode(['user' => $user]);
    }
}
