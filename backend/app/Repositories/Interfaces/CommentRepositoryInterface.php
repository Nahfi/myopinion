<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface
{
    public function create(array $data): int;
    public function getActiveByPost(int $postId): array;
    public function getAllByPost(int $postId): array;
    public function getPending(): array;
    public function updateStatus(int $id, string $status): bool;
    public function delete(int $id): bool;
    public function find(int $id): ?array;
}
