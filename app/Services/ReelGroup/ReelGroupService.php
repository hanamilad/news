<?php

namespace App\Services\ReelGroup;

use App\Repositories\ReelGroup\ReelGroupRepository;
use App\Models\ReelGroup;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class ReelGroupService
{
    use LogActivity;

    public function __construct(protected ReelGroupRepository $repo) {}

    public function create(array $input): ReelGroup
    {
        return DB::transaction(function () use ($input) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $group = $this->repo->create($input);
            $this->log($user->id,'create',ReelGroup::class,$group->id,null,$group->toArray());
            return $group;
        });
    }

    public function update(int $id, array $input): ReelGroup
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $group = $this->repo->findById($id);
            $old = $group->toArray();

            $updated = $this->repo->update($group, $input);

            $this->log($user->id,'update',ReelGroup::class,$updated->id,$old,$updated->toArray()
            );

            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $group = $this->repo->findById($id);
            $old = $group->toArray();

            foreach ($group->reels as $reel) {
                if ($reel->type === 'image' && $reel->path) {
                    $originalPath = ltrim($reel->getRawOriginal('path'), '/');
                    if (Storage::disk('spaces')->exists($originalPath)) {
                        Storage::disk('spaces')->delete($originalPath);
                    }
                }
            }
            $deleted = $this->repo->delete($group);
            $this->log($user->id,'delete',ReelGroup::class,$group->id,$old,null);

            return $deleted;
        });
    }
}
