<?php 

declare(strict_types=1);

//autoloading class files
spl_autoload_register(function ($class){
    require __DIR__ . "/src/$class.php";
});

//setting exception handler
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

//getting API responses in json format
header("Content-type: application/json; charset=UTF-8");

//breaking the url into parts in an array
$parts = explode("/", $_SERVER['REQUEST_URI']);

if($parts[1] != "products"){
    http_response_code(404);
    exit;
}

$id = $parts[2] ?? null;
 
//obj invocations
$database = new Database("localhost", "plain_php_api", "phpmyadmin", "0101157029");
$gateway = new ProductGateway($database);
$controller = new ProductsController($gateway);

//object invocations

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);


