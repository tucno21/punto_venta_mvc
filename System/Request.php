<?php

namespace System;

use System\Csrf;
use System\CronosException;

/**
 * captura el metodo web GET Y POST
 */
class Request
{
    /**
     * captura y tipo de HTTP (get-post) y convierte en minuscula
     */
    public function methodWeb()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    /**
     * captura los parametros despues de la url principal
     */
    public function getPath()
    {
        //captura toda la url
        $path = $_SERVER['REQUEST_URI'];
        //traer antes de la ?
        $position = strpos($path, '?');
        if ($position === false) {
            // si no has encontrado la ? devuelve la url
            return $path;
        }
        //si encontrado la ? devuelve la url hasta la ?
        return $path = substr($path, 0, $position);
    }

    public function isGet()
    {
        return $this->methodWeb() === 'get';
    }

    public function isPost()
    {
        return $this->methodWeb() === 'post';
    }

    /**
     * captura los datos de la url mediante el metodo GET y post
     */
    public function getInput()
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            //agregar token
            $token = bin2hex(random_bytes(32));
            session()->setCsrf('_token', $token);
            $data['_token'] = $token;
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }


        try {
            //comprobar que exista el imput token
            if (!isset($data['_token'])) {
                throw new CronosException('no se encontro el token csrf');
            }
        } catch (CronosException $e) {
            echo $e->cronosMessage();
            exit;
        }

        try {
            //validar el token
            if (!Csrf::validateToken($data['_token'])) {
                throw new CronosException('token csrf no valido');
            }
        } catch (CronosException $e) {
            echo $e->cronosMessage();
            exit;
        }

        if (session()->has('_token')) {
            session()->remove('_token');
        }

        //eliminar data['_token']
        unset($data['_token']);

        return RESULT_TYPE === 'array' ? $data : (object)$data;
    }
}
