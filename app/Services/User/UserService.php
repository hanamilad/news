<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserService
{
    public function __construct(protected UserRepository $repo) {}

    public function create(array $input, $logo = null)
    {
        $input['password'] = Hash::make($input['password']);
        $input['email_verified_at'] = now();
        $input['logo'] = $this->storeLogo($logo);
        return $this->repo->create($input);
    }

    public function update(int $id, array $input, $logo = null)
    {
        $user = $this->repo->findOrFail($id);
        $this->maybeHashPassword($input);
        $this->applyLogoChange($user, $input, $logo);
        return $this->repo->update($id, $input);
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
        $user = $this->repo->findOrFail($id);
        $oldRaw = $user->getRawOriginal('logo');
        if ($oldRaw) {
            $this->deleteLogoByRaw($oldRaw);
        }
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

    protected function storeLogo($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('user_logos', ['disk' => 'spaces']);
        }
        return $path;
    }

    private function maybeHashPassword(array &$input): void
    {
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
    }

    private function applyLogoChange($user, array &$input, $logo): void
    {
        $oldRaw = $user->getRawOriginal('logo');

        // Case 1: new upload provided
        $uploaded = $this->storeLogo($logo);
        if (!empty($uploaded)) {
            if ($oldRaw) {
                $this->deleteLogoByRaw($oldRaw);
            }
            $input['logo'] = $uploaded;
            return;
        }

        // Case 2: no upload; check input['logo'] semantics
        if (!array_key_exists('logo', $input)) {
            return; // nothing to do
        }

        $new = $input['logo'];
        // unchanged string (raw path or resolved URL)
        if (is_string($new) && $oldRaw) {
            $oldUrl = Storage::disk('spaces')->url($oldRaw);
            if ($new === $oldUrl || $new === $oldRaw) {
                unset($input['logo']);
                return;
            }
            // different string; if old is a stored file, clean it up
            if (str_contains($oldRaw, 'user_logos')) {
                $this->deleteLogoByRaw($oldRaw);
            }
            return; // keep the provided string as-is
        }

        // explicit removal
        if ($new === null || $new === '') {
            if ($oldRaw) {
                $this->deleteLogoByRaw($oldRaw);
            }
            $input['logo'] = null;
        }
    }

    private function deleteLogoByRaw(?string $raw): void
    {
        if (!$raw) return;
        $originalPath = ltrim($raw, '/');
        if (Storage::disk('spaces')->exists($originalPath)) {
            Storage::disk('spaces')->delete($originalPath);
        }
    }
}
