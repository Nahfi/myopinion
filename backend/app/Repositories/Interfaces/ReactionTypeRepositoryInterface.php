<?php

namespace App\Repositories\Interfaces;

interface ReactionTypeRepositoryInterface
{
    public function all(): array;
    public function getActive(): array;
    public function findById(int $id): ?array;
    public function create(string $name, string $emoji, int $sortOrder): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
