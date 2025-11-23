<?php

namespace App\Services\Article;

use App\Repositories\Article\ArticleRepository;
use App\Models\Article;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    use LogActivity;
    public function __construct(
        protected ArticleRepository $repo,
        protected \App\Services\Localization\TranslationService $translator
    ) {}
    public function create(array $input,  $file): Article
    {
        return DB::transaction(function () use ($input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['title'] = $this->ensureEn($input['title'] ?? []);
            $input['content'] = $this->ensureEn($input['content'] ?? []);
            $input['author_name'] = $this->ensureEn($input['author_name'] ?? []);
            $input['author_image'] = $this->storeAuthorImage($file);
            $article = $this->repo->create($input);
            $this->log($user->id, 'create', Article::class, $article->id, null, $article->toArray());
            return $article;
        });
    }

    public function update(int $id, array $input, $file): Article
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $article = $this->repo->findById($id);
            $old = $article->toArray();
            $uploaded = $this->storeAuthorImage($file);
            if (!empty($uploaded)) {
                $originalPath = ltrim($article->getRawOriginal('author_image'), '/');
                if ($article->author_image && Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
                $input['author_image'] = $uploaded;
            }
            foreach (['title', 'content', 'author_name'] as $field) {
                if (isset($input[$field]) && is_array($input[$field])) {
                    $input[$field] = $this->ensureEn($input[$field]);
                }
            }
            $updated = $this->repo->update($article, $input);
            $this->log($user->id, 'update', Article::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $article = $this->repo->findById($id);
            $old = $article->toArray();
            $originalPath = ltrim($article->getRawOriginal('author_image'), '/');
            if ($article->author_image && Storage::disk('spaces')->exists($originalPath)) {
                Storage::disk('spaces')->delete($originalPath);
            }
            $deleted = $this->repo->delete($article);
            $this->log($user->id, 'delete', Article::class, $article->id, $old, null);
            return $deleted;
        });
    }

    protected function storeAuthorImage($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('article_images', ['disk' => 'spaces']);
        }
        return $path;
    }

    protected function ensureEn(array $trans): array
    {
        $ar = $trans['ar'] ?? null;
        $en = $trans['en'] ?? null;
        if (!$en && $ar) {
            $trans['en'] = $this->translator->translateOrFallback($ar, 'ar', 'en');
        }
        return $trans;
    }

    public function searchByAuthor(string $name)
    {
        return Article::forPublic()
            ->where(function ($q) use ($name) {
                $q->where('author_name->ar', 'LIKE', "%$name%")
                  ->orWhere('author_name->en', 'LIKE', "%$name%");
            })
            ->orderByDesc('created_at')
            ->get();
    }
}
