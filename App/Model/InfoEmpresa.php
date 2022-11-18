<?php

namespace App\Model;

use System\Model;

class InfoEmpresa extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'info_empresa';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['tipodoc', 'ruc', 'razon_social', 'nombre_comercial', 'direccion', 'pais', 'departamento', 'provincia', 'distrito', 'ubigeo', 'descripcion', 'telefono', 'email', 'usuario_secundario', 'clave_usuario_secundario', 'logo'];
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
}
