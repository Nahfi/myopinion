<?php

namespace App\Http\Controllers\Admin;

use App\Services\CommentService;

class CommentController
{
    private CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    /** GET /admin/comments/pending */
    public function pending(): void
    {
        echo json_encode($this->commentService->getPending());
    }

    /** GET /admin/posts/:postId/comments */
    public function allForPost(string $postId): void
    {
        echo json_encode($this->commentService->getAllComments((int)$postId));
    }

    /** PATCH /admin/comments/:id/approve */
    public function approve(string $id): void
    {
        $result = $this->commentService->approve((int)$id);
        $this->respond($result);
    }

    /** PATCH /admin/comments/:id/reject */
    public function reject(string $id): void
    {
        $result = $this->commentService->reject((int)$id);
        $this->respond($result);
    }

    /** DELETE /admin/comments/:id */
    public function destroy(string $id): void
    {
        $result = $this->commentService->delete((int)$id);
        $this->respond($result);
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
