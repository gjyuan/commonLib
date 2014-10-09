<?php

$libname = "phplib";
$libDir = substr(__DIR__, 0, strpos(__DIR__, $libname)) . $libname;
set_include_path(get_include_path().PATH_SEPARATOR. $libDir);

include "Loader.php";
#Loader::load();

$_SERVER["SpdConfigBasePath"] = __DIR__;

$appName = "spd";

var_dump(Common\Config\Config::getAppConf($appName, "key"));
//var_dump(Common\Config\Config::getDBConf($appName, "mysql"));
