<?php

namespace App\Repositories\TeamMember;

use App\Models\TeamMember;

class TeamMemberRepository
{
    public function findById(int $id): TeamMember
    {
        return TeamMember::findOrFail($id);
    }

    public function create(array $data): TeamMember
    {
        $team_member = TeamMember::create([
            'name' => $data['name'],
            'position' => $data['position'],
            'bio' => $data['bio'],
            'image' => $data['image'],
            'is_active' => $data['is_active'] ?? true,
            'user_id' => $data['user_id'],
        ]);
        return $team_member;
    }

    public function update(TeamMember $team_member, array $data): TeamMember
    {
        $team_member->update([
            'name' => $data['name'] ?? $team_member->getTranslations('name'),
            'position' => $data['position'] ?? $team_member->getTranslations('position'),
            'bio' => $data['bio'] ?? $team_member->getTranslations('bio'),
            'image' => $data['image'] ?? $team_member->image,
            'is_active' => $data['is_active'] ?? $team_member->is_active,
            'user_id' => $data['user_id'] ?? $team_member->user_id,
        ]);

        return $team_member;
    }

    public function delete(TeamMember $team_member): bool
    {
        return (bool) $team_member->delete();
    }
}
