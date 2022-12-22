<?php

namespace App\Model;

use System\Model;

class Inventarios extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'inventario';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['producto_id', 'comprobante', 'cantidad', 'fecha', 'tipo', 'accion', 'stock_actual', 'user_id'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * si hay un campo de contraseña cifrar (true/false)
     */
    protected static $passEncrypt = false;

    protected static $useTimestamps   = false;

    public static function getInventarios()
    {
        $sql = "SELECT i.*, p.detalle as producto, p.codigo as codigo
                FROM inventario i
                INNER JOIN productos p ON p.id = i.producto_id
                ORDER BY i.id DESC";
        return self::querySimple($sql);
    }

    public static function getInventarioComprobante($comprobante)
    {
        $sql = "SELECT * FROM inventario WHERE comprobante = '$comprobante'";
        return self::querySimple($sql);
    }

    public static function getInventarioMes($mes, $ano)
    {
        $sql = "SELECT i.*, p.detalle as producto, p.codigo as codigo
                FROM inventario i
                INNER JOIN productos p ON p.id = i.producto_id
                WHERE MONTH(i.fecha) = '$mes' AND YEAR(i.fecha) = '$ano'
                ORDER BY i.id DESC";
        return self::querySimple($sql);
    }

    public static function getKardex($productoid)
    {
        $sql = "SELECT i.*, p.detalle as producto, p.codigo as codigo
                FROM inventario i
                INNER JOIN productos p ON p.id = i.producto_id
                WHERE i.producto_id = $productoid";
        return self::querySimple($sql);
    }

    public static function getInventarioFecha($productoID, $fechaInicio, $fechaFin)
    {
        $sql = "SELECT i.*, p.detalle as producto, p.codigo as codigo
                FROM inventario i
                INNER JOIN productos p ON p.id = i.producto_id
                WHERE i.producto_id = $productoID AND i.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        return self::querySimple($sql);
    }

    public static function registrarMovimientos($nuevoInventario)
    {
        $sql = "INSERT INTO inventario (producto_id, comprobante, cantidad, fecha, tipo, accion, stock_actual, user_id) VALUES ";
        foreach ($nuevoInventario as $key => $value) {
            $sql .= "({$value['producto_id']}, '{$value['comprobante']}', {$value['cantidad']}, '{$value['fecha']}', '{$value['tipo']}', '{$value['accion']}', {$value['stock_actual']}, {$value['user_id']}),";
        }
        $sql = substr($sql, 0, -1);

        $result = self::execute($sql);
        return $result > 0 ? true : false;
    }
}
