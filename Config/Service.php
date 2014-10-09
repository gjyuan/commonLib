<?php

namespace Common\Config;

class Service extends namespace\Parser {

    private $type = "service";

    public function get($key) {
        return $this->getValue($this->type, $this->type, $key);
    }
}
