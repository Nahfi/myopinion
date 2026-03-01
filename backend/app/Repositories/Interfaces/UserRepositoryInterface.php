<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function findByEmailOrUsername(string $identifier): ?array;
    public function findById(int $id): ?array;
    public function create(array $data): int;
    public function all(): array;
    public function delete(int $id): bool;
    public function emailExists(string $email): bool;
    public function usernameExists(string $username): bool;
}
