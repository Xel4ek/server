<?php


class Router
{
    private $registry;
    private $path;
    private $args = array();
    function __construct($registry) {
        $this->registry = $registry;
    }
    function setPath($path) {
        $path = trim($path, '/\\');
        $path = DIR . DIRSEP . $path;
        if (is_dir($path) == false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        $this->path = $path;
    }
    function delegate()
    {
        $this->getController($file, $controller, $action, $args);
//        var_dump( $args);
        $options = array();
        if(!empty($args)) {
            foreach ($args as $entry) {
                $arg = explode('=', $entry);
                $options[$arg[0]] = $arg[1];
            }
        }
        $this->registry['options'] = $options;
        if (is_readable($file) == false) {
            die ('404 Not Found');
        }
        include ($file);
        $class = 'Controller_' . $controller;
        $controller = new $class($this->registry);
        if (is_callable(array($controller, $action)) == false) {
            die ('404 Not Found');
        }
        $controller->$action();
    }
    private function getController(&$file, &$controller, &$action, &$args) {
        $route = (empty($_GET['route'])) ? '' : $_GET['route'];
        if (empty($route)) { $route = 'index'; }
        $route = trim($route, '/\\');
        $parts = explode('/', $route);
        $cmd_path = $this->path . DIRSEP;
        foreach ($parts as $part) {
            $full_path = $cmd_path . $part;
            if (is_dir($full_path)) {
                $cmd_path .= $part . DIRSEP;
                array_shift($parts);
                continue;
            }
            if (is_file($full_path . '.php')) {
                $controller = $part;
                array_shift($parts);
                break;
            }
        }
        if (empty($controller)) { $controller = 'index'; };
        $action = array_shift($parts);
        if (empty($action)) { $action = 'index'; }
        $file = $cmd_path . $controller . '.php';
        $args = $parts;
    }
}
