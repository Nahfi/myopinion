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
}
