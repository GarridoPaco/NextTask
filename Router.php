<?php

namespace MVC;

/**
 * Clase que gestiona las rutas de la aplicación
 */
class Router
{
    // Propiedades para almacenar rutas
    public array $getRoutes = [];
    public array $postRoutes = [];

    // Método para definir rutas GET
    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    // Método para definir rutas POST
    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    // Método para verificar y manejar las rutas solicitadas
    public function comprobarRutas()
    {
        // Obtener la URL actual y el método HTTP utilizado
        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Determinar la función asociada a la ruta y el método HTTP
        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        // Ejecutar la función si existe, de lo contrario, mostrar un mensaje de error
        if ($fn) {
            // Llama a la función asociada a la ruta
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida.";
        }
    }

    // Método para renderizar vistas
    public function render($view, $datos = [])
    {

        // Asignar valores a las variables que se pasan a la vista
        foreach ($datos as $key => $value) {
            /**
             * Doble signo de dolar significa: variable variable, 
             * básicamente nuestra variable sigue siendo la original, 
             * pero al asignarla a otra no la reescribe, mantiene su valor, 
             * de esta forma el nombre de la variable se asigna dinámicamente
             */
            $$key = $value;
        }

        // Iniciar el almacenamiento en búfer de salida
        ob_start();

        // Incluir la vista dentro del diseño
        include_once __DIR__ . "/views/$view.php";
        // Obtener el contenido del búfer y limpiarlo
        $contenido = ob_get_clean();
        // Incluir el diseño junto con el contenido de la vista
        include_once __DIR__ . '/views/layout.php';
    }
}
