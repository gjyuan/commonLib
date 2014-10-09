<?php
class Loader {
    const NS_SEPARATOR     = '\\';
    const PREFIX_SEPARATOR = '_';

    private static $includePath = array();
    private static $hasLoaded = false;

    public static function addIncludePath($path) {
        self::$includePath[] = $path;
    }

    public static function addIncludePaths($paths) {
        foreach($paths as $path) {
            self::addIncludePath($path);
        }
    }

    public static function load() {
        if(self::$hasLoaded) {
            return;
        }
        self::$hasLoaded = true;

        if(!empty(self::$includePath)) {
            set_include_path(get_include_path() .
                PATH_SEPARATOR. implode(PATH_SEPARATOR, self::$includePath));
        }
        spl_autoload_register(function ($class) {
var_dump($class);
            $file = str_replace(Loader::PREFIX_SEPARATOR, DIRECTORY_SEPARATOR, $class);
            if (false !== strpos($class, Loader::NS_SEPARATOR)) {
                $file = str_replace(Loader::NS_SEPARATOR, DIRECTORY_SEPARATOR, $file);
            }
            include $file . ".php";
        });
    }
}

Loader::load();

