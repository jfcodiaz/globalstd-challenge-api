<?php

namespace App\Repositories;

use App\Models\ModelInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected string $modelClass;

    protected ?ModelInterface $model = null;

    protected function model(): Model
    {
        if ($this->model === null) {
            $this->model = $this->resolveModel();
        }

        return $this->model;
    }

    protected function resolveModel(): Model
    {
        return app($this->modelClass);
    }

    public function firstOrCreate(array $where, array $data = []): Model
    {
        return $this->model()->firstOrCreate($where, $data);
    }

    public function create(array $data): Model
    {
        return $this->model()->create($data);
    }

    public function getRandom(): ?Model
    {
        return $this->model()->inRandomOrder()->first();
    }
}
