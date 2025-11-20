<?php
session_start();
require_once 'env.php';
require_once 'templates/TemplateEngine.php';
require_once 'controllers/EstacionController.php';
require_once 'controllers/AuthController.php';

$template = new TemplateEngine();
$estacionController = new EstacionController($template);
$authController = new AuthController($template);

$request = $_GET['r'] ?? '';
$segments = explode('/', $request);
$route = $segments[0] ?? '';

if (empty($route)) {
    $estacionController->landing();
} elseif ($route === 'panel') {
    $estacionController->panel();
} elseif ($route === 'detalle' && isset($segments[1])) {
    $estacionController->detalle($segments[1]);
} elseif ($route === 'login') {
    $authController->login();
} elseif ($route === 'register') {
    $authController->register();
} elseif ($route === 'validate' && isset($segments[1])) {
    $authController->validate($segments[1]);
} elseif ($route === 'blocked' && isset($segments[1])) {
    $authController->blocked($segments[1]);
} elseif ($route === 'recovery') {
    $authController->recovery();
} elseif ($route === 'reset' && isset($segments[1])) {
    $authController->reset($segments[1]);
} elseif ($route === 'logout') {
    $authController->logout();
} else {
    http_response_code(404);
    echo "Página no encontrada";
}