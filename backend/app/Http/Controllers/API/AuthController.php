<?php

namespace App\Http\Controllers\API;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->authService->register($data);
        $this->respond($result);
    }

    public function login(): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->authService->login($data);
        $this->respond($result);
    }

    public function logout(): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        $token  = $matches[1] ?? '';
        $result = $this->authService->logout($token);
        $this->respond($result);
    }

    private function respond(array $result): void
    {
        $code = $result['code'] ?? 200;
        if (isset($result['error'])) {
            http_response_code($code);
            echo json_encode(['message' => $result['error']]);
        } else {
            http_response_code(isset($result['token']) ? 200 : (isset($result['post_id']) ? 201 : 200));
            echo json_encode($result);
        }
    }
}
