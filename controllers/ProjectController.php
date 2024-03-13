<?php

namespace Controllers;

use Model\Project;

class ProjectController
{
    public static function index()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);

        session_start();
        if (!$project || $project->user_id !== $_SESSION['id'])
            header('Location/404');

        echo json_encode([
            'project' => $project
        ]);
    }
    
    public static function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            if (!$project || $project->user_id !== $_SESSION['id']) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir el proyecto'
                ];
                echo json_encode($result);
                return;
            }

            $projectEdit = new Project($_POST);
            $projectEdit->guardar();
            $result = [
                'type' => 'exito',
                'message' => 'Proyecto actualizado correctamente'
            ];
            echo json_encode($result);
        }
    }

    public static function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            if (!$project || $project->user_id !== $_SESSION['id']) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al eliminar el proyecto'
                ];
                echo json_encode($result);
                return;
            }

            $project->eliminar();
            $result = [
                'type' => 'exito',
                'message' => 'Proyecto eliminado correctamente'
            ];
            echo json_encode($result);
        }
    }
}
