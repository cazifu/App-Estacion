<?php
require_once 'env.php';
require_once 'templates/TemplateEngine.php';
require_once 'controllers/EstacionController.php';

$template = new TemplateEngine();
$controller = new EstacionController($template);

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);

// Remove app-estacion from path if present
if (!empty($segments) && $segments[0] === 'app-estacion') {
    array_shift($segments);
}

$route = $segments[0] ?? '';

if (empty($route)) {
    $controller->landing();
} elseif ($route === 'panel') {
    $controller->panel();
} elseif ($route === 'detalle' && isset($segments[1])) {
    $controller->detalle($segments[1]);
} else {
    http_response_code(404);
    echo "Página no encontrada";
}