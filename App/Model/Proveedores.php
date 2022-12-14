<?php

namespace App\Model;

use System\Model;

class Proveedores extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'proveedores';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['documento', 'nombre', 'direccion', 'telefono', 'estado'];
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

    public static function getBuscar($proveedores)
    {
        //solo con estado = 1
        $sql = "SELECT * FROM proveedores WHERE estado = 1 AND (documento LIKE '%{$proveedores}%' OR nombre LIKE '%{$proveedores}%') ORDER BY nombre ASC";

        return self::querySimple($sql);
    }
}
