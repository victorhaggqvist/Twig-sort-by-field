<?php

/**
 * User: Victor Häggqvist
 * Date: 3/4/15
 * Time: 12:58 PM
 */
class Foo {
    public $name;
    private $attrs = array();

    public function __isset($name) {
        return isset($this->attrs[$name]);
    }

    public function __get($name) {
        return $this->attrs[$name];
    }

    public function __set($name, $value) {
        $this->attrs[$name] = $value;
    }
}
