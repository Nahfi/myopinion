<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;

class CommentController
{
    private Comment $commentModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    public function store($postId, $data)
    {
        $this->commentModel->create(
            $postId,
            $_SERVER['user_id'],
            $data['content']
        );

        echo json_encode(['message' => 'Comment added']);
    }

    public function index($postId)
    {
        $comments = $this->commentModel->getByPost($postId);
        echo json_encode(['comments' => $comments]);
    }
}
