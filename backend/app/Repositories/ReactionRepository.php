<?php

namespace App\Repositories;

use App\Database;
use App\Repositories\Interfaces\ReactionRepositoryInterface;
use PDO;

class ReactionRepository implements ReactionRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // ── Reaction Types (admin managed) ─────────────────────────────────────

    public function getTypes(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM reaction_types WHERE is_active = 1 ORDER BY sort_order ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTypes(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM reaction_types ORDER BY sort_order ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createType(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reaction_types (name, emoji, is_active, sort_order) VALUES (:name, :emoji, :is_active, :sort_order)'
        );
        $stmt->execute([
            ':name'       => $data['name'],
            ':emoji'      => $data['emoji'],
            ':is_active'  => $data['is_active'] ?? 1,
            ':sort_order' => $data['sort_order'] ?? 0,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateType(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['name', 'emoji', 'is_active', 'sort_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[':id'] = $id;
        $stmt = $this->pdo->prepare('UPDATE reaction_types SET ' . implode(', ', $fields) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function deleteType(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM reaction_types WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // ── User Reactions ──────────────────────────────────────────────────────

    public function toggle(int $userId, int $postId, int $reactionTypeId): string
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, reaction_type_id FROM reaction WHERE user_id = ? AND post_id = ?'
        );
        $stmt->execute([$userId, $postId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if ((int)$existing['reaction_type_id'] === $reactionTypeId) {
                // Same reaction → remove
                $del = $this->pdo->prepare('DELETE FROM reaction WHERE id = ?');
                $del->execute([$existing['id']]);
                return 'removed';
            } else {
                // Different reaction → update
                $upd = $this->pdo->prepare(
                    'UPDATE reaction SET reaction_type_id = ? WHERE id = ?'
                );
                $upd->execute([$reactionTypeId, $existing['id']]);
                return 'updated';
            }
        }

        // New reaction
        $ins = $this->pdo->prepare(
            'INSERT INTO reaction (user_id, post_id, reaction_type_id) VALUES (?, ?, ?)'
        );
        $ins->execute([$userId, $postId, $reactionTypeId]);
        return 'added';
    }

    public function getByPost(int $postId, ?int $userId): array
    {
        // Counts per type
        $stmt = $this->pdo->prepare('
            SELECT rt.id, rt.name, rt.emoji, COUNT(r.id) AS count
            FROM reaction_types rt
            LEFT JOIN reaction r ON r.reaction_type_id = rt.id AND r.post_id = :post_id
            WHERE rt.is_active = 1
            GROUP BY rt.id, rt.name, rt.emoji
            ORDER BY rt.sort_order
        ');
        $stmt->execute([':post_id' => $postId]);
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userReaction = null;
        if ($userId) {
            $stmt2 = $this->pdo->prepare(
                'SELECT reaction_type_id FROM reaction WHERE post_id = ? AND user_id = ?'
            );
            $stmt2->execute([$postId, $userId]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            $userReaction = $row ? (int)$row['reaction_type_id'] : null;
        }

        $total = array_sum(array_column($types, 'count'));

        return [
            'types'         => $types,
            'total'         => $total,
            'user_reaction' => $userReaction,
        ];
    }
}
