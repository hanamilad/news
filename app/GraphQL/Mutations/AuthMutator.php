<?php

namespace App\GraphQL\Mutations;

use App\Services\Auth\AuthService;
use Illuminate\Validation\ValidationException;

class AuthMutator
{
    public function __construct(protected AuthService $service) {}

    public function register($_, array $args)
    {
        return $this->service->register($args);
    }

    public function verifyEmail($_, array $args)
    {
        return $this->service->verifyEmail($args['email'], $args['token']);
    }

    public function login($_, array $args)
    {
        return $this->service->login($args);
    }

    public function refresh($_, array $args)
    {
        return $this->service->refreshToken($args['refresh_token']);
    }

    public function forgetPass($_, array $args)
    {
        return $this->service->forgetPassword($args['email']);
    }

    public function resetPass($_, array $args)
    {
        return $this->service->resetPassword($args['token'], $args['email'], $args['password']);
    }

    public function verifyOTP($_, array $args)
    {
        return $this->service->verifyOTP($args['token'], $args['email']);
    }

    public function logout($_, array $args)
    {
        return $this->service->logout();
    }

    public function changePassword($_, array $args)
    {
        $input = [
            'current_password' => $args['current_password'] ?? null,
            'new_password' => $args['new_password'] ?? null,
        ];

        $validator = validator($input, [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->changePassword($input['current_password'], $input['new_password']);
    }
}
