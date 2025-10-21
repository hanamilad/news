<?php

namespace App\Services\Video;

use App\Repositories\Video\VideoRepository;
use App\Models\Video;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class VideoService
{
    use LogActivity;
    public function __construct(protected VideoRepository $repo) {}
    public function create(array $input): Video
    {
        return DB::transaction(function () use ($input) {
            $video = $this->repo->create($input);
            $this->log($video->user_id, 'create', Video::class, $video->id, null, $video->toArray());
            return $video;
        });
    }

    public function update(int $id, array $input): Video
    {
        return DB::transaction(function () use ($id, $input) {
            $video = $this->repo->findById($id);
            $old = $video->toArray();
            $updated = $this->repo->update($video, $input);
            $this->log( request()->user()->id ?? $updated->user_id, 'update', Video::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $video = $this->repo->findById($id);
            $old = $video->toArray();
            $deleted = $this->repo->delete($video);
            $this->log(request()->user()->id ?? $video->user_id, 'delete', Video::class, $video->id, $old, null);
            return $deleted;
        });
    }
}
