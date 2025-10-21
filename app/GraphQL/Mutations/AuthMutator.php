<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthMutator
{
    public function register($_, array $args)
    {
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);

        return $user;
    }

    public function login($_, array $args)
    {
        if (!Auth::attempt(['email' => $args['email'], 'password' => $args['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();
        return $user->createToken('graphql_token')->plainTextToken;
    }
}
