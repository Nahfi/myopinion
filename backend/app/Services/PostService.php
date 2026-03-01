<?php

namespace App\Services;

use App\Repositories\PostRepository;

class PostService
{
    private PostRepository $postRepo;

    public function __construct()
    {
        $this->postRepo = new PostRepository();
    }

    public function getAll(int $page = 1, ?int $userId = null): array
    {
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $posts  = $this->postRepo->getAll($limit, $offset, $userId);
        return ['posts' => $posts];
    }

    public function getOne(int $id): array
    {
        $post = $this->postRepo->find($id);
        if (!$post) {
            return ['error' => 'Post not found', 'code' => 404];
        }
        return $post;
    }

    public function create(array $data, int $userId): array
    {
        if (empty($data['title']) || empty($data['content'])) {
            return ['error' => 'Title and content are required', 'code' => 422];
        }

        // Handle image upload
        $imageUrl = '';
        if (!empty($_FILES['image']['name'])) {
            $result = $this->uploadImage($_FILES['image']);
            if (isset($result['error'])) return $result;
            $imageUrl = $result['url'];
        }

        $id = $this->postRepo->create([
            'title'     => $data['title'],
            'content'   => $data['content'],
            'image_url' => $imageUrl,
            'user_id'   => $userId,
        ]);

        return ['message' => 'Post created', 'post_id' => $id];
    }

    public function update(int $id, array $data): array
    {
        $post = $this->postRepo->find($id);
        if (!$post) {
            return ['error' => 'Post not found', 'code' => 404];
        }

        $updateData = [];
        if (!empty($data['title']))   $updateData['title']   = $data['title'];
        if (!empty($data['content'])) $updateData['content'] = $data['content'];

        if (!empty($_FILES['image']['name'])) {
            $result = $this->uploadImage($_FILES['image']);
            if (isset($result['error'])) return $result;
            $updateData['image_url'] = $result['url'];
        }

        $this->postRepo->update($id, $updateData);
        return ['message' => 'Post updated'];
    }

    public function delete(int $id): array
    {
        $post = $this->postRepo->find($id);
        if (!$post) {
            return ['error' => 'Post not found', 'code' => 404];
        }
        $this->postRepo->delete($id);
        return ['message' => 'Post deleted'];
    }

    private function uploadImage(array $file): array
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            return ['error' => 'Invalid image type', 'code' => 422];
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['error' => 'Image too large (max 5MB)', 'code' => 422];
        }
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $dest     = __DIR__ . '/../../public/uploads/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Failed to upload image', 'code' => 500];
        }
        return ['url' => '/uploads/' . $filename];
    }
}
