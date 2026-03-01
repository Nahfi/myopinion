<?php

namespace App\Models;

use App\Database;
use PDO;

class TokenBlacklist
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function add($token)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO token_blacklist (token) VALUES (?)'
        );
        return $stmt->execute([$token]);
    }
}
