<?php

namespace App\Models;

use App\Database;
use PDO;

class ReactionType
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM reaction_types ORDER BY sort_order ASC'
        );
        return $stmt->fetchAll();
    }

    public function getActive(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM reaction_types WHERE is_active = 1 ORDER BY sort_order ASC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reaction_types WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $emoji, int $sortOrder = 0): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reaction_types (name, emoji, is_active, sort_order) VALUES (?, ?, 1, ?)'
        );
        $stmt->execute([$name, $emoji, $sortOrder]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        if (isset($data['name']))       { $fields[] = 'name = ?';       $values[] = $data['name']; }
        if (isset($data['emoji']))      { $fields[] = 'emoji = ?';      $values[] = $data['emoji']; }
        if (isset($data['is_active']))  { $fields[] = 'is_active = ?';  $values[] = (int)$data['is_active']; }
        if (isset($data['sort_order'])) { $fields[] = 'sort_order = ?'; $values[] = (int)$data['sort_order']; }

        if (empty($fields)) return false;

        $values[] = $id;
        $stmt = $this->pdo->prepare('UPDATE reaction_types SET ' . implode(', ', $fields) . ' WHERE id = ?');
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM reaction_types WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
