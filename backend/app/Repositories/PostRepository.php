<?php

namespace App\Repositories;

use App\Database;
use App\Repositories\Interfaces\PostRepositoryInterface;
use PDO;

class PostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function all(?int $userId = null): array
    {
        return $this->getAll(PHP_INT_MAX, 0, $userId);
    }

    public function getAll(int $limit, int $offset, ?int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                p.*,
                u.username AS author,
                u.name AS author_name,
                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id AND c.status = "active") AS total_comments,
                (SELECT COUNT(*) FROM reaction r WHERE r.post_id = p.id) AS total_reactions,
                (SELECT r.reaction_type_id FROM reaction r WHERE r.post_id = p.id AND r.user_id = :user_id2 LIMIT 1) AS user_reaction_type_id,
                (SELECT rt.emoji FROM reaction r
                    JOIN reaction_types rt ON r.reaction_type_id = rt.id
                    WHERE r.post_id = p.id AND r.user_id = :user_id LIMIT 1) AS user_reaction_emoji,
                (
                    SELECT JSON_ARRAYAGG(JSON_OBJECT("id", rt.id, "name", rt.name, "emoji", rt.emoji, "count", rc.count))
                    FROM reaction_types rt
                    LEFT JOIN (
                        SELECT reaction_type_id, COUNT(*) AS count
                        FROM reaction
                        WHERE post_id = p.id
                        GROUP BY reaction_type_id
                    ) rc ON rc.reaction_type_id = rt.id
                    WHERE rc.count > 0
                ) AS reaction_summary
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue(':user_id', $userId ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':user_id2', $userId ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON reaction_summary for each post
        foreach ($posts as &$post) {
            $post['reaction_summary'] = $post['reaction_summary'] ? json_decode($post['reaction_summary'], true) : [];
        }

        return $posts;
    }

    public function find(int $id, ?int $userId = null): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT p.*, u.username AS author, u.name AS author_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (title, content, image_url, user_id) VALUES (:title, :content, :image_url, :user_id)'
        );
        $stmt->execute([
            ':title'     => $data['title'],
            ':content'   => $data['content'],
            ':image_url' => $data['image_url'] ?? '',
            ':user_id'   => $data['user_id'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['title', 'content', 'image_url'] as $field) {
            if (isset($data[$field])) {
                $fields[]          = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[':id'] = $id;
        $stmt          = $this->pdo->prepare('UPDATE posts SET ' . implode(', ', $fields) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
