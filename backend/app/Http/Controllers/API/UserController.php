<?php

namespace App\Http\Controllers\API;

use App\Models\User;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Get all users (Admin only)
     */
    public function index()
    {
        if ($_SERVER['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $users = $this->userModel->all();

        echo json_encode([
            'status' => 'success',
            'users'  => $users
        ]);
    }

    /**
     * Show single user
     */
    public function show($id)
    {
        if ($_SERVER['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
            return;
        }

        echo json_encode($user);
    }

    /**
     * Update user role (Admin only)
     */
    public function updateRole($id, $data)
    {
        if ($_SERVER['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $role = $data['role'] ?? null;

        if (!in_array($role, ['user', 'admin'])) {
            http_response_code(422);
            echo json_encode(['message' => 'Invalid role']);
            return;
        }

        $this->userModel->updateRole($id, $role);

        echo json_encode(['message' => 'User role updated']);
    }

    /**
     * Delete user (Admin only)
     */
    public function destroy($id)
    {
        if ($_SERVER['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $this->userModel->delete($id);

        echo json_encode(['message' => 'User deleted']);
    }
}
