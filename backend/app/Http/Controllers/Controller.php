<?php

namespace App\Http\Controllers;

use App\Core\Response;

abstract class Controller
{
    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): void
    {
        Response::success($data, $message, $status);
    }

    protected function error(string $message, int $status = 400): void
    {
        Response::error($message, $status);
    }

    protected function userId(): int
    {
        return (int) ($_SERVER['user_id'] ?? 0);
    }

    protected function userRole(): string
    {
        return $_SERVER['user_role'] ?? '';
    }

    protected function input(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? $_POST ?? [];
    }
}
