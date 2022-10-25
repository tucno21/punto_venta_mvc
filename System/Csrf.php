<?php

/**
 * manejo de seguridad csrf
 */

namespace System;

class Csrf
{
    /**
     * generar token
     */
    public static function generateToken()
    {
        $token = bin2hex(random_bytes(32));

        session()->setCsrf('_token', $token);

        return $token;
    }

    /**
     * validar token
     */
    public static function validateToken($token)
    {
        if (!isset($token)) {
            return false;
        } else if ($token !== session()->getCsrf('_token')) {
            return false;
        }
        return true;
    }
}
