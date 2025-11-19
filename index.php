<?php
require_once 'env.php';
require_once 'templates/TemplateEngine.php';
require_once 'controllers/EstacionController.php';

$template = new TemplateEngine();
$controller = new EstacionController($template);

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = str_replace('/app-estacion/', '', $path);

if (empty($path) || $path === '/') {
    $controller->landing();
} elseif ($path === 'panel') {
    $controller->panel();
} elseif (preg_match('/^detalle\/(.+)$/', $path, $matches)) {
    $controller->detalle($matches[1]);
} else {
    http_response_code(404);
    echo "Página no encontrada";
}