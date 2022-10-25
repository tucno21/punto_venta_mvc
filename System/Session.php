<?php

namespace System;

/**
 * Crear sesiones mediante objetos usado en el controlador
 */

class Session
{
    const SESSION = 'catuva';
    const FLASH_SESSION = 'flashSession';

    public function __construct()
    {
        /**
         * verificar si existe una sesion
         * si no existe crea una
         */
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * crear una sesion mediante una clave y  array de la base de datos
     */
    public function set(string $key, array|object $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * crear una sesion mediante array de la base de datos
     */
    public function attempt(array|object $value)
    {
        $_SESSION[self::SESSION] = $value;
    }

    /**
     * funcion para obtener una sesion 'get'
     */
    public function get(string $key = '')
    {
        if (isset($_SESSION[self::FLASH_SESSION])) {
            if (array_key_exists($key, $_SESSION[self::FLASH_SESSION])) {
                return $_SESSION[self::FLASH_SESSION][$key];
            }
        }
        return $this->sessionGet($key);
    }

    /**
     * funcion para obtener una sesion 'user' como laravel
     */
    public function user(string $key = '')
    {
        return $this->sessionGet($key);
    }

    private function sessionGet(string $key = '')
    {
        if (empty($key)) {
            if (isset($_SESSION[self::SESSION])) {
                return RESULT_TYPE === 'array' ? $_SESSION[self::SESSION] : (object) $_SESSION[self::SESSION];
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$key])) {
                return RESULT_TYPE === 'array' ? $_SESSION[$key] : (object)$_SESSION[$key];
            } else {
                return false;
            }
        }
    }

    /**
     * todas las sesiones creadas
     */
    public function all()
    {
        return RESULT_TYPE === 'array' ? $_SESSION : (object) $_SESSION;
    }

    /**
     * Verifica si el existe una session
     */
    public function has(string $key = ''): bool
    {
        if (isset($_SESSION[self::FLASH_SESSION])) {
            if (array_key_exists($key, $_SESSION[self::FLASH_SESSION])) {
                return isset($_SESSION[self::FLASH_SESSION][$key]);
            }
        }

        if (empty($key)) {
            return isset($_SESSION[self::SESSION]);
        } else {
            return isset($_SESSION[$key]);
        }
    }


    public function exists(string $key = ''): bool
    {
        if (empty($key)) {
            return isset($_SESSION[self::SESSION]);
        } else {
            return isset($_SESSION[$key]);
        }
    }


    /**
     * remover una sesion con la 'remove'
     */
    public function remove(string $key = '')
    {
        $this->sessionUnset($key);
    }

    /**
     * remover una sesion con la 'logout'
     */
    public function logout(string $key = '')
    {
        $this->sessionUnset($key);
    }

    /**
     * remover una sesion con la 'forget'
     */
    public function forget(string $key)
    {
        $this->sessionUnset($key);
    }

    private function sessionUnset(string $key = '')
    {
        if (empty($key)) {
            unset($_SESSION[self::SESSION]);
        } else {
            unset($_SESSION[$key]);
        }
    }

    /**
     * remover todas las sesiones
     */
    public function flush()
    {
        session_destroy();
    }

    /**
     * session flash
     */
    public function flash(string $key, string $value)
    {
        $_SESSION[self::FLASH_SESSION][$key] = $value;
    }

    /**
     * session set csrf
     */
    public function setCsrf(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * session get csrf
     */
    public function getCsrf(string $key)
    {
        return $_SESSION[$key];
    }
}
