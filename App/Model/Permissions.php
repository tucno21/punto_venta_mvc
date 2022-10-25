<?php

namespace App\Model;

use System\Model;

class Permissions extends Model
{
    /**
     * nombre de la tabla
     */
    protected static $table       = 'permissions';
    /**
     * nombre primary key
     */
    protected static $primaryKey  = 'id';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = ['per_name', 'description'];
    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'object';
    /**
     * puede eliminar si desea estos campos si no lo rquiere
     * si desea que el framework registre las fechas para la creacion o  actualizacion
     */
    protected static $useTimestamps   = true;
    /**
     * $createdField debe ser DATETIME o TIMESTAMPS con condicion null
     * $$updatedField debe ser TIMESTAMPS con condicion null
     * el framework se encarga de enviar las fechas y no BD
     * colocar el nombre de los campos de fecha de la BD
     */
    protected static $createdField    = 'created_at';
    protected static $updatedField    = 'updated_at';


    public static function permisosRol($id)
    {
        $sql = "SELECT permissions.id, permissions.per_name FROM rol_permission INNER JOIN roles ON rol_permission.rol_id = roles.id INNER JOIN permissions ON rol_permission.permission_id = permissions.id WHERE roles.id = $id";

        return self::querySimple($sql);
    }

    public static function sync($id, $data)
    {
        //query para eliminar los permisos del permisos del rol
        $sql = "DELETE FROM `rol_permission` WHERE rol_id = $id";
        self::querySimple($sql);

        //query para insertar los permisos del rol
        //INSERT INTO `rol_permission` (`rol_id`, `permission_id`) VALUES ('1', '1'), ('1', '2'), ('1', '3');
        $sql2 = "INSERT INTO `rol_permission` (`rol_id`, `permission_id`) VALUES ";

        $permisos = $data;

        foreach ($permisos as $key => $value) {
            $sql2 .= "($id, $value),";
        }

        $sql2 = substr($sql2, 0, -1);

        self::querySimple($sql2);
    }
}
