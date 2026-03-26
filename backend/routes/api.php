<?php

use App\Core\Router;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ReactionTypeController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\JWTMiddleware;

/** @var Router $router */

$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/:id', [PostController::class, 'show']);
$router->get('/posts/:id/comments', [CommentController::class, 'index']);
$router->get('/reaction-types', [PostController::class, 'reactionTypes']);

// Authenticated routes
$router->post('/logout', [AuthController::class, 'logout'], [JWTMiddleware::class]);
$router->post('/posts', [PostController::class, 'store'], [JWTMiddleware::class]);
$router->post('/posts/:id', [PostController::class, 'update'], [JWTMiddleware::class]); // _method=PUT
$router->put('/posts/:id', [PostController::class, 'update'], [JWTMiddleware::class]);
$router->delete('/admin/posts/:id', [PostController::class, 'destroy'], [JWTMiddleware::class]);
$router->post('/reaction/:postId', [PostController::class, 'reaction'], [JWTMiddleware::class]);
$router->post('/posts/:id/comments', [CommentController::class, 'store'], [JWTMiddleware::class]);
$router->get('/posts/with/not-commented', [PostController::class, 'notCommented'], [JWTMiddleware::class]);

// Admin routes
$router->get('/admin/users', [UserController::class, 'index'], [AdminMiddleware::class]);
$router->delete('/admin/users/:id', [UserController::class, 'destroy'], [AdminMiddleware::class]);

$router->post('/admin/users', [UserController::class, 'store'], [AdminMiddleware::class]);
$router->put('/admin/users/:id', [UserController::class, 'update'], [AdminMiddleware::class]);
$router->patch('/admin/users/:id/status', [UserController::class, 'toggleStatus'], [AdminMiddleware::class]);

$router->get('/admin/comments', [AdminCommentController::class, 'all'], [AdminMiddleware::class]);

$router->get('/admin/comments/pending', [AdminCommentController::class, 'pending'], [AdminMiddleware::class]);
$router->get('/admin/posts/:postId/comments', [AdminCommentController::class, 'allForPost'], [AdminMiddleware::class]);
$router->patch('/admin/comments/:id/approve', [AdminCommentController::class, 'approve'], [AdminMiddleware::class]);
$router->patch('/admin/comments/:id/reject', [AdminCommentController::class, 'reject'], [AdminMiddleware::class]);
$router->delete('/admin/comments/:id', [AdminCommentController::class, 'destroy'], [AdminMiddleware::class]);

$router->get('/admin/reaction-types', [ReactionTypeController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/reaction-types', [ReactionTypeController::class, 'store'], [AdminMiddleware::class]);
$router->put('/admin/reaction-types/:id', [ReactionTypeController::class, 'update'], [AdminMiddleware::class]);
$router->delete('/admin/reaction-types/:id', [ReactionTypeController::class, 'destroy'], [AdminMiddleware::class]);
$router->get('/profile', [ProfileController::class, 'show'], [JWTMiddleware::class]);
$router->put('/profile', [ProfileController::class, 'update'], [JWTMiddleware::class]);
