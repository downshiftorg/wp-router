<?php
use NetRivet\WordPress\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router('my_scope');

$router->post('/echo', function () {
    $data = file_get_contents('php://input');
    header('content-type: application/json');
    echo $data;
});

$router->listen();
