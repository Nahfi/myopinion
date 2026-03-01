<?php

namespace App\Repositories\Interfaces;

interface ReactionRepositoryInterface
{
    public function getTypes(): array;
    public function createType(array $data): int;
    public function updateType(int $id, array $data): bool;
    public function deleteType(int $id): bool;
    public function toggle(int $userId, int $postId, int $reactionTypeId): string;
    public function getByPost(int $postId, ?int $userId): array;
}
