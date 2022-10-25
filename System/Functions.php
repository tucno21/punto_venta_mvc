<?php

use System\Csrf;
use System\Route;
use System\Session;
use System\Redirect;
use System\RenderView;
use App\Model\Permissions;
use System\CronosException;

/**
 * funciones principales para toda la aplicacion
 */

if (!function_exists('dd')) {
    /**
     * debugear sin continuar con otros codigos de linea
     */
    function dd($variable)
    {
        echo "<pre>";
        var_dump($variable);
        echo "</pre>";
        exit;
    }
}


if (!function_exists('d')) {
    /**
     * debugear continuando las lineas de codigo
     */
    function d($variable)
    {
        echo "<pre>";
        var_dump($variable);
        echo "</pre>";
    }
}

/**
 * ruta de documento public
 * C:/.../www/mini_framework_mvc_php/public
 */
define('DIR_PUBLIC',  dirname(__DIR__) . '/public');

/**
 * ruta carpeta de imagenes
 */
define('DIR_IMG', dirname(__DIR__) . '/public/' . $imageFolder . '/');

/**
 * ruta de la carpeta App
 * C:\...\www\mini_framework_mvc_php/App
 */
define('DIR_APP', dirname(__DIR__) . '/App');

/**
 * como enviar los resultados array u obejeto
 * no determina para MODEL
 */
define('RESULT_TYPE', $resultType);

/**
 * obtener la ruta web de la aplicacion sin "/"
 */
if (!function_exists('last_char')) {
    function last_char($string)
    {
        //extraer el ultimo letra de un string
        $slash = substr($string, -1);
        if ($slash == '/') {
            //eliminar la ultima letra del string
            return substr($string, 0, -1);
        } else {
            return $string;
        }
    }
}

/**
 * url de la web Principal
 */
define('base_url', last_char($baseURL));


if (!function_exists('base_url')) {
    /**
     * funcion url con parametros
     */
    function base_url($parameters = null)
    {
        return base_url . $parameters;
    }
}

if (!function_exists('view')) {
    /**
     * Funcion para renderizar las vistas desde el controlador
     */
    function view(string $name, array $data = [])
    {
        //cambiar '.' por '/'
        $name = str_replace('.', '/', $name);

        try {
            return RenderView::render($name, $data);
        } catch (CronosException $e) {
            echo $e->cronosMessage();
        }
    }
}


if (!function_exists('session')) {
    /**
     * Instaciar Class Session
     */
    function session()
    {
        return new Session;
    }
}

if (!function_exists('auth')) {
    /**
     * Instaciar Class Session
     */
    function auth()
    {
        return new Session;
    }
}

if (!function_exists('route')) {
    /**
     * funcion para redireccionar a otra web usando el nombre de la ruta
     */
    function route(string $nameRoute)
    {
        return Route::route($nameRoute);
    }
}

/**
 * funcion para  redireccionamiento a otra vista con parametros
 */
if (!function_exists('redirect')) {
    function redirect(string $pathUrl = '', array $dataView = [])
    {
        return Redirect::redirect($pathUrl, $dataView);
    }
}

/**
 * funcion para  redireccionamiento a otra vista con parametros
 */
if (!function_exists('back')) {
    function back(string $pathUrl = '', array $dataView = [])
    {
        return Redirect::redirect($pathUrl, $dataView);
    }
}

/**
 * funcion para extender partes layout o vista
 */
function ext(string $folderLayout)
{
    //cambiar '.' por '/'
    $folderLayout = str_replace('.', '/', $folderLayout);

    return DIR_APP . '/View/' . $folderLayout . '.php';
}

/**
 * funcion para verificar si el usuario tiene permiso
 */
function can(string $data)
{
    $rolId = session()->user()->rol_id;

    $permisosRol = Permissions::permisosRol($rolId);

    //cuando viene un solo objeto
    if (is_object($permisosRol)) {
        $permisosRol = [$permisosRol];
    }

    if (in_array($data, array_column((array)$permisosRol, 'per_name'))) {
        return true;
    } else {
        return false;
    }
}

/**
 * funcion Csrf
 */
if (!function_exists('csrf')) {
    function csrf()
    {
        $csrf = Csrf::generateToken();
        $imputs = "<input type='hidden' name='_token' value='$csrf'>";
        return $imputs;
    }
}
