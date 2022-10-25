<?php
require_once dirname(__DIR__) . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

/**
 * configuraciones principales para la aplicacion
 */
// dd($_ENV);
/**
 * colocar la url Principal de la web sin "/"
 */
$baseURL = $_ENV['APP_URL'];

/**
 * configuraciones de coneccion a la BD
 */
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USERNAME'];
$db_pass = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_DATABASE'];
$db_charset = 'utf8';


/**
 * nombre de la carpeta para almacenar imagenes
 */
$imageFolder = $_ENV['FOLDER_IMAGE'];

/**
 * definir hora y fecha local
 */

date_default_timezone_set($_ENV['TIME_ZONE']);

/**
 * definir el tipo de resultado de la consulta
 * 'array' o 'object'
 */
$resultType = 'object';
