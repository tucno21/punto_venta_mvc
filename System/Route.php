<?php

namespace System;

use Throwable;
use System\Request;
use System\RenderView;
use System\ResponseHTTP;
use System\CronosException;


/**
 * manejo de rutas GET y POST
 */

class Route
{
    /**
     * almacena las rutas y controladores
     * ["/" => [HomeController, "index"], "login" => [AuthController, "login"]]
     * @var array
     */
    protected static array $getRoutes = [];
    protected static array $postRoutes = [];

    /**
     * instanciando el objeto Request
     */
    public static Request $request;
    /**
     * instanciando el objeto responseHTTP
     */
    public static ResponseHTTP $responseHTTP;
    /**
     * instanciando el objeto RenderView
     */
    public static RenderView $renderView;

    /**
     * nombre de las rutas
     */
    protected static array $nameUrl = [];
    protected static array $nameRoute = [];

    /**
     * recibe el metodo  GET, el parametro de la url y el controlador o funcion
     * del archivo App/Config/Routes.php
     */
    public static function get($url, $controller)
    {
        array_push(self::$nameUrl, $url);
        self::$getRoutes[$url] = $controller;
        return new static;
    }

    /**
     * recibe el metodo  POST, el parametro de la url y el controlador o funcion
     * del archivo App/Config/Routes.php
     */
    public static function post($url, $controller)
    {
        self::$postRoutes[$url] = $controller;
        return new static;
    }

    /**
     * ejecuta la busqueda de rutas para los controladores
     */
    public static function run()
    {
        self::$request = new Request();
        self::$responseHTTP = new ResponseHTTP();
        self::$renderView = new RenderView();
        return self::executeRoutes();
    }

    /**
     * ejecuta las rutas y su controlador
     * @return mixed
     */
    protected static function executeRoutes()
    {
        $callback = self::searchRoutes();

        /**
         * comprueba si el param2 es un string get(param1, param2);
         * Route::get('/login', "Desde la url principal");
         */
        if (is_string($callback)) {
            echo 'es solo un string';
            exit;
        }

        /**
         * cuando los pathUrl no existe en el array de rutas
         * error 404
         * $callback = null
         */
        if ($callback == null) {
            self::$responseHTTP->setStatusCode(404);
            echo self::$renderView->render('error/404');
            exit;
        }

        /**
         * comprueba si el param2 es un array get(param1, param2);
         * Route::get('/login', [AuthController::class, 'login']);
         */
        if (is_array($callback)) {
            //verificar si la clase existe
            try {
                //[AuthController::class, 'login']
                $class = $callback[0];
                $callback[0] = new $callback[0]; //convierte el primer elemento en objeto class
                //$callback = [object(App\Controller\Auth\AuthController), "login"]
                // dd(method_exists($callback[0], $callback[1]));
                //comprobar si existe un metodo de una clase
                try {
                    return call_user_func($callback, new static);
                } catch (Throwable $th) {
                    // $mensaje = "El metodo  '" . $callback[1] . "' no existe en la clase " . $class;
                    $mensaje = $th->getMessage();
                    $archivo = $th->getFile();
                    $line = $th->getLine();
                    include_once 'mensajeThorwable.php';
                    exit;
                }
            } catch (Throwable $t) {
                $mensaje = "la clase '" . $callback[0] . "' no existe, o asegurese de haber importado la clase en Routes/web.php";
                $archivo = $t->getFile();
                $line = $t->getLine();
                include_once 'mensajeThorwable.php';
                exit;
            }
        }

        /**
         * comprueba si el param2 es un funcion get(param1, param2);
         * Route::get('/login', function () { echo 'desde la funcion';});
         */
        if (is_callable($callback)) {
            //ejecuta la funcion
            return call_user_func($callback, new static);
        }
    }

    /**
     * busca la ruta url en el array de rutas
     * @return mixed
     */
    protected static function searchRoutes()
    {
        //trae el HTTP GET o POST de la web
        $methodUrl = self::$request->methodWeb();
        //trae la url de la web
        $pathUrl = self::$request->getPath();

        //si el metodo es GET
        if ($methodUrl === 'get') {
            //busca la ruta en el array getRoutes
            $sendRoute = self::$getRoutes[$pathUrl] ?? null;
        } else {
            //busca la ruta en el array postRoutes
            $sendRoute = self::$postRoutes[$pathUrl] ?? null;
        }

        return $sendRoute;
    }

    /**
     * nombre de las rutas
     */
    public static function name(string $name)
    {
        foreach (self::$nameUrl as $key => $value) {
            //agrega el nombre de la ruta
            self::$nameUrl[$name] = $value;
        }

        //array_unique elimina los valores repetidos
        $data = array_unique(self::$nameUrl);
        //array_diff_key elimina los valores repetidos
        self::$nameRoute = array_diff_key(self::$nameUrl, $data);
    }

    public static function route(string $route)
    {
        if (isset(self::$nameRoute[$route])) {
            $link = base_url . self::$nameRoute[$route];
            return  $link;
        } else {
            throw new CronosException('no se encontro la ruta web para el: route(' . $route . ')');
        }
    }
}
