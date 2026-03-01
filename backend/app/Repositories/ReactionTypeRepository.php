<?php

namespace App\Repositories;

use App\Models\ReactionType;
use App\Repositories\Interfaces\ReactionTypeRepositoryInterface;

class ReactionTypeRepository implements ReactionTypeRepositoryInterface
{
    private ReactionType $model;

    public function __construct()
    {
        $this->model = new ReactionType();
    }

    public function all(): array
    {
        return $this->model->all();
    }

    public function getActive(): array
    {
        return $this->model->getActive();
    }

    public function findById(int $id): ?array
    {
        return $this->model->findById($id);
    }

    public function create(string $name, string $emoji, int $sortOrder = 0): int
    {
        return $this->model->create($name, $emoji, $sortOrder);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
