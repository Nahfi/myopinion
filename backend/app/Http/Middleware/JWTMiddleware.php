<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Database;

class JWTMiddleware
{
    public function handle()
    { 
        $authHeader = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            }
        }

        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['message' => 'Authorization header not found']);
            exit;
        }
 
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid Authorization format']);
            exit;
        }

        $token = $matches[1];
 
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM token_blacklist WHERE token = ?");
        $stmt->execute([$token]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(401);
            echo json_encode(['message' => 'This token has been logged out']);
            exit;
        }
 
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
 
            $_SERVER['user_id'] = $decoded->sub;
            $_SERVER['user_role'] = $decoded->role;

            return true;

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token', 'error' => $e->getMessage()]);
            exit;
        }
    }
}
