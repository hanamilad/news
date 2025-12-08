<?php

namespace App\Repositories\Auth;

use App\Models\PasswordResetToken;
use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthRepository
{
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function createAccessToken(User $user): string
    {
        return $user->createToken('access_token')->plainTextToken;
    }

    public function createRefreshToken(User $user, int $daysValid = 30): RefreshToken
    {
        $token = Str::random(64);

        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => Carbon::now()->addDays($daysValid),
        ]);
    }

    public function findRefreshToken(string $token): ?RefreshToken
    {
        return RefreshToken::where('token', $token)->where('revoked', false)->where('expires_at', '>', now())->first();
    }

    public function revokeRefreshToken(RefreshToken $refreshToken): void
    {
        $refreshToken->update(['revoked' => true]);
    }

    public function deleteUserTokens(User $user): void
    {
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        RefreshToken::where('user_id', $user->id)->delete();
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function createPasswordResetToken(string $email, int $minutes = 15): PasswordResetToken
    {
        $code = mt_rand(1000, 9999);
        $token = (string) $code;

        return PasswordResetToken::makeToken($email, $token, $minutes);
    }

    public function findPasswordResetToken(string $email, string $token): ?PasswordResetToken
    {
        $row = PasswordResetToken::where('email', $email)->where('token', $token)->first();
        if (! $row) {
            return null;
        }
        if ($row->isExpired()) {
            return null;
        }

        return $row;
    }

    public function deletePasswordResetToken(string $email): void
    {
        PasswordResetToken::where('email', $email)->delete();
    }

    public function markEmailVerified(User $user): void
    {
        $user->update(['email_verified_at' => Carbon::now()]);
    }
}
