<?php

namespace App\Models;

use App\Database;
use PDO;

class Reaction
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function toggle($userId, $postId)
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM reaction WHERE user_id=? AND post_id=?'
        );
        $stmt->execute([$userId, $postId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $del = $this->pdo->prepare('DELETE FROM reaction WHERE id=?');
            $del->execute([$existing['id']]);
            return 'removed';
        } else {
            $ins = $this->pdo->prepare(
                'INSERT INTO reaction (user_id,post_id,created_at)
                 VALUES (?,?,NOW())'
            );
            $ins->execute([$userId, $postId]);
            return 'added';
        }
    }
}
