<?php

namespace App\Http\Controllers\Admin;

use App\Services\ReactionService;

class ReactionTypeController
{
    private ReactionService $reactionService;

    public function __construct()
    {
        $this->reactionService = new ReactionService();
    }

    /** GET /admin/reaction-types */
    public function index(): void
    {
        echo json_encode($this->reactionService->getAllTypes());
    }

    /** POST /admin/reaction-types */
    public function store(): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->reactionService->createType($data);
        $this->respond($result, 201);
    }

    /** PUT /admin/reaction-types/:id */
    public function update(string $id): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->reactionService->updateType((int)$id, $data);
        $this->respond($result);
    }

    /** DELETE /admin/reaction-types/:id */
    public function destroy(string $id): void
    {
        $result = $this->reactionService->deleteType((int)$id);
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
