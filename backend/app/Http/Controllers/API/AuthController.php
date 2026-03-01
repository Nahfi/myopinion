<?php

namespace App\Http\Controllers\API;

use App\Models\TokenBlacklist;
use App\Models\User;
use Firebase\JWT\JWT;
use PDO;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register($data)
    {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $id = $this->userModel->create(
            $data['name'],
            $data['email'],
            $data['username'],
            $hashed
        );

        echo json_encode(['message' => 'Registration successful', 'user_id' => $id]);
    }

    public function login($data)
    {
        $user = $this->userModel->findByEmailOrUsername($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            return;
        }

        $payload = [
            'sub'  => $user['id'],
            'role' => $user['role'],
            'exp'  => time() + 3600
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        echo json_encode(['token' => $token]);
    }

    public function logout($token)
    {
        $blacklist = new TokenBlacklist();
        $blacklist->add($token);
        echo json_encode(['message' => 'Logged out']);
    }
}
