<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\Reaction;

class PostController
{
    private Post $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }

    public function index()
    {
        $page   = $_GET['page'] ?? 1;
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $userId = $_SERVER['user_id'] ?? null;

        $posts = $this->postModel->getAll($limit, $offset, $userId);

        echo json_encode(['posts' => $posts]);
    }

    public function store($data)
    {
        $id = $this->postModel->create(
            $data['title'],
            $data['content'],
            '',
            $_SERVER['user_id']
        );

        echo json_encode(['post_id' => $id]);
    }

    public function destroy($id)
    {
        $this->postModel->delete($id);
        echo json_encode(['message' => 'Deleted']);
    }

    public function reaction($postId)
    {
        $reaction = new Reaction();
        $result   = $reaction->toggle($_SERVER['user_id'], $postId);
        echo json_encode(['reaction' => $result]);
    }
}
