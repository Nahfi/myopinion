<?php

namespace App\Repositories;

use App\Database;
use App\Repositories\Interfaces\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByEmailOrUsername(string $identifier): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1'
        );
        $stmt->execute([$identifier, $identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find(int $id): ?array
    {
        return $this->findById($id);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, username, email, role, created_at FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, username, email, role, created_at FROM users WHERE username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, username, email, role,status, created_at FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, username, password, role)
             VALUES (:name, :email, :username, :password, 'user')"
        );
        $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':username' => $data['username'],
            ':password' => $data['password'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, username, email, role,status,created_at FROM users ORDER BY created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['name', 'username', 'email', 'password', 'status'] as $field) {
            if (isset($data[$field])) {
                $fields[]          = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[':id'] = $id;
        $stmt          = $this->pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id');
        return $stmt->execute($params);
    }
}
