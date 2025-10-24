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
            $input['path'] = $this->handlePath($input);
            $reel = $this->repo->create($input);
            $this->log($reel->user_id,'create',Reel::class,$reel->id,null,$reel->toArray());
            return $reel;
        });
    }

    public function update(int $id, array $input): Reel
    {
        return DB::transaction(function () use ($id, $input) {
            $reel = $this->repo->findById($id);
            $old = $reel->toArray();
            $newPath = $this->handlePath($input, $reel->path);
            if ($newPath && $newPath !== $reel->path && $reel->type === 'image') {
                if ($reel->path && Storage::disk('public')->exists($reel->path)) {
                    Storage::disk('public')->delete($reel->path);
                }
                $input['path'] = $newPath;
            }
            $updated = $this->repo->update($reel, $input);
            $this->log(request()->user()->id ?? $updated->user_id,'update',Reel::class,$updated->id,$old,$updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $reel = $this->repo->findById($id);
            $old = $reel->toArray();

            if ($reel->type === 'image' && $reel->path && Storage::disk('public')->exists($reel->path)) {
                Storage::disk('public')->delete($reel->path);
            }

            $deleted = $this->repo->delete($reel);

            $this->log(request()->user()->id ?? $reel->user_id,'delete',Reel::class,$reel->id,$old,null);

            return $deleted;
        });
    }


    protected function handlePath(array $input, ?string $oldPath = null): ?string
    {
        $path = $oldPath;
        if ($input['type'] === 'image') {
            if (isset($input['path']) && $input['path'] instanceof UploadedFile) {
                $path = $input['path']->store('reel_images', ['disk' => 'public']);
            } elseif (isset($input['path']) && is_string($input['path'])) {
                $path = $input['path'];
            }
        } elseif ($input['type'] === 'video') {
            $path = $input['path'] ?? $oldPath;
        }
        return $path;
    }
}
