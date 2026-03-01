<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface
{
    public function getAll(int $limit, int $offset, ?int $userId): array;
    public function find(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
