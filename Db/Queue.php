<?php
/**
 * 采用redis list做队列读写，但不支持并发读取
 * 插入数据的方法：rpush
 * 批量取出数据：lrange(), then ltrim()
 */

namespace Db;

class Queue extends namespace\Dao {

    private $redisDb;
    private $appName;
    private $event;
    private $configKey;

    /**
     * @param appName   业务名称，对应配置文件名
     * @param event     redis队列的key：appName_event
     * @param configKey 配置文件中的key，默认redis
     * @param redisDb   默认队列放在db 1中
     */
    public function __construct($appName, $event, $configKey="redis", $redisDb=1) {
        parent::__construct($appName, $redisDb, "", true);
        $this->appName = $appName;
        $this->event = $event;
        $this->redisDb = $redisDb;
        $this->configKey = $configKey;
    }

    private function getRedisKey() {
        return "rqueue:".$this->appName.":".$this->event;
    }

    public function push($value) {
        $this->checkWritable();
        return false !== $this->getRedis($this->configKey)->rpush($this->getRedisKey(), json_encode($value));
    }

    public function pop($length=1) {
        $this->checkWritable();
        if($length < 1) {
            return array();
        }
        $key = $this->getRedisKey();
        if($length == 1) {
            $list = $this->getRedis($this->configKey)->lpop($key);
            if(!empty($list)) {
                $list = array($list);
            } else {
                $list = array();
            }
        } else {
            $list = $this->getRedis($this->configKey)->lrange($key, 0, $length-1);
            if(!empty($list)) {
                $this->getRedis($this->configKey)->ltrim($key, count($list), -1);
            } else {
                $list = array();
            }
        }
        $ret = array();
        for($i=0; $i<count($list); $i++) {
            $ret[] = json_decode($list[$i], true);
        }
        return $ret;
    }
}
