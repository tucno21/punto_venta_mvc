<?php

/**
 * middleware es un archivo que filtra las peticiones HTTP en un sistema, 
 * es un archivo adicional que va en el medio 
 * de la petición y de eso que se quiere ver como resultado final
 */

namespace System;

use System\Request;

class Middleware
{
    /**
     * filtra las peticiones HTTP en un sistema mediante el archivo Request.php
     */
    protected Request $request;

    public static $exceptBool = false;
    public static $except = [];

    public function __construct()
    {
        $this->request = new Request();
    }

    public static function run(string $session)
    {
        if ($session == 'auth') {
            if (self::$exceptBool === false) {
                if (!session()->has()) {
                    header("Location: /");
                }
            } else {
                $request = new Request();
                $url = $request->getPath();
                $url = base_url . $url;

                $exceptUrl = [];
                foreach (self::$except as $key => $value) {
                    $data =  route($value);
                    array_push($exceptUrl, $data);
                }

                if (!session()->has() && !in_array($url, $exceptUrl)) {
                    header("Location: /");
                }
            }
        }

        if ($session !== 'auth') {

            if (self::$exceptBool === false) {
                if (!session()->has($session)) {
                    header("Location: /");
                }
            } else {
                $request = new Request();
                $url = $request->getPath();
                $url = base_url . $url;

                $exceptUrl = [];
                foreach (self::$except as $key => $value) {
                    $data =  route($value);
                    array_push($exceptUrl, $data);
                }

                if (!session()->has($session) && !in_array($url, $exceptUrl)) {
                    header("Location: /");
                }
            }
        }
    }

    public static function except(array $except)
    {
        self::$exceptBool = true;
        self::$except = $except;
    }
}
