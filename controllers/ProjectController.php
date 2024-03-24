<?php

namespace Controllers;

use Model\Project;

/**
 * Clase controladora para los proyectos.
 */
class ProjectController
{
    /**
     * Obtiene y devuelve en formato JSON los detalles de un proyecto.
     * Este método se accede a través de una solicitud HTTP GET.
     * Se espera que el cliente proporcione la URL única del proyecto como parámetro de la solicitud.
     * Si la URL no está definida en la solicitud o no corresponde a ningún proyecto, 
     * se redirige al usuario.
     *
     * @return void
     */
    public static function index()
    {
        // Verificación de que el proyecto exista
        $projectURL = $_GET['url'];
        if (!$projectURL) {
            header('Location: /dashboard');
            exit;
        }

        $project = Project::where('url', $projectURL);
        if (!$project) {
            header('Location: /404');
            exit;
        }

        echo json_encode([
            'project' => $project
        ]);
    }

    /**
     * Actualiza un proyecto existente en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto y 
     * los datos del proyecto a actualizar como parámetros de la solicitud.
     * El proyecto es actualizado por el usuario que ha iniciado sesión.
     * Se devuelve al cliente en formato JSON un mensaje de error o éxito.
     *
     * @return void
     */
    public static function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificación de que el proyecto exista y que el usuario es el mismo que ha creado el proyecto
            session_start();
            $project = Project::where('url', $_POST['url']);

            if (!$project || $project->user_id !== $_SESSION['id']) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al actualizar el proyecto'
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

    /**
     * Elimina un proyecto de la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto como parámetro de la solicitud.
     * El proyecto es eliminado por el usuario que ha iniciado sesión.
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