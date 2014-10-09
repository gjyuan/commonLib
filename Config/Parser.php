<?php

namespace Common\Config;

if(!empty($_SERVER) && isset($_SERVER["SpdConfigBasePath"])) {
    define("SpdConfigBasePath", $_SERVER["SpdConfigBasePath"]);
} else {
    define("SpdConfigBasePath", "/usr/local/spd/configs");
}

abstract class Parser {

  private $iniConf;
  private $phpConf;

  protected function getValue($type, $appName, $key) {
    if (!isset($this->phpConf[$type][$appName][$key])) {
      $this->parserIniFile($type, $appName);
      $iniValue = $this->iniConf[$type][$appName];
      if (!isset($iniValue[$key])) {
        throw new \Exception("Key '$key' is not exist in $appName");
      }
      $value = $iniValue[$key];
      $startChar = substr($value, 0, 1);
      $phpValue = "";
      if ($startChar == "[" || $startChar == "{") {
        $phpValue = json_decode($value, true);
      }
      if (empty($phpValue)) {
        $phpValue = $value;
      }
      $this->phpConf[$type][$appName][$key] = $phpValue;
    }
    return $this->phpConf[$type][$appName][$key];
  }

//  private function parserIniFile($type, $appName) {
//    if (!isset($this->iniConf[$type][$appName])) {
//      $file = SpdConfigBasePath . "/" . $type . "/" . $appName . ".ini";
//      if (!file_exists($file)) {
//        throw new \Exception("Config file '$file' is not exist.");
//      }
//      $this->iniConf[$type] = array($appName => parse_ini_file($file));
//      $this->phpConf[$type] = array($appName => array());
//    }
//  }

  private function parserIniFile($type, $appName) {
    if (!isset($this->iniConf[$type][$appName])) {
      $file = SpdConfigBasePath . "/" . $type . "/" . $appName . ".ini";
      if (!file_exists($file)) {
        throw new \Exception("Config file '$file' is not exist.");
      }

      $ini = $this->readIniFile($file);
      $this->iniConf[$type] = array($appName => $ini);
      $this->phpConf[$type] = array($appName => array());
    }
  }

  /**
   * 读取ini文件
   * @param $file
   * @return array [section][key] = value ,默认section = "";
   *
   */
  private function readIniFile($file) {

    $itemsMap = array();

    $file_handle = fopen($file, "r");
    while (!feof($file_handle)) {
      $line = fgets($file_handle);
      $line = trim($line);
      if ($line == "" || strpos($line, ";")) {
        continue;
      }
        if (strpos($line, "=")) {
          $kv = explode("=", $line,2);
          $key = trim($kv[0]);
          $value = trim($kv[1]);
          $itemsMap[$key] = $value;
        }
    }
    fclose($file_handle);
    return $itemsMap;
  }


}
