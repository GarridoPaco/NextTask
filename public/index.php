<?php 

/**
 * Define y gestiona las rutas de la aplicación web, 
 * asegurando que las solicitudes HTTP sean dirigidas a 
 * los controladores correspondientes para su procesamiento.
 */

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\TaskController;
use Controllers\CollaborationController;
use Controllers\UserController;
use Controllers\AssignmentController;
use Controllers\ProjectController;
use Controllers\CommentController;
use Controllers\IndexController;
use MVC\Router;
$router = new Router();
// Index
$router->get('/', [IndexController::class, 'index']);
$router->get('/development', [IndexController::class, 'development']);
$router->get('/resources', [IndexController::class, 'resources']);
$router->get('/help', [IndexController::class, 'help']);

// Login
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Crear cuenta
$router->get('/create', [LoginController::class, 'create']);
$router->post('/create', [LoginController::class, 'create']);

// Olvide el password
$router->get('/forget', [LoginController::class, 'forget']);
$router->post('/forget', [LoginController::class, 'forget']);

// Nuevo password
$router->get('/reset', [LoginController::class, 'reset']);
$router->post('/reset', [LoginController::class, 'reset']);

// Confirmación de cuenta
$router->get('/message', [LoginController::class, 'message']);
$router->get('/confirm', [LoginController::class, 'confirm']);

// Zona privada de proyectos
$router->get('/admin', [DashboardController::class, 'admin']);
$router->post('/admin', [DashboardController::class, 'admin']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->post('/dashboard', [DashboardController::class, 'index']);
$router->get('/project', [DashboardController::class, 'project']);
$router->post('/project', [DashboardController::class, 'project']);
$router->get('/kanban', [DashboardController::class, 'kanban']);
$router->get('/collaboration', [DashboardController::class, 'collaboration']);
$router->get('/collaboration-kanban', [DashboardController::class, 'collaborationKanban']);
$router->get('/profile', [DashboardController::class, 'profile']);
$router->post('/profile', [DashboardController::class, 'profile']);
$router->post('/deleteProfile', [DashboardController::class, 'deleteProfile']);
$router->post('/changePassword', [DashboardController::class, 'changePassword']);
$router->post('/invitation', [DashboardController::class, 'invitation']);

// API para el proyecto
$router->get('/api/project', [ProjectController::class, 'index']);
$router->post('/api/project/update', [ProjectController::class, 'update']);
$router->post('/api/project/delete', [ProjectController::class, 'delete']);

// API para las tareas
$router->get('/api/tasks', [TaskController::class, 'index']);
$router->post('/api/task', [TaskController::class, 'create']);
$router->post('/api/task/update', [TaskController::class, 'update']);
$router->post('/api/task/delete', [TaskController::class, 'delete']);

// API para los comentarios de las tareas
$router->post('/api/comment', [CommentController::class, 'index']);
$router->post('/api/comment/create', [CommentController::class, 'create']);
$router->post('/api/comment/update', [CommentController::class, 'update']);
$router->post('/api/comment/delete', [CommentController::class, 'delete']);

// API para las colaboraciones
$router->get('/api/collaboration', [CollaborationController::class, 'index']);
$router->post('/api/collaboration', [CollaborationController::class, 'create']);
$router->post('/api/collaboration/delete', [CollaborationController::class, 'delete']);

// API para las asignaciones de tareas
$router->get('/api/assignment', [AssignmentController::class, 'index']);
$router->post('/api/assignment', [AssignmentController::class, 'create']);
$router->post('/api/assignment/delete', [AssignmentController::class, 'delete']);

// API para obtener los usuarios y el usuario activo
$router->get('/api/users', [UserController::class, 'users']);
$router->get('/api/user', [UserController::class, 'user']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();