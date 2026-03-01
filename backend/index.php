<?php

require_once __DIR__ . '/bootstrap.php';

use App\Core\Router;

header('Content-Type: application/json');

// Support _method override for PUT/DELETE in multipart forms
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_GET['_method']);
}

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/') ?: '/';

// Strip subfolder if project is not in web root
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($base && $base !== '/') {
    $uri = preg_replace('#^' . preg_quote($base) . '#', '', $uri) ?: '/';
}

// Initialize router
$router = new Router();

// Load all routes from routes/api.php
require_once __DIR__ . '/routes/api.php';

// Dispatch the request
$router->dispatch($method, $uri);
