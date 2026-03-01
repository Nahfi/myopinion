<?php

namespace App\Http\Middleware;

use App\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTMiddleware
{
    public function handle(): void
    {
        $token = $this->extractToken();

        if (!$token) {
            http_response_code(401);
            echo json_encode(['message' => 'Authorization token not provided']);
            exit;
        }

        // Check token blacklist
        $pdo  = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM token_blacklist WHERE token = ?');
        $stmt->execute([$token]);
        if ((int)$stmt->fetchColumn() > 0) {
            http_response_code(401);
            echo json_encode(['message' => 'Token has been revoked']);
            exit;
        }

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            $_SERVER['user_id']   = $decoded->sub;
            $_SERVER['user_role'] = $decoded->role;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid or expired token']);
            exit;
        }
    }

    private function extractToken(): ?string
    {
        $header = null;
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $header  = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }
        if (!$header) {
            $header = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        }
        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
