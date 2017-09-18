<?php

/**
 * User: Victor Häggqvist
 * Date: 3/4/15
 * Time: 12:58 PM
 */
class Foo {
    public $name;
    private $attrs = array();

    /* @var Foo */
    private $object;

    public function __construct($name = null, $object = null)
    {
        $this->name   = $name;
        $this->object = $object;
    }

    public function __isset($name) {
        return isset($this->attrs[$name]);
    }

    public function __get($name) {
        return $this->attrs[$name];
    }

    public function __set($name, $value) {
        $this->attrs[$name] = $value;
    }

    /**
     * @return Foo
     */
    public function getObject()
    {
        return $this->object;
    }
}
