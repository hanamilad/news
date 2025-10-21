<?php

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository
{
    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        $category = Category::create([
            'name' => $data['name'],
            'template_id' => $data['template_id'],
        ]);
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->getTranslations('name'),
            'template_id' => $data['template_id'] ?? $category->template_id,
        ]);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
