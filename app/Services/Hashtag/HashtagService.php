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
        $hashtag = $this->repo->create($input);
        $this->log($hashtag->user_id, 'create', Hashtag::class, $hashtag->id, null, $hashtag->toArray());
        return $hashtag;
    }

    public function update(int $id, array $input): Hashtag
    {
        return DB::transaction(function () use ($id, $input) {
            $hashtag = $this->repo->findById($id);
            $old = $hashtag->toArray();
            $updated = $this->repo->update($hashtag, $input);
            $this->log(request()->user()->id ?? $updated->user_id, 'update', Hashtag::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $hashtag = $this->repo->findById($id);
            $old = $hashtag->toArray();
            $deleted = $this->repo->delete($hashtag);
            $this->log(request()->user()->id ?? $hashtag->user_id, 'delete', Hashtag::class, $hashtag->id, $old, null);
            return $deleted;
        });
    }
}
