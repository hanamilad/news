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
    public function __construct(protected ArticleRepository $repo) {}
    public function create(array $input,  $file): Article
    {
        return DB::transaction(function () use ($input, $file) {
            $input['author_image'] = $this->storeAuthorImage($file);
            $article = $this->repo->create($input);
            $this->log($article->user_id, 'create', Article::class, $article->id, null, $article->toArray());
            return $article;
        });
    }

    public function update(int $id, array $input, $file): Article
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $article = $this->repo->findById($id);
            $old = $article->toArray();
            $uploaded = $this->storeAuthorImage($file);
            if (!empty($uploaded)) {
                if ($article->author_image &&Storage::disk('public')->exists($article->author_image)) {
                    Storage::disk('public')->delete($article->author_image);
                }
                $input['author_image'] = $uploaded;
            }
            $updated = $this->repo->update($article, $input);
            $this->log(request()->user()->id ?? $updated->user_id, 'update', Article::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $article = $this->repo->findById($id);
            $old = $article->toArray();
            if ($article->author_image && Storage::disk('public')->exists($article->author_image)) {
                Storage::disk('public')->delete($article->author_image);
            }
            $deleted = $this->repo->delete($article);
            $this->log(request()->user()->id ?? $article->user_id, 'delete', Article::class, $article->id, $old, null);
            return $deleted;
        });
    }

    protected function storeAuthorImage($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('article_images', ['disk' => 'public']);
        }
        return $path;
    }
}
