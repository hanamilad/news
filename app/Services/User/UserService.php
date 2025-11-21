<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserService
{
    public function __construct(protected UserRepository $repo){}

    public function create(array $input)
    {
        $input['password'] = Hash::make($input['password']);
        $input['email_verified_at'] = now();
        return $this->repo->create($input);
    }

    public function deactivate(int $id)
    {
        return $this->repo->softDelete($id);
    }

    public function restore(int $id)
    {
        return $this->repo->restore($id);
    }

    public function delete(int $id)
    {
        return $this->repo->forceDelete($id);
    }
    public function assignAccess(int $userId, string $type, int $accessId)
    {
        $user = $this->repo->findOrFail($userId);

        match (strtolower($type)) {
            'role' => $this->assignRole($user, $accessId),
            'permission' => $this->assignPermission($user, $accessId),
            default => throw new ModelNotFoundException("Invalid access type: $type"),
        };

        return $user->load('roles', 'permissions');
    }

    public function removeAccess(int $userId, string $type, int $accessId)
    {
        $user = $this->repo->findOrFail($userId);

        match (strtolower($type)) {
            'role' => $this->removeRole($user, $accessId),
            'permission' => $this->removePermission($user, $accessId),
            default => throw new ModelNotFoundException("Invalid access type: $type"),
        };

        return $user->load('roles', 'permissions');
    }


    protected function assignRole($user, int $id)
    {
        $role = Role::findOrFail($id);
        $user->assignRole($role);
    }

    protected function assignPermission($user, int $id)
    {
        $perm = Permission::findOrFail($id);
        $user->givePermissionTo($perm);
    }

    protected function removeRole($user, int $id)
    {
        $role = Role::findOrFail($id);
        $user->removeRole($role);
    }

    protected function removePermission($user, int $id)
    {
        $perm = Permission::findOrFail($id);
        $user->revokePermissionTo($perm);
    }
}
