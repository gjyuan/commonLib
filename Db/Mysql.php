<?php

namespace Db;

class Mysql {

    private static $ins;

    private function __construct() {}

    private function __clone() {}

    private static function createIns($host, $port, $user, $passwd, $dbName) {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8";
        $ins = new \PDO($dsn, $user, $passwd);
        $ins->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $ins->setAttribute(\PDO::ATTR_TIMEOUT, 3);
        return $ins;
    }

    public static function getInstance($host, $port, $user, $passwd, $dbName) {
        $key = $host."_".$port;
        if(empty(self::$ins)) {
            self::$ins = array();
        }
        if(!isset(self::$ins[$key])) {
            self::$ins[$key] = self::createIns($host, $port, $user, $passwd, $dbName);
        }
        return self::$ins[$key];
    } 
}

