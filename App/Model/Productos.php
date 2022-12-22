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

    public static function getProducto($id)
    {
        $sql = "SELECT p.*,  c.nombre as categoria, u.descripcion as unidad, t.descripcion as tipo_afectacion, A.name as usuario 
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                INNER JOIN unidades u ON u.id = p.unidad_id
                INNER JOIN tipo_afectacion t ON t.id = p.tipo_afectacion_id
                INNER JOIN users A ON A.id = p.user_id
                WHERE p.id = $id";
        return self::querySimple($sql);
    }

    public static function productoCode($code)
    {
        //estado = 1 , tipo_afectacion_id , stock > 0
        $sql = "SELECT p.*,  t.codigo as codigo_afectacion_alt, t.codigo_afectacion, t.nombre_afectacion, t.tipo_afectacion, u.codigo as unidad 
                FROM productos p
                INNER JOIN tipo_afectacion t ON t.id = p.tipo_afectacion_id
                INNER JOIN unidades u ON u.id = p.unidad_id
                WHERE p.codigo = '$code' AND p.estado = 1 AND p.stock > 0";
        return self::querySimple($sql);
    }

    public static function search($search)
    {
        //estado = 1 , tipo_afectacion_id , stock > 0
        $sql = "SELECT p.*,  t.codigo as codigo_afectacion_alt, t.codigo_afectacion, t.nombre_afectacion, t.tipo_afectacion, u.codigo as unidad 
                FROM productos p
                INNER JOIN tipo_afectacion t ON t.id = p.tipo_afectacion_id
                INNER JOIN unidades u ON u.id = p.unidad_id
                WHERE p.estado = 1 AND p.stock > 0 AND p.detalle LIKE '%$search%' OR p.codigo LIKE '%$search%'
                ORDER BY p.id DESC";

        return self::querySimple($sql);
    }
    public static function productoCodeCompra($code)
    {
        //estado = 1 , tipo_afectacion_id , stock > 0
        $sql = "SELECT p.*,  t.codigo as codigo_afectacion_alt, t.codigo_afectacion, t.nombre_afectacion, t.tipo_afectacion, u.codigo as unidad 
                FROM productos p
                INNER JOIN tipo_afectacion t ON t.id = p.tipo_afectacion_id
                INNER JOIN unidades u ON u.id = p.unidad_id
                WHERE p.codigo = '$code' AND p.estado = 1";
        return self::querySimple($sql);
    }

    public static function searchCompra($search)
    {
        //estado = 1 , tipo_afectacion_id , stock > 0
        $sql = "SELECT p.*,  t.codigo as codigo_afectacion_alt, t.codigo_afectacion, t.nombre_afectacion, t.tipo_afectacion, u.codigo as unidad 
                FROM productos p
                INNER JOIN tipo_afectacion t ON t.id = p.tipo_afectacion_id
                INNER JOIN unidades u ON u.id = p.unidad_id
                WHERE p.estado = 1 AND p.detalle LIKE '%$search%' OR p.codigo LIKE '%$search%'
                ORDER BY p.id DESC";

        return self::querySimple($sql);
    }

    public static function getProd($id)
    {
        $sql = "SELECT p.* 
                FROM productos p
                WHERE p.id = $id";
        return self::querySimple($sql);
    }

    public static function productoCodeKardex($code)
    {
        //estado = 1 , tipo_afectacion_id , stock > 0
        $sql = "SELECT id, codigo 
                FROM productos 
                WHERE codigo = '$code'";
        return self::querySimple($sql);
    }

    public static function searchKardex($search)
    {
        $sql = "SELECT id, codigo, detalle 
                FROM productos
                WHERE detalle LIKE '%$search%' OR codigo LIKE '%$search%'
                ORDER BY id DESC";

        return self::querySimple($sql);
    }

    public static function stockDebajoMinimo()
    {
        // stock debajo del minimo pero no cero
        $sql = "SELECT p.id, p.codigo, p.detalle ,p.stock, p.stock_minimo,  c.nombre as categoria 
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.stock <= p.stock_minimo AND p.stock > 0 AND p.estado = 1
                ORDER BY p.id DESC";
        return self::querySimple($sql);
    }

    public static function stockCero()
    {
        // stock cero
        $sql = "SELECT p.id, p.codigo, p.detalle ,p.stock, p.stock_minimo,  c.nombre as categoria 
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.stock = 0 AND p.estado = 1
                ORDER BY p.id DESC";
        return self::querySimple($sql);
    }

    public static function getCodigoProductos()
    {
        $sql = "SELECT codigo FROM productos";
        return self::querySimple($sql);
    }

    public static function insertProductos($dataProductos)
    {
        // $sql = "INSERT INTO productos (codigo, detalle, categoria_id, unidad_id, tipo_afectacion_id, stock, stock_minimo, precio_compra, precio_venta, estado, user_id) 
        //         VALUES (:codigo, :detalle, :categoria_id, :unidad_id, :tipo_afectacion_id, :stock, :stock_minimo, :precio_compra, :precio_venta, :estado, :user_id)";
        $sql = "INSERT INTO `productos` (`codigo`, `detalle`,`precio_compra`,`precio_venta`,`stock`, `stock_minimo`,`categoria_id`,`unidad_id`,`tipo_afectacion_id`,`user_id`,`created_at`) VALUES ";
        foreach ($dataProductos as $k => $v) {
            //sanitizar
            $v['codigo'] = filter_var($v['codigo'], FILTER_UNSAFE_RAW);
            $v['detalle'] = filter_var($v['detalle'], FILTER_UNSAFE_RAW);
            $v['precio_compra'] = filter_var($v['precio_compra'], FILTER_UNSAFE_RAW);
            $v['precio_venta'] = filter_var($v['precio_venta'], FILTER_UNSAFE_RAW);
            $v['stock'] = filter_var($v['stock'], FILTER_UNSAFE_RAW);
            $v['stock_minimo'] = filter_var($v['stock_minimo'], FILTER_UNSAFE_RAW);
            $v['categoria_id'] = filter_var($v['categoria_id'], FILTER_UNSAFE_RAW);
            $v['unidad_id'] = filter_var($v['unidad_id'], FILTER_UNSAFE_RAW);
            $v['tipo_afectacion_id'] = filter_var($v['tipo_afectacion_id'], FILTER_UNSAFE_RAW);
            $v['user_id'] = filter_var($v['user_id'], FILTER_UNSAFE_RAW);
            $v['created_at'] = filter_var($v['created_at'], FILTER_UNSAFE_RAW);

            $sql .= "('{$v['codigo']}','{$v['detalle']}','{$v['precio_compra']}','{$v['precio_venta']}','{$v['stock']}','{$v['stock_minimo']}','{$v['categoria_id']}','{$v['unidad_id']}','{$v['tipo_afectacion_id']}','{$v['user_id']}','{$v['created_at']}'),";
        }
        $sql = substr($sql, 0, -1);

        return self::querySimple($sql);
    }

    public static function disminuirStock($productos)
    {
        //disminir el stock actual array de productos
        $sql = "UPDATE productos SET stock = CASE id ";
        foreach ($productos as $k => $v) {
            $sql .= "WHEN {$v->id} THEN stock - {$v->cantidad} ";
        }
        $sql .= "END WHERE id IN(";
        foreach ($productos as $k => $v) {
            $sql .= $v->id . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ')';

        $result = self::execute($sql);

        return $result > 0 ? true : false;
    }
}
