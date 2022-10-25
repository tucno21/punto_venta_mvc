<?php

use System\ConnectDB;

/**
 * cargar el autoloader de composer
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * parametros de la aplicacion
 */
require_once dirname(__DIR__) . '/App/Config/App.php';
/**
 * envia todas las funciones o helpers a la aplicacion
 */
require_once 'Functions.php';

/**
 * obtener la configuracion de la aplicacion
 * para la base de datos
 */
ConnectDB::dataBase($db_host, $db_user, $db_pass, $db_name, $db_charset);
