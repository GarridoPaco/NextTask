<?php

namespace Controllers;

use Model\Comment;
use Model\CommentUser;
use Model\Project;

/**
 * Clase controladora para los comentarios
 */
class CommentController
{

    /**
     * Obtiene y devuelve en formato JSON los comentarios asociados a una tarea.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione el ID de la tarea como parámetro de la solicitud.
     *
     * @return void
     */
    public static function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $comments = CommentUser::findComments($task_id);
            echo json_encode($comments);
        }
    }

    /**
     * Crea un nuevo comentario y lo guarda en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto y 
     * el ID del comentario como parámetros de la solicitud,
     * así como los datos del comentario.
     * El comentario es creado por el usuario que ha iniciado sesión.
     * Devuelve un mensaje JSON de éxito o error al cliente.
     *
     * @return void
     */
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            session_start();

            // Verificación de que el proyecto exista
            $project = Project::where('url', $_POST['url']);
            if (!$project) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir el comentario'
                ];
                echo json_encode($result);
                return;
            }

            $comment = new Comment($_POST);
            $comment->user_id = $_SESSION['id'];
            $saveComment = $comment->guardar();

            if ($saveComment['resultado']) {
                $result = [
                    'comment_id' => $saveComment['id'],
                    'type' => 'exito',
                    'message' => 'Comentario añadido correctamente'
                ];
                echo json_encode($result);
            } else {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al añadir el comentario'
                ];
                echo json_encode($result);
            }
        }
    }

    /**
     * Actualiza un comentario existente en la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto, 
     * el ID del comentario, el ID de la tarea y el nuevo texto del comentario 
     * como parámetros de la solicitud.
     * El comentario es actualizado por el usuario que ha iniciado sesión.
     * Devuelve un mensaje JSON de éxito o error al cliente.
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
                    'message' => 'Hubo un error al actualizar el comentario'
                ];
                echo json_encode($result);
                return;
            }

            $comment = new Comment($_POST);
            $comment->user_id = $_SESSION['id'];
            $comment->guardar();
            $result = [
                'type' => 'exito',
                'message' => 'Comentario actualizado correctamente'
            ];
            echo json_encode($result);
        }
    }

    /**
     * Elimina un comentario de la base de datos.
     * Este método se accede a través de una solicitud HTTP POST.
     * Se espera que el cliente proporcione la URL del proyecto y 
     * el ID del comentario como parámetros de la solicitud.
     * El comentario es eliminado por el usuario que ha iniciado sesión.
     * Devuelve un mensaje JSON de éxito o error al cliente.
     *
     * @return void
     */
    public static function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista
            if (!$project) {
                $result = [
                    'type' => 'error',
                    'message' => 'Hubo un error al eliminar el comentario'
                ];
                echo json_encode($result);
                return;
            }

            $comment = new Comment($_POST);
            $comment->eliminar();
            $result = [
                'type' => 'exito',
                'message' => 'Comentario eliminado correctamente'
            ];
            echo json_encode($result);
        }
    }
}
