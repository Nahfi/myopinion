<?php

namespace App\Models;

use App\Database;
use PDO;

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByEmailOrUsername($identifier)
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = ? OR username = ?'
        );
        $stmt->execute([$identifier, $identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $username, $password)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name,email,username,password,role)
             VALUES (?,?,?,?, 'user')"
        );

        $stmt->execute([$name, $email, $username, $password]);
        return $this->pdo->lastInsertId();
    }

    public function all()
    {
        $stmt = $this->pdo->query(
            'SELECT id,name,username,email,role,created_at
             FROM users ORDER BY created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
