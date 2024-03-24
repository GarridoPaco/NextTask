<?php

namespace Controllers;

use Model\Assignment;
use Model\Project;

/**
 * Clase controladora para las asignaciones
 */
class AssignmentController
{
    /**
     * Interfaz de la API que devuelve al cliente el listado codificado en formato JSON de las diferentes
     * asignaciones que tiene un proyecto.
     * El proyecto es identificado por la url única enviada por el cliente mediante 
     * una petición HTTP tipo GET.
     * Si no está definida la url en la petición, o no hay ningún proyecto con esa url
     * el usuario es redirigido.
     *
     * @return void
     */
    public static function index()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) header('Location: /dashboard');

        $project = Project::where('url', $projectURL);

        if (!$project) header('Location/404');

        $assignments = Assignment::getAssignment($project->id);

        echo json_encode(['assignments' => $assignments]);
    }

    /**
     * Función que crea una nueva asignación solicitada por una petición HTTP tipo POST.
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
     *
     * @return void
     */
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            $project = Project::where('url', $_POST['url']);
            session_start();
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

    /**
     * Función que elimina la asignación indicada por la petición HTTP tipo POST.
     * Devuelve un mensaje en formato JSON al cliente con el resultado.
     *
     * @return void
     */
    public static function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            session_start();
            $project = Project::where('url', $_POST['url']);
            if (!$project || $project->user_id !== $_SESSION['id']) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al eliminar la asignación'
                ];
                echo json_encode($result);
                return;
            }

            $assignment = new Assignment($_POST);
            $assignment->deleteAssignment();
            $result = [
                'type' => 'exito',
                'message' => 'Asignación eliminada correctamente'
            ];
            echo json_encode($result);
        }
    }
}