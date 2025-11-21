<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository
{
    public function __construct(protected AuthRepository $authRepo) {}

    public function findOrFail(int $id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new ModelNotFoundException("User with ID $id not found");
        }

        return $user;
    }
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function softDelete(int $id): ?User
    {
        $user = User::findOrFail($id);
        $this->authRepo->deleteUserTokens($user);
        $user->delete();
        return $user;
    }

    public function restore(int $id): ?User
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return $user;
    }

    public function forceDelete(int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);
        return (bool) $user->forceDelete();
    }
}
