<?php

namespace App\Http\Controllers\API;

use App\Services\UserService;

class ProfileController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /** GET /profile */
    public function show(): void
    {
        $result = $this->userService->getOne((int) $_SERVER['user_id']);
        $this->respond($result);
    }

    /** PUT /profile */
    public function update(): void
    {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->userService->updateProfile((int) $_SERVER['user_id'], $data);
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
