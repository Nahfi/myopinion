<?php

namespace App\Http\Controllers\API;

use App\Services\CommentService;

class CommentController
{
    private CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    /** GET /posts/:postId/comments — active only */
    public function index(string $postId): void
    {
        $result = $this->commentService->getActiveComments((int) $postId);
        echo json_encode($result);
    }

    /** POST /posts/:postId/comments — submit (goes to pending) */
    public function store(string $postId): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->commentService->store((int) $postId, (int) $_SERVER['user_id'], $data);
        $this->respond($result, 201);
    }

    private function respond(array $result, int $successCode = 200): void
    {
        if (isset($result['error'])) {
            http_response_code($result['code'] ?? 400);
            echo json_encode(['message' => $result['error']]);
        } else {
            http_response_code($successCode);
            echo json_encode($result);
        }
    }
}
