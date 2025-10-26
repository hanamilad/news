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
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $video = $this->repo->create($input);
            $this->log($user->id, 'create', Video::class, $video->id, null, $video->toArray());
            return $video;
        });
    }

    public function update(int $id, array $input): Video
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $video = $this->repo->findById($id);
            $old = $video->toArray();
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
            $deleted = $this->repo->delete($video);
            $this->log($user->id, 'delete', Video::class, $video->id, $old, null);
            return $deleted;
        });
    }
}
