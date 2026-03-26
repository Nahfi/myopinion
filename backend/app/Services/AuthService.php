<?php

namespace App\Services;

use App\Database;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register(array $data): array
    {
        // Validate required fields
        foreach (['name', 'email', 'username', 'password'] as $field) {
            if (empty($data[$field])) {
                return ['error' => "Field '$field' is required", 'code' => 422];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format', 'code' => 422];
        }

        if ($this->userRepo->emailExists($data['email'])) {
            return ['error' => 'Email already taken', 'code' => 409];
        }

        if ($this->userRepo->usernameExists($data['username'])) {
            return ['error' => 'Username already taken', 'code' => 409];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $id               = $this->userRepo->create($data);
        $user             = $this->userRepo->findById($id);
        $token            = $this->generateToken($user);

        return [
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => $this->sanitizeUser($user),
        ];
    }

    public function login(array $data): array
    {
        if (empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Email and password are required', 'code' => 422];
        }

        $user = $this->userRepo->findByEmailOrUsername($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return ['error' => 'Invalid credentials', 'code' => 401];
        }

        if($user['status'] != 'active') {
            return ['error' => 'Account is not active', 'code' => 401];
        }

        $token = $this->generateToken($user);

        return [
            'token' => $token,
            'user'  => $this->sanitizeUser($user),
        ];
    }

    public function logout(string $token): array
    {
        $pdo  = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO token_blacklist (token) VALUES (?)');
        $stmt->execute([$token]);
        return ['message' => 'Logged out successfully'];
    }

    private function generateToken(array $user): string
    {
        $payload = [
            'sub'  => $user['id'],
            'role' => $user['role'],
            'name' => $user['name'],
            'iat'  => time(),
            'exp'  => time() + (60 * 60 * 24 * 7), // 7 days
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }

    private function sanitizeUser(array $user): array
    {
        unset($user['password']);
        return $user;
    }
}
