<?php

namespace Controllers;

use Model\Project;
use Model\Collaboration;
use Model\User;

/**
 * Clase controladora para las colaboraciones
 */
class CollaborationController
{
    /**
     * Obtiene y devuelve en formato JSON los colaboradores asignados a un proyecto.
     * Este método se accede a través de una solicitud HTTP GET.
     * Se espera que el cliente proporcione la URL única del proyecto como parámetro de consulta.
     * Si la URL no está definida en la solicitud o no se encuentra ningún proyecto con esa URL, se redirige al usuario.
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

        if (!$project){
            header('Location: /404');
            exit;
        }

        // Se obtienen todas las colaboraciones del proyeto
        $collaborations = Collaboration::belongsTo('project_id', $project->id);


        // Array donde se almacenan los colaboradores
        $collaborators = [];
        foreach ($collaborations as $collaboration) {
            $collaborators[] = User::where('id', $collaboration->user_id);
        }

        echo json_encode(['collaborators' => $collaborators]);
    }

    /**
     * Crea nuevas colaboraciones y las guarda en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente, que debe ser el creador del proyecto, envíe una cadena con los IDs de usuario separados por comas.
     * Devuelve un mensaje JSON de éxito o error al cliente.
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
                    'message' => 'Hubo un error al añadir la colaboración'
                ];
                echo json_encode($result);
                return;
            }

            /**
             * Se almacenan en un array los ids de los usuarios a añadir como colaboradores, 
             * los ids son enviados como una cadena separados por una coma.
             */
            $usersId = explode(",", $_POST['user_id']);
            // Guardado de cada asignación
            for ($i = 0; $i < count($usersId); $i++) {
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

    /**
     * Elimina una colaboración específica de la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente envíe el ID de usuario y la URL del proyecto.
     * Devuelve un mensaje JSON de éxito o error al cliente.
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
                    'message' => 'Hubo un error al eliminar al colaborador'
                ];
                echo json_encode($result);
                return;
            }

            // Eliminación de la colaboración
            $collaboration = new Collaboration($_POST);
            $collaboration->project_id = $project->id;
            $collaboration->deleteCollaboration();

            $result = [
                'type' => 'exito',
                'message' => 'Colaborador eliminado correctamente'
            ];
            echo json_encode($result);
        }
    }
}
