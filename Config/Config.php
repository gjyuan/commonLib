<?php

namespace Common\Config;

class Config {

    private static $parserMap;

    public static function getAppConf($appName, $key) {
        $parser = self::getParser("app");
        return $parser->get($appName, $key);
    }

    public static function getDBConf($appName, $key="mysql") {
        $parser = self::getParser("db");
        return $parser->get($appName, $key);
    }

    public static function getServiceConf($key) {
        $parser = self::getParser("service");
        return $parser->get($key);
    }

    private static function getParser($name) {
        if(!isset(self::$parserMap[$name])) {
            $obj = "";
            switch($name) {
                case "app":
                    $obj = new namespace\App();
                    break;
                case "db":
                    $obj = new namespace\Db();
                    break;
                case "service":
                    $obj = new namespace\Service();
                    break;
                default:
                    return;
            }
            self::$parserMap[$name] = $obj;
        }
        return self::$parserMap[$name];
    }

    private function __construct() {}
    private function __clone() {}
}
