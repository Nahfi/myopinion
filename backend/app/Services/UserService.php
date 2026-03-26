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
        if (!$user) return ['error' => 'User not found', 'code' => 404];
        unset($user['password']);
        return $user;
    }

    public function create(array $data): array
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Name, email and password are required', 'code' => 422];
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format', 'code' => 422];
        }
        if (strlen($data['password']) < 6) {
            return ['error' => 'Password must be at least 6 characters', 'code' => 422];
        }

        // password confirm check
        if (empty($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
            return ['error' => 'Passwords do not match', 'code' => 422];
        }

        $existing = $this->userRepo->findByEmail($data['email']);
        if ($existing) return ['error' => 'Email already taken', 'code' => 409];

        if (!empty($data['username'])) {
            $existingU = $this->userRepo->findByUsername($data['username']);
            if ($existingU) return ['error' => 'Username already taken', 'code' => 409];
        }

        $id = $this->userRepo->create([
            'name'     => $data['name'],
            'username' => $data['username'] ?? null,
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => 'user', // always user, no role edit
            'status'   => 'active',
        ]);

        $user = $this->userRepo->findById($id);
        unset($user['password']);
        return ['message' => 'User created', 'user' => $user];
    }

    public function adminUpdate(int $id, array $data): array
    {
        $user = $this->userRepo->findById($id);
        if (!$user) return ['error' => 'User not found', 'code' => 404];
        if ($user['role'] === 'admin') return ['error' => 'Admin cannot be edited', 'code' => 403];

        if (empty($data['name']) || empty($data['email'])) {
            return ['error' => 'Name and email are required', 'code' => 422];
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format', 'code' => 422];
        }

        $existing = $this->userRepo->findByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== $id) {
            return ['error' => 'Email already taken', 'code' => 409];
        }

        if (!empty($data['username'])) {
            $existingU = $this->userRepo->findByUsername($data['username']);
            if ($existingU && (int) $existingU['id'] !== $id) {
                return ['error' => 'Username already taken', 'code' => 409];
            }
        }

        $updateData = [
            'name'     => $data['name'],
            'username' => $data['username'] ?? $user['username'],
            'email'    => $data['email'],
        ];

         // only validate/hash password if provided
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return ['error' => 'Password must be at least 6 characters', 'code' => 422];
            }
            if (empty($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
                return ['error' => 'Passwords do not match', 'code' => 422];
            }
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $this->userRepo->update($id, $updateData);
        $updated = $this->userRepo->findById($id);
        unset($updated['password']);
        return ['message' => 'User updated', 'user' => $updated];
    }

    public function toggleStatus(int $id): array
    {
        $user = $this->userRepo->findById($id);
        if (!$user) return ['error' => 'User not found', 'code' => 404];
        if ($user['role'] === 'admin') return ['error' => 'Admin status cannot be changed', 'code' => 403];

        $newStatus = $user['status'] === 'active' ? 'banned' : 'active';
        $this->userRepo->update($id, ['status' => $newStatus]);

        return ['message' => 'Status updated', 'status' => $newStatus];
    }

    public function delete(int $id): array
    {
        $user = $this->userRepo->findById($id);

        if (!$user) return ['error' => 'User not found', 'code' => 404];
        if ($user['role'] === 'admin') return ['error' => 'Admin cannot be deleted', 'code' => 403];

        $this->userRepo->delete($id);
        return ['message' => 'User deleted'];
    }

    public function updateProfile(int $id, array $data): array
    {
        if (empty($data['name']) || empty($data['username']) || empty($data['email'])) {
            return ['error' => 'Name, username and email are required', 'code' => 422];
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format', 'code' => 422];
        }

        $existing = $this->userRepo->findByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== $id) {
            return ['error' => 'Email already taken', 'code' => 409];
        }

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
