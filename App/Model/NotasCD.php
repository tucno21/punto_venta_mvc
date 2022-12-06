<?php

namespace App\Model;

use System\Model;

class NotasCD extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'notas_cd';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['usuario_id', 'venta_id', 'tipodoc', 'nombre_tipodoc', 'serie_id', 'serie', 'correlativo', 'codmotivo', 'descripcion', 'moneda', 'fecha_emision', 'op_gratuitas', 'op_exoneradas', 'op_inafectas', 'op_gravadas', 'igv_gratuita', 'igv_exonerada', 'igv_inafecta', 'igv_grabada', 'igv_total', 'total', 'cliente_id', 'nombre_xml', 'forma_pago', 'cuotas', 'estado', 'estado_sunat', 'productos', 'tipodoc_ref', 'serie_ref', 'correlativo_ref'];
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

    public static function getNota($id)
    {
        // INNER JOIN
        $sql = "SELECT n.*, u.name as vendedor
                FROM notas_cd n
                INNER JOIN users u ON u.id = n.usuario_id
                WHERE n.id = $id";
        return self::querySimple($sql);
    }

    public static function getNotas()
    {
        // INNER JOIN users y clientes
        $sql = "SELECT n.*, u.name as vendedor, c.nombre as cliente
                FROM notas_cd n
                INNER JOIN users u ON u.id = n.usuario_id
                INNER JOIN clientes c ON c.id = n.cliente_id
                ORDER BY n.id DESC";
        return self::querySimple($sql);
    }

    public static function getNombreXML($id)
    {
        // INNER JOIN users y clientes
        $sql = "SELECT n.nombre_xml
                FROM notas_cd n
                WHERE n.id = $id";
        return self::querySimple($sql);
    }
}
