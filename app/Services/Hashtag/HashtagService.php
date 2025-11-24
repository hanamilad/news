<?php

namespace App\Services\Hashtag;

use App\Repositories\Hashtag\HashtagRepository;
use App\Models\Hashtag;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class HashtagService
{
    use LogActivity;
    public function __construct(protected HashtagRepository $repo) {}
    public function create(array $input): Hashtag
    {
        $user = auth('api')->user();
        $hashtag = $this->repo->create($input);
        $this->log($user->id, 'اضافة', Hashtag::class, $hashtag->id, null, $hashtag->toArray());
        return $hashtag;
    }

    public function update(int $id, array $input): Hashtag
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $hashtag = $this->repo->findById($id);
            $old = $hashtag->toArray();
            $updated = $this->repo->update($hashtag, $input);
            $this->log($user->id, 'تعديل', Hashtag::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $hashtag = $this->repo->findById($id);
            $old = $hashtag->toArray();
            $deleted = $this->repo->delete($hashtag);
            $this->log($user->id, 'حذف', Hashtag::class, $hashtag->id, $old, null);
            return $deleted;
        });
    }
}
