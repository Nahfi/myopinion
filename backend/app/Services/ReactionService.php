<?php

namespace App\Services;

use App\Repositories\ReactionRepository;

class ReactionService
{
    private ReactionRepository $reactionRepo;

    public function __construct()
    {
        $this->reactionRepo = new ReactionRepository();
    }

    /** Public: list active reaction types */
    public function getTypes(): array
    {
        return ['types' => $this->reactionRepo->getTypes()];
    }

    /** Admin: list all reaction types */
    public function getAllTypes(): array
    {
        return ['types' => $this->reactionRepo->getAllTypes()];
    }

    /** Admin: create a new reaction type */
    public function createType(array $data): array
    {
        if (empty($data['name']) || empty($data['emoji'])) {
            return ['error' => 'Name and emoji are required', 'code' => 422];
        }
        $id = $this->reactionRepo->createType([
            'name'       => $data['name'],
            'emoji'      => $data['emoji'],
            'is_active'  => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'sort_order' => (int)($data['sort_order'] ?? 0),
        ]);
        return ['message' => 'Reaction type created', 'id' => $id];
    }

    /** Admin: update a reaction type */
    public function updateType(int $id, array $data): array
    {
        $this->reactionRepo->updateType($id, $data);
        return ['message' => 'Reaction type updated'];
    }

    /** Admin: delete a reaction type */
    public function deleteType(int $id): array
    {
        $this->reactionRepo->deleteType($id);
        return ['message' => 'Reaction type deleted'];
    }

    /** User: toggle reaction on a post */
    public function toggle(int $userId, int $postId, array $data): array
    {
        if (empty($data['reaction_type_id'])) {
            return ['error' => 'reaction_type_id is required', 'code' => 422];
        }
        $action = $this->reactionRepo->toggle($userId, $postId, (int)$data['reaction_type_id']);
        $counts = $this->reactionRepo->getByPost($postId, $userId);
        return array_merge($counts, ['action' => $action]);
    }
}
