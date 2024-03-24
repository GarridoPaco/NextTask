<?php

/**
 * Función para depurar variables imprimiéndolas en formato legible para humanos y deteniendo la ejecución del script.
 *
 * @param mixed $variable La variable que se va a depurar.
 * @return string
 */
function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

/**
 * Función para escapar y sanitizar el contenido HTML.
 *
 * @param string $html El contenido HTML que se va a sanear.
 * @return string El contenido HTML sanitizado.
 */
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}


/**
 * Función que verifica si el usuario está autenticado. Si no lo está, redirige al inicio.
 *
 * @return void
 */
function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}

/**
 * Función que verifica si el usuario es administrador. Si no lo es, redirige al inicio.
 *
 * @return void
 */
function isAdmin() : void {
    if($_SESSION['admin'] === false) {
        header('Location: /');
    }
}