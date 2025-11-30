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
        return DB::transaction(function () use ($data) {
            $gridOrder = $this->resolveGridOrderOnCreate($data['grid_order']);
            $category = Category::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'show_in_navbar' => $data['show_in_navbar'] ?? false,
                'show_in_homepage' => $data['show_in_homepage'] ?? false,
                'show_in_grid' => $data['show_in_grid'],
                'grid_order' => $gridOrder,
                'template_id' => $data['template_id'],
            ]);

            return $category;
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $newShowInGrid = $data['show_in_grid'] ?? $category->show_in_grid;
            $requestedOrder = array_key_exists('grid_order', $data) ? $data['grid_order'] : $category->grid_order;

            if ($category->show_in_grid && ! $newShowInGrid) {
                $oldOrder = $category->grid_order ?? 0;
                if ($oldOrder) {
                    $this->shiftGridOrders($oldOrder + 1, null, -1);
                }
                $newOrder = null;
            } elseif (! $category->show_in_grid && $newShowInGrid) {
                $newOrder = $this->resolveGridOrderOnCreate($requestedOrder);
            } elseif ($newShowInGrid) {
                $oldOrder = $category->grid_order ?? 0;
                $newOrder = $requestedOrder ?? $oldOrder;
                if ($oldOrder && $newOrder && $newOrder !== $oldOrder) {
                    if ($newOrder > $oldOrder) {
                        $this->shiftGridOrders($oldOrder + 1, $newOrder, -1);
                    } else {
                        $this->shiftGridOrders($newOrder, $oldOrder - 1, 1);
                    }
                }
            } else {
                $newOrder = null;
            }

            $category->update([
                'name' => $data['name'] ?? $category->getTranslations('name'),
                'description' => $data['description'] ?? $category->getTranslations('description'),
                'show_in_navbar' => $data['show_in_navbar'] ?? $category->show_in_navbar,
                'show_in_homepage' => $data['show_in_homepage'] ?? $category->show_in_homepage,
                'show_in_grid' => $newShowInGrid,
                'grid_order' => $newOrder,
                'template_id' => $data['template_id'] ?? $category->template_id,
            ]);

            return $category->fresh();
        });
    }

    public function delete(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            $order = $category->grid_order;
            $inGrid = $category->show_in_grid;
            $deleted = (bool) $category->delete();
            if ($deleted && $inGrid && $order) {
                $this->shiftGridOrders(($order ?? 0) + 1, null, -1);
            }

            return $deleted;
        });
    }

    protected function resolveGridOrderOnCreate(?int $gridOrder): ?int
    {
        if ($gridOrder === null) {
            return (Category::where('show_in_grid', true)->max('grid_order') ?? 0) + 1;
        }

        $categories = Category::where('show_in_grid', true)
            ->where('grid_order', '>=', $gridOrder)
            ->orderBy('grid_order', 'desc')
            ->lockForUpdate()
            ->get();
        foreach ($categories as $cat) {
            $cat->grid_order = ($cat->grid_order ?? 0) + 1;
            $cat->save();
        }

        return $gridOrder;
    }

    protected function shiftGridOrders(int $from, ?int $to, int $step): void
    {
        $query = Category::where('show_in_grid', true)
            ->where('grid_order', '>=', $from);
        if ($to !== null) {
            $query->where('grid_order', '<=', $to);
        }
        $order = $step > 0 ? 'desc' : 'asc';
        $items = $query->orderBy('grid_order', $order)->lockForUpdate()->get();
        foreach ($items as $item) {
            $item->grid_order = ($item->grid_order ?? 0) + $step;
            $item->save();
        }
    }
}