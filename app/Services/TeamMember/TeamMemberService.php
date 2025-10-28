<?php

namespace App\Services\TeamMember;

use App\Repositories\TeamMember\TeamMemberRepository;
use App\Models\TeamMember;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class TeamMemberService
{
    use LogActivity;
    public function __construct(protected TeamMemberRepository $repo) {}
    public function create(array $input,  $file): TeamMember
    {
        return DB::transaction(function () use ($input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['image'] = $this->storeTeamMemberImages($file);
            $team_member = $this->repo->create($input);
            $this->log($user->id, 'create', TeamMember::class, $team_member->id, null, $team_member->toArray());
            return $team_member;
        });
    }

    public function update(int $id, array $input, $file): TeamMember
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $team_member = $this->repo->findById($id);
            $old = $team_member->toArray();
            $uploaded = $this->storeTeamMemberImages($file);
            if (!empty($uploaded)) {
                if ($team_member->image && Storage::disk('public')->exists($team_member->image)) {
                    Storage::disk('public')->delete($team_member->image);
                }
                $input['image'] = $uploaded;
            }
            $updated = $this->repo->update($team_member, $input);
            $this->log($user->id, 'update', TeamMember::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $team_member = $this->repo->findById($id);
            $old = $team_member->toArray();
            if ($team_member->image && Storage::disk('public')->exists($team_member->image)) {
                Storage::disk('public')->delete($team_member->image);
            }
            $deleted = $this->repo->delete($team_member);
            $this->log($user->id, 'delete', TeamMember::class, $team_member->id, $old, null);
            return $deleted;
        });
    }

    protected function storeTeamMemberImages($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('team_member_images', ['disk' => 'public']);
        }
        return $path;
    }
}
