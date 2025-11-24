<?php

namespace App\Services\ReelGroup;

use App\Repositories\ReelGroup\ReelGroupRepository;
use App\Repositories\Reel\ReelRepository;
use App\Models\ReelGroup;
use App\Models\Reel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class ReelGroupService
{
    use LogActivity;

    public function __construct(
        protected ReelGroupRepository $repo,
        protected ReelRepository $reelRepo
    ) {}

    public function create(array $input): ReelGroup
    {
        return DB::transaction(function () use ($input) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $group = $this->repo->create($input);
            if (isset($input['reels']) && is_array($input['reels'])) {
                foreach ($input['reels'] as $index => $reelData) {
                    $this->createReel($group->id, $reelData, $index, $user->id);
                }
            }
            $this->log($user->id, 'اضافة', ReelGroup::class, $group->id, null, $group->toArray());

            return $group->fresh()->load('reels');
        });
    }

    public function update(int $id, array $input): ReelGroup
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $group = $this->repo->findById($id);
            $old = $group->toArray();
            $updated = $this->repo->update($group, $input);
            if (isset($input['reels']) && is_array($input['reels'])) {
                $sentReelIds = [];
                foreach ($input['reels'] as $index => $reelData) {
                    if (isset($reelData['id']) && !empty($reelData['id'])) {
                        $reel = $this->updateReel($reelData['id'], $reelData, $index, $user->id);
                        $sentReelIds[] = (int)$reel->id;
                    } else {
                        $reel = $this->createReel($group->id, $reelData, $index, $user->id);
                        $sentReelIds[] = (int)$reel->id;
                    }
                }
                $reelsToDelete = $group->reels()->whereNotIn('id', $sentReelIds)->get();
                foreach ($reelsToDelete as $reel) {
                    $this->deleteReelWithFile($reel, $user->id);
                }
            }
            $this->log($user->id, 'تعديل', ReelGroup::class, $updated->id, $old, $updated->toArray());
            return $updated->fresh()->load('reels');
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $group = $this->repo->findById($id);
            $old = $group->toArray();
            foreach ($group->reels as $reel) {
                $this->deleteReelFile($reel);
            }
            $deleted = $this->repo->delete($group);
            $this->log($user->id, 'حذف', ReelGroup::class, $group->id, $old, null);
            return $deleted;
        });
    }

    protected function createReel(int $groupId, array $data, int $index, int $userId): Reel
    {
        $data['reel_group_id'] = $groupId;
        $data['user_id'] = $userId;
        $data['sort_order'] = $data['sort_order'] ?? $index;
        $data['path'] = $this->handlePath($data);
        $reel = $this->reelRepo->create($data);
        $this->log($userId, 'اضافة', Reel::class, $reel->id, null, $reel->toArray());
        return $reel;
    }

    protected function updateReel(int $id, array $data, int $index, int $userId): Reel
    {
        $reel = $this->reelRepo->findById($id);
        $old = $reel->toArray();
        $oldRawPath = $reel->getRawOriginal('path');
        $data['sort_order'] = $data['sort_order'] ?? $index;
        $newPath = $this->handlePath($data, $oldRawPath, $reel->type);
        if ($newPath && $newPath !== $oldRawPath && $this->isUploadedFile($reel)) {
            $this->deleteReelFile($reel);
        }
        if ($newPath) {
            $data['path'] = $newPath;
        }
        $updated = $this->reelRepo->update($reel, $data);
        $this->log($userId, 'تعديل', Reel::class, $updated->id, $old, $updated->toArray());
        return $updated;
    }

    protected function deleteReelWithFile(Reel $reel, int $userId): bool
    {
        $old = $reel->toArray();
        $this->deleteReelFile($reel);
        $deleted = $this->reelRepo->delete($reel);
        $this->log($userId, 'حذف', Reel::class, $reel->id, $old, null);
        return $deleted;
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
            if ($oldPath && $input['path'] === $oldPath) {
                return $oldPath;
            }
            return $input['path'];
        }

        return $oldPath;
    }

    protected function deleteReelFile(Reel $reel): void
    {
        if ($reel->path && $this->isUploadedFile($reel)) {
            $originalPath = ltrim($reel->getRawOriginal('path'), '/');
            if (Storage::disk('spaces')->exists($originalPath)) {
                Storage::disk('spaces')->delete($originalPath);
            }
        }
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
