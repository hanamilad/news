<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserService
{
    use LogActivity;

    public function __construct(protected UserRepository $repo) {}

    public function create(array $input, $logo = null)
    {
        $user = auth('api')->user();
        $input['password'] = Hash::make($input['password']);
        $input['email_verified_at'] = now();
        $input['logo'] = $this->storeLogo($logo);
        $new_user = $this->repo->create($input);
        $this->log($user->id, 'اضافة', User::class, $new_user->id, null, $new_user->toArray());

        return $new_user;
    }

    public function update(int $id, array $input, $logo = null)
    {
        $user_auth = auth('api')->user();
        $user = $this->repo->findOrFail($id);
        $this->maybeHashPassword($input);
        $this->applyLogoChange($user, $input, $logo);
        $updated_user = $this->repo->update($id, $input);
        $this->log($user_auth->id, 'تعديل', User::class, $updated_user->id, $user->toArray(), $updated_user->toArray());

        return $updated_user;
    }

    public function deactivate(int $id)
    {
        $user_auth = auth('api')->user();
        $user = $this->repo->softDelete($id);
        $this->log($user_auth->id, 'تعطيل  ', User::class, $id, null, $user->toArray());

        return $user;
    }

    public function restore(int $id)
    {
        $user_auth = auth('api')->user();
        $user = $this->repo->restore($id);
        $this->log($user_auth->id, 'استعادة', User::class, $id, null, $user->toArray());

        return $user;
    }

    public function delete(int $id)
    {
        $user_auth = auth('api')->user();
        $user = $this->repo->findOrFail($id);
        $oldRaw = $user->getRawOriginal('logo');
        if ($oldRaw) {
            $this->deleteLogoByRaw($oldRaw);
        }
        $user_deleted = $this->repo->forceDelete($id);
        $this->log($user_auth->id, 'حذف', User::class, $id, $user->toArray(), null);

        return $user_deleted;
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
        $path = '';
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('user_logos', ['disk' => 'spaces']);
        }

        return $path;
    }

    private function maybeHashPassword(array &$input): void
    {
        if (! empty($input['password'])) {
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
        if (! empty($uploaded)) {
            if ($oldRaw) {
                $this->deleteLogoByRaw($oldRaw);
            }
            $input['logo'] = $uploaded;

            return;
        }

        // Case 2: no upload; check input['logo'] semantics
        if (! array_key_exists('logo', $input)) {
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
        if (! $raw) {
            return;
        }
        $originalPath = ltrim($raw, '/');
        if (Storage::disk('spaces')->exists($originalPath)) {
            Storage::disk('spaces')->delete($originalPath);
        }
    }
}
