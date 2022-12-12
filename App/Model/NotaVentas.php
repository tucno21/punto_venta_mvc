<?php

namespace App\Model;

use System\Model;

class NotaVentas extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'nota_ventas';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['usuario_id', 'tipodoc', 'nombre_tipodoc', 'serie_id', 'serie', 'correlativo', 'moneda', 'fecha_emision', 'op_gratuitas', 'op_exoneradas', 'op_inafectas', 'op_gravadas', 'igv_gratuita', 'igv_exonerada', 'igv_inafecta', 'igv_grabada', 'igv_total', 'total', 'cliente_id', 'estado', 'estado_sunat', 'forma_pago', 'productos', 'venta_id'];
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

    public static function getVentas()
    {
        // INNER JOIN users y clientes
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente, c.documento as documentocliente
                FROM nota_ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                ORDER BY v.id DESC";
        return self::querySimple($sql);
    }

    public static function getVenta($id)
    {
        // INNER JOIN
        $sql = "SELECT v.*, u.name as vendedor
                FROM nota_ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                WHERE v.id = $id";
        return self::querySimple($sql);
    }

    public static function getVentaId($id)
    {
        $sql = "SELECT * FROM nota_ventas WHERE id = $id";
        return self::querySimple($sql);
    }

    public static function TotalVentas($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        $sql = "SELECT SUM(total) as total 
                FROM nota_ventas 
                WHERE fecha_emision BETWEEN '$fecha_apertura' AND '$fecha_cierre'
                AND estado_sunat = 0
                AND usuario_id = $usuarioCaja";
        return self::querySimple($sql);
    }

    public static function ventasGeneradas($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        $sql = "SELECT v.*, c.nombre as cliente
                FROM nota_ventas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE fecha_emision BETWEEN '$fecha_apertura' AND '$fecha_cierre'
                AND v.estado_sunat = 0
                AND v.usuario_id = $usuarioCaja
                ORDER BY v.id DESC";
        return self::querySimple($sql);
    }

    public static function getVentasFechas($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente, c.documento as documentocliente
                FROM nota_ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
                ORDER BY v.id ASC";
        return self::querySimple($sql);
    }
}
