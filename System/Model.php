<?php

/**
 * ActiveRecord para extender en los modelos
 */

namespace System;

use PDO;
use System\ConnectDB;


class Model
{
    /**
     * conexion a la base de datos
     */
    protected static $db;
    /**
     * nombre de la tabla
     */
    protected static $table = '';

    /**
     * nombre del primaryKey
     */
    protected static $primaryKey  = '';
    /**
     * nombre de la columnas de la tabla
     */
    protected static $allowedFields = [];

    /**
     * obtener los datos de la tabla en 'array' u 'object'
     */
    protected static $returnType     = 'array';
    /**
     * constructor de la clase
     */
    protected static $where = null;
    protected static $orderBy = null;
    protected static $select = null;
    protected static $join = null;
    protected static $woj = false;
    /**
     * si existe columna password para encriptar
     */
    protected static $passEncrypt = false;
    protected static $password =  null;

    /**
     * registro de tiempos de creacion y/o actualizacion
     */
    protected static $useTimestamps   = false;
    protected static $createdField    = null;
    protected static $updatedField    = null;

    /**
     * query para MySQL
     */
    protected static $query;
    protected static $bindparam = [];

    /**
     * constructor de la clase
     */
    public function __construct()
    {
        // $database = new ConnectDB();
        self::$db = ConnectDB::conn();
    }

    /**
     * obtener la conexion a la base de datos
     */

    private static function getConnection()
    {
        //instanciar toda la clase para invocar al constructor
        return new static;
    }

    /**
     * cerrar la coneccion a la base de datos
     */
    private static function db_close()
    {
        self::$db = null;
    }

    /**
     * relaciona el array  que biene del controlador con la tabla del modelo creado
     * no toma en cuenta los imputs que no existan en la tabla
     */
    protected static function allowedFields(array $data)
    {
        $fields = [];
        foreach (self::$allowedFields as $val) {
            if (isset($data[$val])) {
                $fields[$val] = $data[$val];
            }
        }

        return $fields;
    }

