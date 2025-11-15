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
            'show_in_navbar' => $data['show_in_navbar'] ?? false,
            'show_in_homepage' => $data['show_in_homepage'] ?? false,
            'template_id' => $data['template_id'],
        ]);
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->getTranslations('name'),
            'show_in_navbar' => $data['show_in_navbar'] ?? $category->show_in_navbar,
            'show_in_homepage' => $data['show_in_homepage'] ?? $category->show_in_homepage,
            'template_id' => $data['template_id'] ?? $category->template_id,
        ]);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
