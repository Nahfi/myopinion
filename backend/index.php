<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Middleware\JWTMiddleware;

require_once __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_GET['_method']);
}


$uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
 
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base !== '/') {
    $uri = preg_replace('#^' . preg_quote($base) . '#', '', $uri);
}

$uri = $uri ?: '/';


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

header('Content-Type: application/json');
  
if (preg_match('#^/posts/(\d+)$#', $uri, $m) && $method === 'GET') {
    $controller = new PostController();
    $controller->show($m[1]);
    exit;
}

if (preg_match('#^/posts/(\d+)$#', $uri, $m) && $method === 'PUT') {
    (new JWTMiddleware())->handle();
    $controller = new PostController();
    $controller->update($m[1], $input);
    exit;
}

if (preg_match('#^/posts/(\d+)$#', $uri, $m) && $method === 'DELETE') {
    (new JWTMiddleware())->handle();
    $controller = new PostController();
    $controller->destroy($m[1]);
    exit;
}

if (preg_match('#^/reaction/(\d+)$#', $uri, $m) && $method === 'POST') {
    (new JWTMiddleware())->handle();
    $controller = new PostController();
    $controller->reaction($m[1]);
    exit;
}


if (preg_match('#^/posts/(\d+)/comments$#', $uri, $m) && $method === 'POST') {
    (new App\Http\Middleware\JWTMiddleware())->handle();
    $controller = new App\Http\Controllers\API\CommentController();
    $controller->store($m[1], $input);
    exit;
}

if (preg_match('#^/posts/(\d+)/comments$#', $uri, $m) && $method === 'GET') {
    $controller = new App\Http\Controllers\API\CommentController();
    $controller->index($m[1]);
    exit;
}
 

switch ("$method $uri") {
    case 'POST /register':
        $controller = new AuthController();
        $controller->register($input);
        break;

    case 'POST /login':
        $controller = new AuthController();
        $controller->login($input);
        break;

    case 'GET /posts':
        $controller = new PostController();
        $controller->index();
        break;

    case 'GET /users':
        (new JWTMiddleware())->handle();
        $controller = new UserController();
        $controller->index();
        break;

    case 'POST /posts':
        (new JWTMiddleware())->handle();
        $controller = new PostController();
        $controller->store($input);
        break;

    case 'POST /reaction':
        (new JWTMiddleware())->handle();
        $controller = new PostController();
        $controller->store($input);
        break;

    case 'POST /logout':
        (new App\Http\Middleware\JWTMiddleware())->handle();
        $controller = new AuthController();
        $controller->logout();
    break;


    default:
        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
        break;
}


function dd($data, $flow = null)
{
    print "<pre>";
    print_r($data);
    print "</pre>";
    if ($flow) {
        return true;
    }
    exit;
}