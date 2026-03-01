<?php

namespace App\Http\Middleware;

// Remove this line; 'app()' is a Laravel global helper and does not need to be imported.

class AdminMiddleware
{
    public function handle(): void
    {
        // JWTMiddleware must run first
        $authService = \App\Services\AuthService::class;
        (new JWTMiddleware(new $authService()))->handle();

        if (($_SERVER['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Admin access required']);
            exit;
        }
    }
}
