<?php

namespace App\GraphQL\Mutations;

use Spatie\Permission\Models\Permission;
use InvalidArgumentException;

class PermissionMutator
{
    public function create($_, array $args)
    {
        $name = trim($args['name']);

        if ($name === '') {
            throw new InvalidArgumentException("Permission name cannot be empty");
        }

        $existing = Permission::where('name', $name)->first();

        if ($existing) {
            throw new InvalidArgumentException("Permission '{$name}' already exists");
        }

        return Permission::create(['name' => $name]);
    }
}
