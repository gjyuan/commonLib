<?php
/**
 * dao基类提供了如下功能：
 *   1. 配置中心接入
 *   2. 读接口的软负载均衡
 *   3. 分库分表的支持
 *   4. mysql和redis实例的维护
 */

namespace Common\Db;

abstract class Dao {

    private static $masterConf = array();
    private static $slaverConf = array();

    private static $dbCount = 10;
    private static $tbCount = 10;

    private $mysql;
    private $redis;

    private $appName;
    private $redisDb;
    private $mysqlDbName;
    private $isMaster;
    private $hashKey;

    protected function __construct($appName, $redisDb, $mysqlDbName, $isMaster=false, $hashKey="") {
        $this->appName = $appName;
        $this->redisDb = $redisDb;
        $this->mysqlDbName = $mysqlDbName;
        $this->isMaster = $isMaster;
        $this->hashKey = $hashKey;
    }

    private function getConf($configKey) {
        if($this->isMaster && isset(self::$masterConf[$configKey]) && isset(self::$masterConf[$configKey][$this->appName])) {
            return self::$masterConf[$configKey][$this->appName];
        }
        if(!$this->isMaster && isset(self::$slaverConf[$configKey]) && isset(self::$slaverConf[$configKey][$this->appName])) {
            return self::$slaverConf[$configKey][$this->appName];
        }
        $conf = \Common\Config\Config::getDBConf($this->appName, $configKey);
        if(empty($conf)) {
            throw new \Exception($configKey." conf for ".$this->appName." is missing.");
        }

        if($this->isMaster) {
            if(!isset(self::$masterConf[$configKey])) {
                self::$masterConf[$configKey] = array();
            }
            self::$masterConf[$configKey][$this->appName] = $conf["master"];
            return self::$masterConf[$configKey][$this->appName];
        } else {
            if(!isset(self::$slaverConf[$configKey])) {
                self::$slaverConf[$configKey] = array();
            }
            $masterConf = $conf["master"];
            $conf = $conf["slaver"];
            $conf[] = $masterConf;
            self::$slaverConf[$configKey][$this->appName] = $conf[rand(0, count($conf)-1)];
            return self::$slaverConf[$configKey][$this->appName];
        }
    }

    protected function getRedis($configKey="redis") {
        if(!isset($this->redis)) {
            $this->redis = new namespace\MyRedis($this->redisDb, $this->getConf($configKey));
        }
        return $this->redis;
    }

    private function getMysqlConf($configKey="mysql") {
        $conf = $this->getConf($configKey);
        // TODO: 根据hashKey处理分库逻辑
        return $conf;
    }

    protected function getMysql($configKey="mysql") {
        if(empty($this->mysql)) {
            $conf = $this->getMysqlConf($configKey);
            $this->mysql = namespace\Mysql::getInstance($conf["host"], $conf["port"], $conf["user"], $conf["passwd"], $this->mysqlDbName);
        }
        return $this->mysql;
    }

    protected function begin($configKey="mysql") {
        $this->getMysql($configKey)->beginTransaction();
    }

    protected function commit($configKey="mysql") {
        $this->getMysql($configKey)->commit();
    }

    protected function rollBack($configKey="mysql") {
        $this->getMysql($configKey)->rollBack();
    }

    protected function prepare($sql, $configKey="mysql") {
        return $this->getMysql($configKey)->prepare($sql);
    }

    protected function lastInsertId($configKey="mysql") {
        return $this->getMysql($configKey)->lastInsertId();
    }

    protected function checkWritable() {
        if(!$this->isMaster) {
            throw new \Exception("Write to slave error.");
        }
    }

    protected static function cacuDB($dbname, $uid){
        $prefix = $uid / 1000 % self::$dbCount;
        return $dbname.$prefix;
    }

    protected static function cacuTB($tbname, $uid) {
        $prefix = $uid % self::$tbCount;
        return $tbname.$prefix;
    }

    public static function cacuStringAscii($string) {
        $s = 0;
        for($i=0; $i<strlen($string); $i++) {
            $s += ord($string[$i]);
        }
        return $s;
    }

}

