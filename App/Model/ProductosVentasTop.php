<?php

namespace App\Model;

use System\Model;

class ProductosVentasTop extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'productos_top_ventas';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['producto_id', 'cant_ventas'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = false;

    public static function getProducto($producto_id)
    {
        $sql = "SELECT * FROM productos_top_ventas WHERE producto_id = $producto_id";
        return self::querySimple($sql);
    }

    public static function registrar($data)
    {
        $sql = "INSERT INTO productos_top_ventas (producto_id, cant_ventas) VALUES ($data->producto_id, $data->cant_ventas)";
        return self::querySimple($sql);
    }

    public static function actualizar($id, $data)
    {
        $sql = "UPDATE productos_top_ventas SET cant_ventas = $data->cant_ventas WHERE producto_id = $id";
        return self::querySimple($sql);
    }

    public static function getVentasTop()
    {
        //inner join con productos
        $sql = "SELECT productos_top_ventas.*, productos.detalle 
        FROM productos_top_ventas 
        INNER JOIN productos ON productos_top_ventas.producto_id = productos.id 
        ORDER BY cant_ventas DESC LIMIT 5";
        return self::querySimple($sql);
    }
}
