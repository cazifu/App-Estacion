<?php
require_once 'env.php';
require_once 'templates/TemplateEngine.php';
require_once 'controllers/EstacionController.php';

$template = new TemplateEngine();
$controller = new EstacionController($template);

$request = $_GET['r'] ?? '';

if (empty($request)) {
    $controller->landing();
} elseif ($request === 'panel') {
    $controller->panel();
} elseif (strpos($request, 'detalle/') === 0) {
    $chipid = substr($request, 8);
    $controller->detalle($chipid);
} else {
    http_response_code(404);
    echo "Página no encontrada";
}