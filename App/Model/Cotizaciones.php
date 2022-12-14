<?php

namespace App\Model;

use System\Model;

class Cotizaciones extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'cotizaciones';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['usuario_id', 'tipodoc', 'nombre_tipodoc', 'serie_id', 'serie', 'correlativo', 'moneda', 'fecha_emision', 'tiempo', 'op_gratuitas', 'op_exoneradas', 'op_inafectas', 'op_gravadas', 'igv_gratuita', 'igv_exonerada', 'igv_inafecta', 'igv_grabada', 'igv_total', 'total', 'cliente_id', 'estado', 'estado_sunat', 'forma_pago', 'productos', 'venta_id'];
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

    public static function getCotizaciones()
    {
        // INNER JOIN users y clientes
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente, c.documento as documentocliente
                FROM cotizaciones v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                ORDER BY v.id DESC";
        return self::querySimple($sql);
    }

    public static function getCotizacion($id)
    {
        // INNER JOIN
        $sql = "SELECT v.*, u.name as vendedor
                FROM cotizaciones v
                INNER JOIN users u ON u.id = v.usuario_id
                WHERE v.id = $id";
        return self::querySimple($sql);
    }

    public static function getCotizacionId($id)
    {
        $sql = "SELECT * FROM cotizaciones WHERE id = $id";
        return self::querySimple($sql);
    }

    public static function getCotizacionesFechas($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente, c.documento as documentocliente
                FROM cotizaciones v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
                ORDER BY v.id ASC";
        return self::querySimple($sql);
    }
}
