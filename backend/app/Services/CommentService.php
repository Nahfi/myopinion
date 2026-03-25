<?php

namespace App\Services;

use App\Repositories\CommentRepository;

class CommentService
{
    private CommentRepository $commentRepo;

    public function __construct()
    {
        $this->commentRepo = new CommentRepository();
    }

    /** Frontend: only approved comments */
    public function getActiveComments(int $postId): array
    {
        return [
            'comments' => $this->commentRepo->getActiveByPost($postId),
        ];
    }

    /** Admin: all comments for a post */
    public function getAllComments(int $postId): array
    {
        return [
            'comments' => $this->commentRepo->getAllByPost($postId),
        ];
    }

    public function getAllByStatus(): array
    {
        return [
            'pending'  => $this->commentRepo->getByStatus('pending'),
            'active'   => $this->commentRepo->getByStatus('active'),
            'rejected' => $this->commentRepo->getByStatus('rejected'),
        ];
    }

    /** Admin: moderation queue */
    public function getPending(): array
    {
        return [
            'comments' => $this->commentRepo->getPending(),
        ];
    }

    /** User submits a comment — always starts as pending */
    public function store(int $postId, int $userId, array $data): array
    {
        if (empty($data['content'])) {
            return ['error' => 'Comment content is required', 'code' => 422];
        }

        $id = $this->commentRepo->create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => trim($data['content']),
        ]);

        return [
            'message'    => 'Comment submitted. It will appear after moderation.',
            'comment_id' => $id,
        ];
    }

    /** Admin: approve */
    public function approve(int $id): array
    {
        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return ['error' => 'Comment not found', 'code' => 404];
        }
        $this->commentRepo->updateStatus($id, 'active');
        return ['message' => 'Comment approved'];
    }

    /** Admin: reject */
    public function reject(int $id): array
    {
        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return ['error' => 'Comment not found', 'code' => 404];
        }
        $this->commentRepo->updateStatus($id, 'rejected');
        return ['message' => 'Comment rejected'];
    }

    /** Admin: delete permanently */
    public function delete(int $id): array
    {
        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return ['error' => 'Comment not found', 'code' => 404];
        }
        $this->commentRepo->delete($id);
        return ['message' => 'Comment deleted'];
    }
    }
