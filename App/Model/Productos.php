<?php

namespace App\Model;

use System\Model;

class Productos extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'productos';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['codigo', 'detalle', 'imagen', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'estado', 'user_id', 'categoria_id', 'unidad_id', 'tipo_afectacion_id'];
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

    public static function getProductos()
    {
        $sql = "SELECT p.*,  c.nombre as categoria 
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.id DESC";
        return self::querySimple($sql);
    }
}
