<?php
session_start();
require_once 'config/database.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if (isset($_SESSION['user_id']) && $controller == 'auth' && $action == 'login') {
    $controller = 'dashboard';
    $action = 'index';
}

$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerObj = new $controllerName();
    if (method_exists($controllerObj, $action)) {
        $controllerObj->$action();
    } else {
        echo "Action not found";
    }
} else {
    echo "Controller not found";
}
?>
