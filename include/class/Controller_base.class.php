<?php


abstract Class Controller_base {
    protected $registry;
    function __construct($registry) {
        $this->registry = $registry;
    }
    abstract function index();
}