<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function getAll(): array
    {
        return ['users' => $this->userRepo->all()];
    }

    public function getOne(int $id): array
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
        }
        return $user;
    }

    public function delete(int $id): array
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
        }
        $this->userRepo->delete($id);
        return ['message' => 'User deleted'];
    }

    /**
     * Summary of updateProfile
     * @param int $id
     * @param array $data
     * @return array{code: int, error: string|array{message: string, user: array|null}}
     */
    public function updateProfile(int $id, array $data): array
    {
        if (empty($data['name']) || empty($data['username']) || empty($data['email'])) {
            return ['error' => 'Name, username and email are required', 'code' => 422];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format', 'code' => 422];
        }

        // Check email uniqueness (excluding current user)
        $existing = $this->userRepo->findByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== $id) {
            return ['error' => 'Email already taken', 'code' => 409];
        }

        // Check username uniqueness (excluding current user)
        $existingU = $this->userRepo->findByUsername($data['username']);
        if ($existingU && (int) $existingU['id'] !== $id) {
            return ['error' => 'Username already taken', 'code' => 409];
        }

        $updateData = [
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $this->userRepo->update($id, $updateData);

        $user = $this->userRepo->findById($id);
        unset($user['password']);
        return ['message' => 'Profile updated', 'user' => $user];
    }
}
