<?php

namespace Controllers;

use Model\Comment;
use Model\CommentUser;
use Model\Project;

class CommentController
{
    public static function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $comments = CommentUser::findComments($task_id);
            echo json_encode($comments);
        }

    }
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $project = Project::where('url', $_POST['url']);
            // Verificación de que el proyecto exista
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
            if ($saveComment['resultado']){
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
