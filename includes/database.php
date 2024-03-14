<?php
try {
    $db = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
    $db->set_charset('utf8');
    if (!$db) {
        throw new Exception("Error: No se pudo conectar a MySQL. Error: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
