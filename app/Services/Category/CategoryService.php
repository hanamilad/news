<?php

namespace App\Services\Category;

use App\Repositories\Category\CategoryRepository;
use App\Models\Category;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    use LogActivity;
    public function __construct(protected CategoryRepository $repo) {}

    public function create(array $input): Category
    {
        $user = auth('api')->user();
        $category = $this->repo->create($input);
        $this->log($user->id, 'create', Category::class, $category->id, null, $category->toArray());
        return $category;
    }

    public function update(int $id, array $input): Category
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $category = $this->repo->findById($id);
            $old = $category->toArray();
            $updated = $this->repo->update($category, $input);
            $this->log($user->id, 'update', Category::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $category = $this->repo->findById($id);
            $old = $category->toArray();
            $deleted = $this->repo->delete($category);
            $this->log($user->id, 'delete', Category::class, $category->id, $old, null);
            return $deleted;
        });
    }
}