    /**
     * ejecutar query de CREATE, UPDATE, DELETE
     */
    protected static function tableChange($send)
    {
        try {
            $stmt = self::$db->prepare(self::$query);
            $status = $stmt->execute($send);
            $count = $stmt->rowCount();
            $id = self::$db->lastInsertId();
            self::db_close();

            if ($id != 0) {
                $return = ['status' =>  $status, 'id' => $id];
            } else if ($count === 0) {
                $return = ['status' => false];
            } else {
                $return = ['status' => $status];
            }

            if ($id != 0) {
                if (self::$returnType == 'object') {
                    return (object) $return;
                }
                return $return;
            }

            if ($count === 0) {
                if (self::$returnType == 'object') {
                    return (object) $return;
                }
                return $return;
            }

            if (self::$returnType == 'object') {
                return (object) $return;
            }
            return $return;
        } catch (\PDOException $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * iniciar funciones de bd
     */
    protected static function startBd($data = null)
    {
        self::setTable();
        self::getConnection();

        if ($data != null) {
            if (is_object($data))
                $data = (array)$data;
            $send = self::allowedFields($data);

            if (self::$passEncrypt === true) {
                if (isset($send[self::$password])) {
                    $send[self::$password] = password_hash($send[self::$password], PASSWORD_BCRYPT);
                }
            }
        } else {
            $send = null;
        }

        return $send;
    }

    /**
     * crear un nuevo registro
     * https://diego.com.es/sentencias-preparadas-en-php
     */
    public static function create(array|object $data)
    {
        $send = self::startBd($data);

        if (self::$useTimestamps == true) {
            $send[self::$createdField] = date('Y-m-d H:i:s');
        }

        $colum = implode(', ', array_keys($send));
        $value = implode(', :', array_keys($send));

        self::$query = 'INSERT INTO ' . self::$table . ' (' . $colum . ') VALUES (:' . $value . ')';

        return self::tableChange($send);
    }


    /**
     * actualizar un registro
     */
    public static function update(mixed $id, $data)
    {
        $send = self::startBd($data);

        if (self::$useTimestamps == true) {
            $send[self::$updatedField] = date('Y-m-d H:i:s');
        }

        $query = 'UPDATE ' . self::$table . ' SET ';
        $i = 0;
        foreach ($send as $key => $value) {
            if ($i == 0) {
                $query .= $key . ' = :' . $key;
            } else {
                $query .= ', ' . $key . ' = :' . $key;
            }
            $i++;
        }

        $query .= ' WHERE ' . self::$primaryKey . ' = :' . self::$primaryKey;

        self::$query = $query;

        $send[self::$primaryKey] = $id;

        return self::tableChange($send);
    }

    /**
     * eliminar un registro
     */
    public static function delete(mixed $id)
    {
        self::startBd();

        self::$query = 'DELETE FROM ' . self::$table . ' WHERE ' . self::$primaryKey . ' = :' . self::$primaryKey;

        $send = [':' . self::$primaryKey => $id];

        return self::tableChange($send);
    }


    /**
     * recibe el query y retorna los resultados
     */
    private static function readDB($data = null)
    {
        try {
            $stmt = self::$db->prepare(self::$query);
            $stmt->execute($data);
            if (self::$returnType == 'object') {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                self::db_close();

                if (count($result) == 1) {
                    return $result[0];
                }
                return $result;
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            self::db_close();

            if (count($result) == 1) {
                return $result[0];
            }
            return $result;
        } catch (\PDOException $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    /**
     * obtener todos los resultados
     */
    public static function all()
    {
        self::startBd();
        self::$query = 'SELECT * FROM ' . self::$table;
        return self::readDB();
    }

    /**
     * obtener un solo resultado
     */
    public static function find($id)
    {
        self::startBd();
        self::$query = 'SELECT * FROM ' . self::$table . ' WHERE ' . self::$primaryKey . ' = :' . self::$primaryKey;
        $send = [':' . self::$primaryKey => $id];
        return self::readDB($send);
    }

    /**
     * buscar una fila por su (id o nombre de columna, valor de columna)
     */
    public static function first(mixed $colum = null, mixed $valColum = null)
    {
        if (self::$woj == false)
            self::startBd();

        if ($colum != null && $valColum != null) {
            self::$query = 'SELECT * FROM ' . self::$table . ' WHERE ' . $colum . ' = :' . $colum . " LIMIT 1";
            $send = [':' . $colum => $valColum];
        } elseif ($colum != null) {
            $primaryKey = static::$primaryKey;
            self::$query = 'SELECT * FROM ' . self::$table . ' WHERE ' . $primaryKey . ' = :' . $primaryKey . " LIMIT 1";
            $send = [':' . $primaryKey => $colum];
        } else {
            self::$query = "SELECT * FROM " . static::$table . self::$where . self::$orderBy . " LIMIT 1";
            $send = self::$bindparam;
        }

        // return self::$query;
        // return $send;
        return self::readDB($send);
    }

    /**
     * traer todos los resultados de una query con la combinacion de otras funciones
     */
    public static function get()
    {
        if (self::$woj == false)
            self::startBd();

        if (self::$select != null) {
            self::$query = 'SELECT ' . self::$select . ' FROM ' . self::$table . self::$join . self::$where . self::$orderBy;
        } else {
            self::$query = 'SELECT * FROM ' . self::$table . self::$where . self::$orderBy;
        }
        $send = self::$bindparam;

        return self::readDB($send);
    }

    /**
     * posibles combinaciones con get(), first();
     */
    /**
     * selecionar las columnas que se desea buscar
     */
    public static function select($colum_a = null, $colum_b = null, $colum_c = null, $colum_d = null, $colum_e = null, $colum_f = null, $colum_g = null)
    {
        if ($colum_a != null) {

            $colum_b = is_null($colum_b) ? "" : ", $colum_b";
            $colum_c = is_null($colum_c) ? "" : ", $colum_c";
            $colum_d = is_null($colum_d) ? "" : ", $colum_d";
            $colum_e = is_null($colum_e) ? "" : ", $colum_e";
            $colum_f = is_null($colum_f) ? "" : ", $colum_f";
            $colum_g = is_null($colum_g) ? "" : ", $colum_g";

            self::$select = "$colum_a$colum_b$colum_c$colum_d$colum_e$colum_f$colum_g";
        }
        return new static;
    }

    /**
     * busqueda con condicion where
     */
    public static function where(string $colum, string $operator = null, mixed $valueColum = null)
    {
        self::$woj = true;
        self::startBd();

        if ($operator != null && $valueColum != null) {
            self::$where  = ' WHERE ' . $colum . ' ' . $operator . ' :' . $colum;
            self::$bindparam[':' . $colum] = $valueColum;
        } else {
            self::$where = ' WHERE ' . $colum . ' = :' . $colum;
            self::$bindparam[':' . $colum] = $operator;
        }

        return new static;
    }

    /**
     * ordenar los resultados
     * en funcion al campo y el orden
     */
    public static function orderBy(string $colum, string $order)
    {
        self::$woj = true;
        self::startBd();
        self::$orderBy = ' ORDER BY ' . $colum . ' ' . $order;
        return new static;
    }

    /**
     * recuperar datos de varias tablas al mismo tiempo.
     */
    public static function join($table, $colum, $operator, $colum2)
    {
        self::$woj = true;
        self::startBd();
        self::$join = " INNER JOIN $table ON $colum $operator $colum2";
        return new static;
    }

    /**
     * obtener una cantidad de resultados que se tiene de la consulta
     */
    public static function count()
    {
        if (self::$woj == false)
            self::startBd();
        self::$query = 'SELECT * FROM ' . self::$table . self::$where . self::$orderBy;

        $send = self::$bindparam;

        return count(self::readDB($send));
    }

    /**
     * obtener una cantidad MAX de la columna
     */
    public static function max(string $colum)
    {
        if (self::$woj == false)
            self::startBd();
        self::$query = 'SELECT MAX(' . $colum . ') FROM ' . self::$table . self::$where . self::$orderBy;
        $send = self::$bindparam;
        $max = self::readDB($send);

        if (is_array($max)) {
            return $max["MAX($colum)"];
        }

        $array = (array)$max;
        return $array["MAX($colum)"];
    }

    /**
     * obtener una cantidad MIN de la columna
     */
    public static function min(string $colum)
    {
        if (self::$woj == false)
            self::startBd();
        self::$query = 'SELECT MIN(' . $colum . ') FROM ' . self::$table . self::$where . self::$orderBy;
        $send = self::$bindparam;
        $min = self::readDB($send);

        if (is_array($min)) {
            return $min["MIN($colum)"];
        }

        $array = (array)$min;
        return $array["MIN($colum)"];
    }

    /**
     * obtener el promedio de la columna
     */
    public static function avg(string $colum)
    {
        if (self::$woj == false)
            self::startBd();
        self::$query = 'SELECT AVG(' . $colum . ') FROM ' . self::$table . self::$where . self::$orderBy;
        $send = self::$bindparam;
        $avg = self::readDB($send);

        if (is_array($avg)) {
            return $avg["AVG($colum)"];
        }

        $array = (array)$avg;
        return $array["AVG($colum)"];
    }

    /**
     * obtener una cantidad SUM de la columna
     */
    public static function sum(string $colum)
    {
        if (self::$woj == false)
            self::startBd();
        self::$query = 'SELECT SUM(' . $colum . ') FROM ' . self::$table . self::$where . self::$orderBy;
        $send = self::$bindparam;
        $sum = self::readDB($send);

        if (is_array($sum)) {
            return $sum["SUM($colum)"];
        }

        $array = (array)$sum;
        return $array["SUM($colum)"];
    }


    /**
     * pasar el nombre de la tabla
     */
    private static function setTable()
    {
        self::$table = static::$table;
        self::$primaryKey = static::$primaryKey;
        self::$allowedFields = static::$allowedFields;

        /**
         * verificar si existe returnType en el modelo hijo y pasar el valor a la variable
         * del modelo padre
         */
        if (property_exists(static::class, 'returnType')) {
            self::$returnType = static::$returnType;
        }

        /**
         * verifica si existe los columanas useTimestamps
         */
        if (property_exists(static::class, 'useTimestamps')) {
            self::$useTimestamps = static::$useTimestamps;

            if (self::$useTimestamps) {
                if (property_exists(static::class, 'createdField')) {
                    self::$createdField = static::$createdField;
                }

                if (property_exists(static::class, 'updatedField')) {
                    self::$updatedField = static::$updatedField;
                }
            }
        }

        /**
         * encriptar la columna contraseña
         */
        if (property_exists(static::class, 'passEncrypt')) {
            self::$passEncrypt = static::$passEncrypt;
            if (self::$passEncrypt) {
                self::$password = static::$password;
            }
        }
    }

    /**
     * JQUERY PERSONALIZADO
     */
    //RECIVE UN QUERY Y ENVIA GRUPOS DE OBJETOS
    public static function query($query, $data)
    {
        self::startBd();
        self::$query = $query;
        return self::readDB($data);
    }

    //RECIVE SOLO QUERY
    public static function querySimple($query)
    {
        self::startBd();
        self::$query = $query;
        return self::readDB();
    }
}
