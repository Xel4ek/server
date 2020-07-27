<?php


class Registry Implements ArrayAccess
{
    private $args;
    function  __construct()
    {
    }
    function offsetExists($offset) {
        return isset($this->args[$offset]);
    }

    function offsetGet($offset) {
        return $this->args[$offset];
    }

    function offsetSet($offset, $value) {
        $this->args[$offset] = $value;
    }

    function offsetUnset($offset) {
        unset($this->args[$offset]);
    }
}