<?php

namespace Controllers;

use Model\Project;
use Model\Task;

/**
 * Clase controladora para las tareas.
 */
class TaskController
{
    /**
     * Obtiene y devuelve en formato JSON un array con las tareas asociadas a un proyecto.
     * Este método se accede a través de una solicitud HTTP GET.
     * Se espera que el cliente proporcione la URL única del proyecto como parámetro de la solicitud.
     * Si la URL no está definida en la solicitud, se redirige al usuario.
     *
     * @return void
     */
    public static function index()
    {
        $projectURL = $_GET['url'];

        if (!$projectURL) {
            header('Location: /dashboard');
            exit;
        }

        $project = Project::where('url', $projectURL);

        $tasks = Task::belongsTo('project_id', $project->id);


        echo json_encode([
            'tasks' => $tasks
        ]);
    }

    /**
     * Crea una nueva tarea y la guarda en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto como parámetro de la solicitud,
     * así como los datos de la nueva tarea.
     * La tarea es creada por el usuario que ha iniciado sesión y asociada al proyecto.
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
     * En caso de éxito se devuelve el ID de la nueva tarea en el mismo JSON.
     *
     * @return void
     */
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            session_start();
            $project = Project::where('url', $_POST['url']);
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
            // Comprobación de que la tarea se ha guardado correctamente
            if ($savedTask['resultado']) {
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

    /**
     * Actualiza una tarea existente en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto como parámetro de la solicitud,
     * así como los datos a actualizar en la tarea.
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
     *
     * @return void
     */
    public static function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista
            if (!$project) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($result);
                return;
            }

            $task = new Task($_POST);
            $updatedTask = $task->guardar();
            // Comprobación de que la tarea se actualiza correctamente
            if ($updatedTask) {
                $result = [
                    'type' => 'exito',
                    'message' => 'Tarea actualizada correctamente'
                ];
            } else {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al actualizar la tarea'
                ];
            }

            echo json_encode($result);
        }
    }

    /**
     * Elimina una tarea de la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto y 
     * el ID de la tarea como parámetros de la solicitud.
     * La tarea solo es eliminada por el usuario que creó el proyecto.
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
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
                    'message' => 'Hubo un error al eliminar la tarea'
                ];
                echo json_encode($result);
                return;
            }

            $task = new Task($_POST);
            
            // Verificación de que la tarea se ha eliminado correctamente
            if ($task->eliminar()){
                $result = [
                    'type' => 'exito',
                    'message' => 'Tarea eliminada correctamente'
                ];
            } else {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al eliminar la tarea'
                ];
            }

            echo json_encode($result);
        }
    }
}
