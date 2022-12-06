<?php

namespace App\Model;

use System\Model;

class Ventas extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'ventas';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['usuario_id', 'tipodoc', 'nombre_tipodoc', 'serie_id', 'serie', 'correlativo', 'moneda', 'fecha_emision', 'op_gratuitas', 'op_exoneradas', 'op_inafectas', 'op_gravadas', 'igv_gratuita', 'igv_exonerada', 'igv_inafecta', 'igv_grabada', 'igv_total', 'total', 'cliente_id', 'nombre_xml', 'forma_pago', 'cuotas', 'estado', 'estado_sunat', 'productos'];
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

    public static function getVenta($id)
    {
        // INNER JOIN
        $sql = "SELECT v.*, u.name as vendedor
                FROM ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                WHERE v.id = $id";
        return self::querySimple($sql);
    }

    public static function getVentas()
    {
        // INNER JOIN users y clientes
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente
                FROM ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                ORDER BY v.id DESC";
        return self::querySimple($sql);
    }

    public static function getVentaNota($id)
    {
        $sql = "SELECT v.*, c.nombre as cliente
        FROM ventas v
        INNER JOIN clientes c ON c.id = v.cliente_id
        WHERE v.id = $id";
        return self::querySimple($sql);
    }

    public static function search($search)
    {
        ///buscar por "serie-correlativo" estado = 1
        $sql = "SELECT v.*, c.nombre as cliente
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE CONCAT(v.serie, '-', v.correlativo) LIKE '%$search%'
                AND v.estado = 1
                AND v.estado_sunat = 1
                ORDER BY v.id DESC";
        return self::querySimple($sql);
    }
}
