<?php

namespace App\Repositories;

use App\Database;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use PDO;

class CommentRepository implements CommentRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO comments (post_id, user_id, content, status)
             VALUES (:post_id, :user_id, :content, 'pending')"
        );
        $stmt->execute([
            ':post_id' => $data['post_id'],
            ':user_id' => $data['user_id'],
            ':content' => $data['content'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getActiveByPost(int $postId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.username AS author, u.name AS author_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ? AND c.status = "active"
            ORDER BY c.created_at DESC
        ');
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByPost(int $postId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.username AS author, u.name AS author_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at DESC
        ');
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPending(): array
    {
        $stmt = $this->pdo->query('
            SELECT c.*, u.username AS author, u.name AS author_name, p.title AS post_title
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN posts  p ON c.post_id = p.id
            WHERE c.status = "pending"
            ORDER BY c.created_at ASC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE comments SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, u.username AS author FROM comments c
             JOIN users u ON c.user_id = u.id WHERE c.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
