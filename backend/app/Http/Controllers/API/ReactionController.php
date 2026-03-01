<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\ReactionRepository;
use App\Repositories\ReactionTypeRepository;
use App\Services\ReactionService;

class ReactionController extends Controller
{
    private ReactionService $reactionService;

    public function __construct()
    {
        $this->reactionService = new ReactionService(
            new ReactionRepository(),
            new ReactionTypeRepository()
        );
    }

    /** GET /reaction-types — all active types for picker */
    public function types(): void
    {
        $this->json(['types' => $this->reactionService->getReactionTypes()]);
    }

    /** POST /reaction/{postId} — toggle a reaction */
    public function toggle(string $postId): void
    {
        try {
            $data   = $this->input();
            $typeId = (int) ($data['reaction_type_id'] ?? 0);

            if (!$typeId) {
                $this->error('reaction_type_id is required', 422);
                return;
            }

            $result = $this->reactionService->toggleReaction($this->userId(), (int) $postId, $typeId);
            $this->json($result);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage(), 422);
        }
    }
}
