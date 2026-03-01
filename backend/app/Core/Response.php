<?php

namespace App\Core;

class Response
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data);
    }

    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): void
    {
        $payload = ['message' => $message];
        if ($data !== null) {
            $payload = array_merge($payload, is_array($data) ? $data : ['data' => $data]);
        }
        self::json($payload, $status);
    }

    public static function error(string $message, int $status = 400): void
    {
        self::json(['message' => $message], $status);
    }
}
