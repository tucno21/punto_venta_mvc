<?php

namespace App\Model;

use System\Model;

class CuentasCobrar extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'abonos';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['venta_id', 'user_id', 'monto', 'fecha'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = false;

    public static function abonoTotalVentaId()
    {
        //ORDER BY id DESC
        $sql = "SELECT SUM(monto) as total,  venta_id
                FROM abonos
                GROUP BY venta_id";
        return self::querySimple($sql);
    }

    public static function abonosVentaId($id)
    {
        //ORDER BY id DESC
        $sql = "SELECT * FROM abonos WHERE venta_id = $id";
        return self::querySimple($sql);
    }

    public static function TotalAbonos($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        $sql = "SELECT SUM(monto) as total
                FROM abonos
                WHERE fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre' AND user_id = $usuarioCaja";
        return self::querySimple($sql);
    }

    public static function abonosGenerados($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        $sql = "SELECT a.*, v.serie, v.correlativo
                FROM abonos a
                INNER JOIN ventas v ON v.id = a.venta_id
                WHERE a.fecha BETWEEN '$fecha_apertura' AND '$fecha_cierre' AND a.user_id = $usuarioCaja";
        return self::querySimple($sql);
    }
}
