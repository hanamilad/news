<?php

namespace App\GraphQL\Mutations;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class RoleMutator
{
    public function create($_, array $args)
    {
        // منع التكرار
        $existing = Role::where('name', $args['name'])->first();

        if ($existing) {
            throw new InvalidArgumentException("Role '{$args['name']}' already exists");
        }

        return Role::create(['name' => $args['name']]);
    }

    public function managePermissions($_, array $args)
    {
        $role = Role::find($args['role_id']);

        if (!$role) {
            throw new ModelNotFoundException("Role with ID '{$args['role_id']}' not found");
        }

        $permissions = Permission::whereIn('id', $args['permission_ids'])->get();

        if ($permissions->count() !== count($args['permission_ids'])) {
            throw new InvalidArgumentException("One or more permission IDs not found");
        }

        match (strtolower($args['action'])) {
            'add' => $role->givePermissionTo($permissions),
            'remove' => $role->revokePermissionTo($permissions),
            default => throw new InvalidArgumentException("Invalid action: {$args['action']}"),
        };

        return $role->load('permissions');
    }
}
