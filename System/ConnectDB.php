<?php

namespace System;

use PDO;

class ConnectDB
{
    private static $db_host;
    private static $db_user;
    private static $db_pass;
    private static $db_name;
    private static $db_charset;

    public static function dataBase($db_host, $db_user, $db_pass, $db_name, $db_charset)
    {
        self::$db_host = $db_host;
        self::$db_user = $db_user;
        self::$db_pass = $db_pass;
        self::$db_name = $db_name;
        self::$db_charset = $db_charset;
    }

    //conectar a la base de datos PDO
    private static function db_open()
    {
        $dsn = 'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name . ';charset=' . self::$db_charset;

        try {
            $conn =  new PDO($dsn, self::$db_user, self::$db_pass);
            return $conn;
        } catch (\PDOException $e) {
            echo 'La conexión a la base de datos falló: ' . $e->getMessage();
            echo "<br>";
            echo "error de depuración: \n" . (int)$e->getCode();
        }
    }

    public static function conn()
    {
        return self::db_open();
    }
}
