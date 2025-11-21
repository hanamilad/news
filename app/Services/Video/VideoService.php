<?php

namespace App\Services\Video;

use App\Repositories\Video\VideoRepository;
use App\Models\Video;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class VideoService
{
    use LogActivity;
    public function __construct(
        protected VideoRepository $repo,
        protected \App\Services\Localization\TranslationService $translator
    ) {}
    public function create(array $input, $file): Video
    {
        return DB::transaction(function () use ($input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['description'] = $this->ensureEnTranslation($input['description'] ?? []);
            $pathFromFile = $this->storeVideo($file);
            if (!empty($pathFromFile)) {
                $input['video_path'] = $pathFromFile;
            } elseif (isset($input['video_path']) && is_string($input['video_path'])) {
                $input['video_path'] = $input['video_path'];
            }
            $video = $this->repo->create($input);
            $this->log($user->id, 'create', Video::class, $video->id, null, $video->toArray());
            return $video;
        });
    }

    public function update(int $id, array $input, $file): Video
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            if (isset($input['description']) && is_array($input['description'])) {
                $input['description'] = $this->ensureEnTranslation($input['description']);
            }
            $model = $this->repo->findById($id);
            $old = $model->toArray();

            $uploaded = $this->storeVideo($file);
            if (!empty($uploaded)) {
                $originalPath = ltrim($model->getRawOriginal('video_path'), '/');
                if ($model->video_path && Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
                $input['video_path'] = $uploaded;
            } elseif (isset($input['video_path']) && is_string($input['video_path'])) {
                $newPath = $input['video_path'];
                $oldRaw = $model->getRawOriginal('video_path');
                if ($oldRaw && $newPath !== $oldRaw && str_contains($oldRaw, 'videos')) {
                    $originalPath = ltrim($oldRaw, '/');
                    if (Storage::disk('spaces')->exists($originalPath)) {
                        Storage::disk('spaces')->delete($originalPath);
                    }
                }
            }

            $updated = $this->repo->update($model, $input);
            $this->log($user->id, 'update', Video::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $video = $this->repo->findById($id);
            $old = $video->toArray();
            $originalPath = ltrim($video->getRawOriginal('video_path'), '/');
            if ($video->video_path && Storage::disk('spaces')->exists($originalPath)) {
                Storage::disk('spaces')->delete($originalPath);
            }
            $deleted = $this->repo->delete($video);
            $this->log($user->id, 'delete', Video::class, $video->id, $old, null);
            return $deleted;
        });
    }
    protected function storeVideo($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('videos', ['disk' => 'spaces']);
        }
        return $path;
    }

    protected function ensureEnTranslation(array $trans): array
    {
        $ar = $trans['ar'] ?? null;
        $en = $trans['en'] ?? null;
        if (!$en && $ar) {
            $trans['en'] = $this->translator->translateOrFallback($ar, 'ar', 'en');
        }
        return $trans;
    }
}