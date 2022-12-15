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

    public static function TotalVentas($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        //estado = 1
        $sql = "SELECT SUM(total) as total
                FROM ventas
                WHERE fecha_emision BETWEEN '$fecha_apertura' AND '$fecha_cierre'
                AND estado = 1
                AND usuario_id = $usuarioCaja";
        return self::querySimple($sql);
    }

    public static function ventasGeneradas($fecha_apertura, $fecha_cierre, $usuarioCaja)
    {
        //estado = 1
        $sql = "SELECT v.*, c.nombre as cliente
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE fecha_emision BETWEEN '$fecha_apertura' AND '$fecha_cierre'
                AND v.estado = 1
                AND v.usuario_id = $usuarioCaja
                ORDER BY v.id DESC";

        return self::querySimple($sql);
    }

    public static function getVentasFechas($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT v.*, u.name as vendedor, c.nombre as cliente, c.documento as documentocliente
                FROM ventas v
                INNER JOIN users u ON u.id = v.usuario_id
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
                ORDER BY v.id ASC";

        return self::querySimple($sql);
    }

    public static function ventaTotalPorMes()
    {
        // $sql = "SELECT SUM(total) as total, MONTH(fecha_emision) as mes
        //         FROM ventas
        //         WHERE estado = 1
        //         GROUP BY MONTH(fecha_emision)";
        // $sql = "SELECT MONTH(fecha_emision) as mes, SUM(total) as total
        // FROM ventas
        // WHERE estado = 1
        // AND estado_sunat = 1
        // GROUP BY MONTH(fecha_emision)";

        // $sql = "SELECT SUM(total) as total, MONTH(fecha_emision) as mes
        //         FROM ventas
        //         WHERE estado = 1 AND YEAR(fecha_emision) = YEAR(NOW())
        //         GROUP BY MONTH(fecha_emision)";
        //xodos los meses del año
        $sql = "SELECT SUM(IF(MONTH(fecha_emision) = 1, total,0)) AS ene,
                SUM(IF(MONTH(fecha_emision) = 2, total,0)) AS feb,
                SUM(IF(MONTH(fecha_emision) = 3, total,0)) AS mar,
                SUM(IF(MONTH(fecha_emision) = 4, total,0)) AS abr,
                SUM(IF(MONTH(fecha_emision) = 5, total,0)) AS may,
                SUM(IF(MONTH(fecha_emision) = 6, total,0)) AS jun,
                SUM(IF(MONTH(fecha_emision) = 7, total,0)) AS jul,
                SUM(IF(MONTH(fecha_emision) = 8, total,0)) AS ago,
                SUM(IF(MONTH(fecha_emision) = 9, total,0)) AS sep,
                SUM(IF(MONTH(fecha_emision) = 10, total,0)) AS oct,
                SUM(IF(MONTH(fecha_emision) = 11, total,0)) AS nov,
                SUM(IF(MONTH(fecha_emision) = 12, total,0)) AS dic
                FROM ventas
                WHERE estado = 1 AND YEAR(fecha_emision) = YEAR(NOW())";
        return self::querySimple($sql);
    }

    public static function getVentaCliente($id)
    {
        $sql = "SELECT v.id, v.nombre_xml, c.nombre as cliente, c.email as email
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE v.id = $id";
        return self::querySimple($sql);
    }
}
