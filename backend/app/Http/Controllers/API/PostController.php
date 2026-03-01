<?php

namespace App\Http\Controllers\API;

use App\Services\PostService;
use App\Services\ReactionService;

class PostController
{
    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService();
    }

    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $userId = isset($_SERVER['user_id']) ? (int) $_SERVER['user_id'] : null;
        $result = $this->postService->getAll($page, $userId);
        echo json_encode($result);
    }

    public function show(string $id): void
    {
        $result = $this->postService->getOne((int) $id);
        $this->respond($result);
    }

    public function store(): void
    {
        // Support both JSON and multipart/form-data
        $data = !empty($_POST)
            ? $_POST
            : (json_decode(file_get_contents('php://input'), true) ?? []);

        $result = $this->postService->create($data, (int) $_SERVER['user_id']);
        $this->respond($result, 201);
    }

    public function update(string $id): void
    {
        // Support multipart/form-data for file uploads via POST + _method=PUT
        $data = !empty($_POST)
            ? $_POST
            : (json_decode(file_get_contents('php://input'), true) ?? []);

        $result = $this->postService->update((int) $id, $data);
        $this->respond($result);
    }

    public function destroy(string $id): void
    {
        echo 'Deleted';
        $result = $this->postService->delete((int) $id);
        $this->respond($result);
    }

    public function reaction(string $postId): void
    {
        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $service = new ReactionService();
        $result  = $service->toggle((int) $_SERVER['user_id'], (int) $postId, $data);
        $this->respond($result);
    }

    public function reactionTypes(): void
    {
        $service = new ReactionService();
        echo json_encode($service->getTypes());
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
