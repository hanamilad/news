<?php

namespace App\GraphQL\Mutations;

use App\Services\Auth\AuthService;

class AuthMutator
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register($_, array $args)
    {
        return $this->authService->register($args);
    }

    public function verifyEmail($_, array $args)
    {
        return $this->authService->verifyEmail($args['email'], $args['token']);
    }

    public function login($_, array $args)
    {
        return $this->authService->login($args);
    }

    public function refresh($_, array $args)
    {
        return $this->authService->refreshToken($args['refresh_token']);
    }

    public function forgetPass($_, array $args)
    {
        return $this->authService->forgetPassword($args['email']);
    }

    public function resetPass($_, array $args)
    {
        return $this->authService->resetPassword($args['token'], $args['email'], $args['password']);
    }

    public function logout($_, array $args)
    {
        return $this->authService->logout();
    }
}
