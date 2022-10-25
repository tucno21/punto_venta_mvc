<?php

use System\Route;

/**
 * index acceso a la aplicacion
 */
require_once dirname(__DIR__) . '/Routes/web.php';
/**
 * ejecuta la busqueda de rutas para los controladores
 */
Route::run();
