<?php

namespace App\Http\Controllers\API;

use App\Services\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index(): void
    {
        echo json_encode($this->userService->getAll());
    }

    public function store(): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->userService->create($data);
        $this->respond($result, 201);
    }

    public function update(string $id): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->userService->adminUpdate((int) $id, $data);
        $this->respond($result);
    }

    public function destroy(string $id): void
    {
        $result = $this->userService->delete((int) $id);
        $this->respond($result);
    }

    public function toggleStatus(string $id): void
    {
        $result = $this->userService->toggleStatus((int) $id);
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
