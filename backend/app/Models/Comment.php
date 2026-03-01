<?php

namespace App\Models;

use App\Database;
use PDO;

class Comment
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function create($postId, $userId, $content)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comments (post_id,user_id,content)
             VALUES (?,?,?)'
        );
        return $stmt->execute([$postId, $userId, $content]);
    }

    public function getByPost($postId)
    {
        $stmt = $this->pdo->prepare('
            SELECT comments.*, users.username AS author
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = ?
            ORDER BY comments.created_at ASC
        ');

        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
