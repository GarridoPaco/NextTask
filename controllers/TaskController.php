<?php

namespace Controllers;

use Model\Collaboration;
use Model\Project;
use Model\Task;
use Model\User;

class TaskController
{
    public static function index()
    {
        $projectURL = $_GET['url'];

        if(!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);

        session_start();
        if(!$project || $project->user_id !== $_SESSION['id'])
            header('Location/404');

        $tasks = Task::belongsTo('project_id', $project->id);
        

        echo json_encode(['tasks' => $tasks
    ]);

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
                    'message' => 'Hubo un error al añadir la tarea'
                ];
                echo json_encode($result);
                return;
            }
            $task = new Task($_POST);
            $task->project_id = $project->id;
            $savedTask = $task->guardar();
            if ($savedTask['resultado']){
                $result = [
                    'type' => 'exito',
                    'message' => 'Tarea añadida correctamente',
                    'task_id' => $savedTask['id']
                ];
            } else {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir la tarea'
                ];
            }

            echo json_encode($result);
        }
    }
    public static function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista
            if (!$project) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir la tarea'
                ];
                echo json_encode($result);
                return;
            }

            $task = new Task($_POST);
            $task->guardar();
            $result = [
                'type' => 'exito',
                'message' => 'Tarea actualizada correctamente'
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
                    'message' => 'Hubo un error al eliminar la tarea'
                ];
                echo json_encode($result);
                return;
            }

            $task = new Task($_POST);
            $task->eliminar();
            $result = [
                'type' => 'exito',
                'message' => 'Tarea eliminada correctamente'
            ];
            echo json_encode($result);
        }
    }
}
