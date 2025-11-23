<?php

namespace App\GraphQL\Mutations;

use App\Services\User\UserService;

class UserMutator
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function create($_, array $args)
    {
        return $this->service->create($args['input'], $args['logo'] ?? null);
    }

    public function update($_, array $args)
    {
        return $this->service->update((int)$args['id'], $args['input'], $args['logo'] ?? null);
    }

    public function deactivate($_, array $args)
    {
        return $this->service->deactivate($args['id']);
    }

    public function restore($_, array $args)
    {
        return $this->service->restore($args['id']);
    }

    public function delete($_, array $args)
    {
        return $this->service->delete($args['id']);
    }
    public function manageAccess($_, array $args)
    {
        return $this->service->assignAccess($args['user_id'], $args['type'], $args['access_id']);
    }

    public function removeAccess($_, array $args)
    {
        return $this->service->removeAccess($args['user_id'], $args['type'], $args['access_id']);
    }
}
