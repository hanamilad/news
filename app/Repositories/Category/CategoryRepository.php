<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        $gridOrder = $this->validateGridOrder($data['grid_order'] ?? null);
        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'show_in_navbar' => $data['show_in_navbar'] ?? false,
            'show_in_homepage' => $data['show_in_homepage'] ?? false,
            'show_in_grid' => $data['show_in_grid'] ?? false,
            'grid_order' => $gridOrder,
            'template_id' => $data['template_id'],
        ]);
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['grid_order'])) {
            $gridOrder = $this->validateGridOrder($data['grid_order'], $category->id);
        } else {
            $gridOrder = $category->grid_order;
        }

        $category->update([
            'name' => $data['name'] ?? $category->getTranslations('name'),
            'description' => $data['description'] ?? $category->getTranslations('description'),
            'show_in_navbar' => $data['show_in_navbar'] ?? $category->show_in_navbar,
            'show_in_homepage' => $data['show_in_homepage'] ?? $category->show_in_homepage,
            'show_in_grid' => $data['show_in_grid'] ?? $category->show_in_grid,
            'grid_order' => $gridOrder,
            'template_id' => $data['template_id'] ?? $category->template_id,
        ]);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
    protected function validateGridOrder(?int $gridOrder, ?int $excludeId = null): int
    {
        if (!$gridOrder) {
            return (Category::max('grid_order') ?? 0) + 1;
        }

        return DB::transaction(function () use ($gridOrder, $excludeId) {
            $categories = Category::where('grid_order', '>=', $gridOrder)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->orderBy('grid_order', 'desc')
                ->lockForUpdate()
                ->get();
            foreach ($categories as $category) {
                $category->grid_order = $category->grid_order + 1;
                $category->save();
            }
            return $gridOrder;
        });
    }
}
