<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function getConnection()
    {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . $_ENV['DB_HOST'] .
                       ';port=' . $_ENV['DB_PORT'] .
                       ';dbname=' . $_ENV['DB_NAME'];

                self::$instance = new PDO(
                    $dsn,
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['message' => 'Database connection failed']);
                exit;
            }
        }

        return self::$instance;
    }
}
