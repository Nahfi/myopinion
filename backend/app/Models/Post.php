<?php

namespace App\Models;

use App\Database;
use PDO;

class Post
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll($limit, $offset, $userId)
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                posts.*, 
                users.username AS author,
                (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS total_comments,
                (SELECT COUNT(*) FROM reaction WHERE reaction.post_id = posts.id) AS total_reactions,
                (
                    SELECT COUNT(*) 
                    FROM reaction 
                    WHERE reaction.post_id = posts.id AND reaction.user_id = :user_id
                ) AS user_reacted
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ');

        $stmt->bindValue(':user_id', $userId ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('
            SELECT posts.*, users.username AS author
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $content, $imageUrl, $userId)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (title,content,image_url,user_id)
             VALUES (?,?,?,?)'
        );
        $stmt->execute([$title, $content, $imageUrl, $userId]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $title, $content)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE posts SET title=?, content=? WHERE id=?'
        );
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id=?');
        return $stmt->execute([$id]);
    }
}
