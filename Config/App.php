<?php

namespace Common\Config;

class App extends namespace\Parser {

    private $type = "app";

    public function get($appName, $key) {
        return $this->getValue($this->type, $appName, $key);
    }
}
