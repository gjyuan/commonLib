<?php

namespace Common\Config;

class Db extends namespace\Parser {

    private $type = "db";

    public function get($appName, $key) {
        return $this->getValue($this->type, $appName, $key);
    }
}
