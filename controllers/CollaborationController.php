<?php

namespace Controllers;

use Model\Assignment;
use Model\Project;
use Model\Task;
use Model\Collaboration;
use Model\User;

class CollaborationController
{
    public static function index()
    {
        $projectURL = $_GET['url'];

        if(!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);

        session_start();
        if(!$project || $project->user_id !== $_SESSION['id'])
            header('Location/404');

        $collaborations = Collaboration::belongsTo('project_id', $project->id);
        $collaborators = [];
        foreach($collaborations as $collaboration){
            $collaborators[] = User::where('id', $collaboration->user_id);
        }
        //$collaborators = User::where('id', $collaborations->user_id);

        echo json_encode(['collaborators' => $collaborators]);

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
                    'message' => 'Hubo un error al añadir la colaboración'
                ];
                echo json_encode($result);
                return;
            }

            $usersId = explode(",", $_POST['user_id']);
            for($i=0; $i<count($usersId); $i++){
                $collaboration = new Collaboration();
                $collaboration->user_id = $usersId[$i];
                $collaboration->project_id = $project->id;
                $collaboration->guardar();
            }

            $result = [
                'type' => 'exito',
                'message' => 'Colaboración añadida correctamente',
                'log' => $usersId
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
                    'message' => 'Hubo un error al eliminar al colaborador'
                ];
                echo json_encode($result);
                return;
            }

            $collaboration = new Collaboration($_POST);
            $collaboration->project_id = $project->id;
            $collaboration->eliminarColaborador();

            // $assignment = new Assignment();
            // $assignment->user_id === $_SESSION['id'];
            // $assignment->task_id === 
            $result = [
                'type' => 'exito',
                'message' => 'Colaborador eliminado correctamente'
            ];
            echo json_encode($result);
        }
    }
}