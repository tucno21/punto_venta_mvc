<?php

namespace App\Model;

use System\Model;

class CajasArqueo extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'arqueo_caja';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['caja_id', 'usuario_id', 'monto_inicial', 'monto_final', 'fecha_apertura', 'fecha_cierre', 'total_venta', 'estado'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = false;

    public static function ultimaCajaUsuario($id)
    {
        $sql = "SELECT estado FROM arqueo_caja WHERE usuario_id = $id ORDER BY id DESC LIMIT 1";
        return self::querySimple($sql);
    }
}
