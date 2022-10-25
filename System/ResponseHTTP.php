<?php

namespace System;

/** 
 * verificar la web si es 404
 */

class ResponseHTTP
{
    public function setStatusCode(int $code)
    {
        //es una funcion de php que devuelve el codigo de respuesta de la web
        http_response_code($code);
    }
}
