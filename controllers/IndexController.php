<?php

namespace Controllers;

use MVC\Router;

/**
 * Clase controladora para la gestión de la página de inicio
 */
class IndexController
{
    public static function index(Router $router)
    {
        // Renderizar la vista de inicio
        $router->render('index/home', [
            'titulo' => 'Inicio'
        ]);
    }

    public static function development(Router $router)
    {
        $router->render('index/development', [
            'titulo' => 'Desarrollo'
        ]);
    }

    public static function resources(Router $router)
    {
        $router->render('index/resources', [
            'titulo' => 'Recursos'
        ]);
    }

    public static function help(Router $router)
    {
        $router->render('index/help', [
            'titulo' => 'Ayuda'
        ]);
    }
}
