<?php 

use Model\ActiveRecord;

// Cargar el autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Requerir funciones personalizadas y la configuración de la base de datos
require 'funciones.php';
require 'database.php';

// Conectar a la base de datos utilizando ActiveRecord
ActiveRecord::setDB($db);