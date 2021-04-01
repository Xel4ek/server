<?php
define('DIRSEP', DIRECTORY_SEPARATOR);
define('DIR', __DIR__);
if (!function_exists('classAutoLoader')) {
    function classAutoLoader($class)
    {
//         $classFile = $_SERVER['DOCUMENT_ROOT'].
        $classFile = DIR .
            DIRSEP .
            'include' .
            DIRSEP .
            'class' .
            DIRSEP .
            $class . '.class.php';
        if (is_file($classFile) && !class_exists($class)) include $classFile;
    }
}
spl_autoload_register('classAutoLoader');

header("Access-Control-Allow-Origin: http://localhost:8080");
header('Access-Control-Allow-Credentials: true');
session_start();
if (isset($_SESSION['registry'])) {
    $registry = $_SESSION['registry'];
    $router = $registry['router'];
} else {
    $registry = new Registry;
    $router = new Router($registry);
    $registry['router'] = $router;
    $router->setPath(DIRSEP . 'controllers');
    $model = new Model($registry);
    $_SESSION['registry'] = $registry;
}
$router->delegate();
