<?php 

namespace Db;

class RedisPool {

    private static $pool;
    private static $currentDb;

    private function __construct() {
    }

    private function __clone() {
    }

    private static function getConnection($host, $port, $timeout) {
        $redis = new \Redis();
        //if(!$redis->pconnect($host, $port, $timeout, "newRedis")) {
        if(!$redis->connect($host, $port, $timeout)) {
            throw new \Exception("redis pconnect error: host is $host, port is $port.");
        }
        return $redis;
    }

    private static function getPoolKey($host, $port, $timeout) {
        return $host."_".$port;
    }

    public static function getRedisHandler($host, $port, $timeout, $db) {
        $key = self::getPoolKey($host, $port, $timeout);

        if(!isset(self::$pool[$key])) {
            self::$pool[$key]= self::getConnection($host, $port, $timeout);
        }

        if(!isset(self::$currentDb[$key]) || self::$currentDb[$key] != $db) {
            if(self::$pool[$key]->select($db)) {
                self::$currentDb[$key] = $db;
            } else {
                throw new \Exception("select db($db) failed. host is $host, port is $port.");
            }
        }
        return self::$pool[$key];
    }

    public static function reconnect($host, $port, $timeout) {
        $key = self::getPoolKey($host, $port, $timeout);
        self::$currentDb[$key] = null;
        self::$pool[$key]= self::getConnection($host, $port, $timeout);
    }
}
