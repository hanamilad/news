<?php

namespace App\Services\Category;

use App\Repositories\Category\CategoryRepository;
use App\Models\Category;
use App\Services\Localization\TranslationService;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    use LogActivity;
    public function __construct(
        protected CategoryRepository $repo,
        protected TranslationService $translator,
    ) {}

    public function create(array $input): Category
    {
        $user = auth('api')->user();
        $this->ensureEn($input, 'name');
        $this->ensureEn($input, 'description');
        $sub = $input['sub_category_ids'] ?? [];
        unset($input['sub_category_ids']);

        $category = $this->repo->create($input);
        if (!empty($sub)) {
            $category->subCategories()->sync($sub);
        }
        $this->log($user->id, 'اضافة', Category::class, $category->id, null, $category->toArray());
        return $category;
    }

    public function update(int $id, array $input): Category
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $category = $this->repo->findById($id);
            $old = $category->toArray();
            if (array_key_exists('name', $input)) {
                $this->ensureEn($input, 'name');
            }
            if (array_key_exists('description', $input)) {
                $this->ensureEn($input, 'description');
            }
            $sub = $input['sub_category_ids'] ?? null;
            unset($input['sub_category_ids']);
            $updated = $this->repo->update($category, $input);
            if (!is_null($sub)) {
                $updated->subCategories()->sync($sub);
            }
            $this->log($user->id, 'تعديل', Category::class, $updated->id, $old, $updated->toArray());
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
            $this->log($user->id, 'حذف', Category::class, $category->id, $old, null);
            return $deleted;
        });
    }

    private function ensureEn(array &$input, string $field): void
    {
        if (!isset($input[$field]) || !is_array($input[$field])) {
            return;
        }

        $translations = $input[$field];
        $en = $translations['en'] ?? null;
        $ar = $translations['ar'] ?? null;

        if ((is_null($en) || trim((string)$en) === '') && !is_null($ar) && trim((string)$ar) !== '') {
            $input[$field]['en'] = $this->translator->translateOrFallback((string)$ar, 'ar', 'en');
        }
    }
}
