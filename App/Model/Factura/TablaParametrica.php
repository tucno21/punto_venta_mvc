<?php

namespace App\Model\Factura;

use System\Model;

class TablaParametrica extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'tabla_parametrica';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['codigo', 'tipo', 'descripcion', 'estado'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = true;
    /**
     * $createdField debe ser DATETIME o TIMESTAMPS con condicion null
     * $$updatedField debe ser TIMESTAMPS con condicion null
     * el framework se encarga de enviar las fechas y no BD
     * colocar el nombre de los campos de fecha de la BD
     */
    protected static $createdField    = 'created_at';
    protected static $updatedField    = 'updated_at';

    public static function getMotivos($tipoDoc)
    {
        //estado = 1 activo
        $sql = "SELECT * FROM tabla_parametrica WHERE tipo = '$tipoDoc' AND estado = 1";
        return self::querySimple($sql);
    }
}
