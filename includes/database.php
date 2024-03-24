<?php
try {
    // Conectar a la base de datos utilizando las variables de entorno
    $db = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

    // Establecer el conjunto de caracteres UTF-8 para la conexión
    $db->set_charset('utf8');

    // Verificar si la conexión fue exitosa
    if (!$db) {
        // Si la conexión falla, lanzar una excepción con el mensaje de error correspondiente
        throw new Exception("Error: No se pudo conectar a MySQL. Error: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    // En caso de error, mostrar el mensaje de error y salir del script
    echo "Error: " . $e->getMessage();
    exit;
}
