<?php

namespace App\Services\Reel;

use App\Repositories\Reel\ReelRepository;
use App\Models\Reel;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ReelService
{
    use LogActivity;

    public function __construct(protected ReelRepository $repo) {}

    public function create(array $input): Reel
    {
        return DB::transaction(function () use ($input) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['path'] = $this->handlePath($input);
            $reel = $this->repo->create($input);
            $this->log($user->id, 'create', Reel::class, $reel->id, null, $reel->toArray());
            return $reel;
        });
    }

    public function update(int $id, array $input): Reel
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $reel = $this->repo->findById($id);
            $old = $reel->toArray();

            $newPath = $this->handlePath($input, $reel->path, $reel->type);

            if ($newPath && $newPath !== $reel->path && $this->isUploadedFile($reel)) {
                $originalPath = ltrim($reel->getRawOriginal('path'), '/');
                if (Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
            }

            if ($newPath) {
                $input['path'] = $newPath;
            }
            $updated = $this->repo->update($reel, $input);
            $this->log($user->id, 'update', Reel::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $reel = $this->repo->findById($id);
            $old = $reel->toArray();

            if ($reel->path && $this->isUploadedFile($reel)) {
                $originalPath = ltrim($reel->getRawOriginal('path'), '/');
                if (Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
            }

            $deleted = $this->repo->delete($reel);
            $this->log($user->id, 'delete', Reel::class, $reel->id, $old, null);
            return $deleted;
        });
    }


    protected function handlePath(array $input, ?string $oldPath = null, ?string $oldType = null): ?string
    {
        if (!isset($input['path'])) {
            return $oldPath;
        }
        if ($input['path'] instanceof UploadedFile) {
            $folderName = $input['type'] === 'image' ? 'reel_images' : 'reel_videos';
            return $input['path']->store($folderName, ['disk' => 'spaces']);
        }
        if (is_string($input['path'])) {
            return $input['path'];
        }
        return $oldPath;
    }


    protected function isUploadedFile(Reel $reel): bool
    {
        $path = $reel->getRawOriginal('path');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }
        return str_contains($path, 'reel_images') || str_contains($path, 'reel_videos');
    }
}
