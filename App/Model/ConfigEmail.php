<?php

namespace App\Model;

use System\Model;

class ConfigEmail extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'config_email';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['servidor', 'correo_servidor', 'contrasena_servidor', 'puerto', 'tipo_protocolo'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = false;
}
