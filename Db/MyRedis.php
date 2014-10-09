<?php

namespace Common\Db;

class MyRedis {
    private $db;
    private $conf;
    private $timeout;

    public function __construct($db, $conf, $timeout=0) {
        $this->db = $db;
        $this->conf = $conf;
        $this->timeout = $timeout;
    }

    private static function callRedis($redis, $method, $arguments) {
        if(!is_array($arguments)) {
            $arguments = array($arguments);
        }
        $argCount = count($arguments);
        $ret = false;
        if($argCount == 1) {
            $ret = $redis->$method($arguments[0]);
        } else if($argCount == 2) {
            $ret = $redis->$method($arguments[0], $arguments[1]);
        } else if($argCount == 3) {
            $ret = $redis->$method($arguments[0], $arguments[1], $arguments[2]);
        } else if($argCount == 4) {
            $ret = $redis->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
        } else if($argCount == 5) {
            $ret = $redis->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
        } else {
            $ret = call_user_func_array(array($redis, $method), $arguments);
        }
        return $ret;
    }

    private function getConf($arguments) {
        if(isset($this->conf["host"])) {
            return $this->conf;
        } else {
            $key = $arguments[0];
            $size = count($this->conf);
            $node = "node".((abs(crc32($key)) % $size) + 1);
            if(!isset($this->conf[$node])) {
                throw new \Exception("Redis config for node '$node' is not exist");
            }
            return $this->conf[$node];
        }
    }

    public function __call($method, $arguments) {
        $conf = $this->getConf($arguments);
        $redis = namespace\RedisPool::getRedisHandler($conf["host"], $conf["port"], $this->timeout, $this->db);
        $ret = false;
        try {
            $ret = self::callRedis($redis, $method, $arguments);
        } catch(\Exception $e) {
            namespace\RedisPool::reconnect($conf["host"], $conf["port"], $this->timeout);
            $redis = namespace\RedisPool::getRedisHandler($conf["host"], $conf["port"], $this->timeout, $this->db);
            $ret = self::callRedis($redis, $method, $arguments);
        }
        return $ret;
    }

}
