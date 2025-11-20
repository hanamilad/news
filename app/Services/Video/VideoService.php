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
    public function __construct(protected VideoRepository $repo) {}
    public function create(array $input, $video): Video
    {
        return DB::transaction(function () use ($input, $video) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['video_path'] = $this->storeVideo($video);
            $video = $this->repo->create($input);
            $this->log($user->id, 'create', Video::class, $video->id, null, $video->toArray());
            return $video;
        });
    }

    public function update(int $id, array $input, $video): Video
    {
        return DB::transaction(function () use ($id, $input, $video) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $video = $this->repo->findById($id);
            $old = $video->toArray();
            $uploaded = $this->storeVideo($video);
            if (!empty($uploaded)) {
                $originalPath = ltrim($video->getRawOriginal('video_path'), '/');
                if ($video->video_path && Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
                $input['video_path'] = $uploaded;
            }
            $updated = $this->repo->update($video, $input);
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
}
