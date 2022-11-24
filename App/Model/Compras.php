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
}
