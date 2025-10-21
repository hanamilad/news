<?php

namespace App\Services\News;

use App\Repositories\News\NewsRepository;
use App\Models\News;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class NewsService
{
    use LogActivity;
    public function __construct(protected NewsRepository $repo) {}

    public function findById(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $input, array $files = []): News
    {
        return DB::transaction(function () use ($input, $files) {
            $input['images'] = $this->storeFilesAndBuildImages($files);
            $news = $this->repo->create($input);
            $this->log($news->user_id, 'create', News::class, $news->id, null, $news->toArray());
            return $news;
        });
    }

    public function update(int $id, array $input, array $files = []): News
    {
        return DB::transaction(function () use ($id, $input, $files) {
            $news = $this->repo->findById($id);
            $old = $news->toArray();
            $uploaded = $this->storeFilesAndBuildImages($files);
            if (!empty($uploaded)) {
                $input['images'] = $uploaded;
            }
            $updated = $this->repo->update($news, $input);
            $this->log(request()->user()->id ?? $updated->user_id, 'update', News::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $news = $this->repo->findById($id);
            $old = $news->toArray();
            foreach ($news->images as $img) {
                if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }
            $deleted = $this->repo->delete($news);
            $this->log(request()->user()->id ?? $news->user_id, 'delete', News::class, $news->id, $old, null);
            return $deleted;
        });
    }

    protected function storeFilesAndBuildImages(array $files): array
    {
        $out = [];
        foreach ($files as $i => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store('news_images', ['disk' => 'public']);
                $out[] = [
                    'image_path' => $path,
                    'is_main' => $i === 0,
                ];
            }
        }
        return $out;
    }
}
