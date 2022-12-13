<?php

namespace App\Model;

use System\Model;

class Compras extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'compras';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['tipo_comprobante_id', 'serie', 'proveedor_id', 'productos', 'total', 'estado', 'user_id', 'fecha_compra'];
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


    public static function getCompras()
    {
        $sql = "SELECT c.*, tc.descripcion as tipo_comprobante, p.nombre as proveedor 
                FROM compras c
                INNER JOIN tipo_comprobante tc ON tc.id = c.tipo_comprobante_id
                INNER JOIN proveedores p ON p.id = c.proveedor_id
                ORDER BY c.id DESC";
        return self::querySimple($sql);
    }

    public static function getCompra($id)
    {
        $sql = "SELECT c.*, tc.descripcion as tipo_comprobante, p.nombre as proveedor 
                FROM compras c
                INNER JOIN tipo_comprobante tc ON tc.id = c.tipo_comprobante_id
                INNER JOIN proveedores p ON p.id = c.proveedor_id
                WHERE c.id = $id";
        return self::querySimple($sql);
    }

    public static function getComprasFechas($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT c.*, tc.descripcion as tipo_comprobante, p.nombre as proveedor 
                FROM compras c
                INNER JOIN tipo_comprobante tc ON tc.id = c.tipo_comprobante_id
                INNER JOIN proveedores p ON p.id = c.proveedor_id
                WHERE c.fecha_compra BETWEEN '$fecha_inicio' AND '$fecha_fin'
                ORDER BY c.id ASC";
        return self::querySimple($sql);
    }

    public static function comprasTotalPorMes()
    {
        $sql = "SELECT SUM(IF(MONTH(fecha_compra) = 1, total,0)) AS ene,
                SUM(IF(MONTH(fecha_compra) = 2, total,0)) AS feb,
                SUM(IF(MONTH(fecha_compra) = 3, total,0)) AS mar,
                SUM(IF(MONTH(fecha_compra) = 4, total,0)) AS abr,
                SUM(IF(MONTH(fecha_compra) = 5, total,0)) AS may,
                SUM(IF(MONTH(fecha_compra) = 6, total,0)) AS jun,
                SUM(IF(MONTH(fecha_compra) = 7, total,0)) AS jul,
                SUM(IF(MONTH(fecha_compra) = 8, total,0)) AS ago,
                SUM(IF(MONTH(fecha_compra) = 9, total,0)) AS sep,
                SUM(IF(MONTH(fecha_compra) = 10, total,0)) AS oct,
                SUM(IF(MONTH(fecha_compra) = 11, total,0)) AS nov,
                SUM(IF(MONTH(fecha_compra) = 12, total,0)) AS dic
                FROM compras
                WHERE estado = 1 AND YEAR(fecha_compra) = YEAR(NOW())";
        return self::querySimple($sql);
    }
}
