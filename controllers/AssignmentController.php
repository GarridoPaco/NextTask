<?php

namespace Controllers;

use Model\Assignment;
use Model\Project;
use Model\Task;
use Model\Collaboration;
use Model\User;

class AssignmentController
{
    public static function index()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);

        session_start();
        if (!$project || $project->user_id !== $_SESSION['id'])
            header('Location/404');

        $assignments = Assignment::getAssingns($project->id);

        echo json_encode(['assignments' => $assignments]);
    }
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            if (!$project || $project->user_id !== $_SESSION['id']) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir la asignación'
                ];
                echo json_encode($result);
                return;
            }

            $assignment = new Assignment($_POST);
            $assignment->guardar();


            $result = [
                'type' => 'exito',
                'message' => 'Asignación añadida correctamente'
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
                    'message' => 'Hubo un error al eliminar la asignación'
                ];
                echo json_encode($result);
                return;
            }

            $assignment = new Assignment($_POST);
            $assignment->eliminarAsignacion();
            $result = [
                'type' => 'exito',
                'message' => 'Asignación eliminada correctamente'
            ];
            echo json_encode($result);
        }
    }
}
