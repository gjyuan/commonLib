<?php

namespace Common\Utils;

class Url {

    private static $httpStatusHeader = array(
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out"
    );

    public static function getServerName() {
        return !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
    }

    public static function getRequestPath() {
        $path = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
        $path = filter_var($path, FILTER_SANITIZE_URL);

        $parts = explode("?", $path);
        return trim($parts[0], "/");
    }

    public static function issetParam($paramName) {
        return isset($_REQUEST[$paramName]);
    }

    public static function getIntParam($paramName, $defaultValue = 0) {
        $value = $defaultValue;
        if(isset($_REQUEST[$paramName])) {
            $value = trim($_REQUEST[$paramName]);
            if(!is_numeric($value)) {
                return $defaultValue;
            }
        }
        return $value - 0;
    }

    public static function getStringParam($paramName, $defaultValue = "") {
        $value = $defaultValue;
        if(isset($_REQUEST[$paramName])) {
            $value = trim(htmlentities($_REQUEST[$paramName], ENT_QUOTES|ENT_IGNORE, 'UTF-8'));
        }
        return $value;
    }

    public static function getArrayParam($paramName) {
        $value = array();
        if(isset($_REQUEST[$paramName])) {
            foreach($_REQUEST[$paramName] as $v) {
                $v = trim($v);
                if(!empty($v)) {
                    $value[] = htmlentities($v, ENT_QUOTES|ENT_IGNORE, 'UTF-8');
                } else {
                    $value[] = "";
                }
            }
        }
        return $value;
    }

    public static function getPage() {
        $page = self::getIntParam("page");
        if($page < 1) {
            $page = 1;
        }
        return $page;
    }

    public static function getPageSize() {
        $pageSize = self::getIntParam("pagesize");
        if($pageSize < 1) {
            $pageSize = 20;
        }
        return $pageSize;
    }

    public static function redirect($location, $status=303) {
        self::httpHeader($status);
        header("Location: $location");
        exit;
    }

    public static function httpHeader($status) {
        if(isset(self::$httpStatusHeader[$status])) {
            header(self::$httpStatusHeader[$status]);
        }
    }
}

