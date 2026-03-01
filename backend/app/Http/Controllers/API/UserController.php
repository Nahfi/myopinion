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

    public function destroy(string $id): void
    {
        $result = $this->userService->delete((int)$id);
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
