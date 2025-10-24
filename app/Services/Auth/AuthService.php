<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use App\Mail\OTPMail;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    protected AuthRepository $repo;
    protected int $otpValidityMinutes = 15;

    public function __construct(AuthRepository $repo)
    {
        $this->repo = $repo;
    }

    public function register(array $data)
    {
        if ($this->repo->findUserByEmail($data['email'])) {
            throw new Error('Email already registered.');
        }
        $user = $this->repo->createUser($data);
        $tokenRow = $this->repo->createPasswordResetToken($user->email, $this->otpValidityMinutes);
        Mail::to($user->email)->send(new OTPMail($tokenRow->token, 'Email Verification'));
        return array_merge($user->toArray(), [
            'message' => 'User created. Verification OTP sent to email.'
        ]);
    }

    public function verifyEmail(string $email, string $token)
    {
        $row = $this->repo->findPasswordResetToken($email, $token);
        if (! $row) {
            throw new Error('Invalid or expired verification code.');
        }
        $user = $this->repo->findUserByEmail($email);
        if (! $user) {
            throw new Error('User not found.');
        }
        $this->repo->markEmailVerified($user);
        $this->repo->deletePasswordResetToken($email);
        return 'Email verified successfully.';
    }

    // LOGIN: validate & issue tokens
    public function login(array $data)
    {
        $user = $this->repo->findUserByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new Error('Invalid credentials.');
        }

        if (is_null($user->email_verified_at)) {
            throw new Error('Email not verified.');
        }

        // Clear old tokens
        $this->repo->deleteUserTokens($user);

        return $this->generateTokensResponse($user);
    }

    protected function generateTokensResponse(User $user): array
    {
        $accessToken  = $this->repo->createAccessToken($user);
        $refreshToken = $this->repo->createRefreshToken($user);

        return array_merge($user->toArray(), [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->token,
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ]);
    }

    // REFRESH: use DB-stored refresh token
    public function refreshToken(string $token)
    {
        $refresh = $this->repo->findRefreshToken($token);

        if (! $refresh) {
            throw new Error('Invalid or expired refresh token.');
        }

        $user = $refresh->user;

        // rotate tokens: delete old and issue new
        $this->repo->deleteUserTokens($user);

        return [
            'access_token'  => $this->repo->createAccessToken($user),
            'refresh_token' => $this->repo->createRefreshToken($user)->token,
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ];
    }

    // FORGET PASSWORD: create OTP (in password_reset_tokens) and email it
    public function forgetPassword(string $email)
    {
        $user = $this->repo->findUserByEmail($email);
        if (! $user) {
            // For security, still respond with success message to avoid leaking user existence
            return 'If the email exists, a reset code has been sent.';
        }

        $tokenRow = $this->repo->createPasswordResetToken($email, $this->otpValidityMinutes);

        Mail::to($email)->send(new OTPMail($tokenRow->token, 'Password Reset'));

        return 'If the email exists, a reset code has been sent.';
    }

    // RESET PASSWORD: validate OTP then change password
    public function resetPassword(string $token, string $email, string $password)
    {
        $row = $this->repo->findPasswordResetToken($email, $token);
        if (! $row) {
            throw new Error('Invalid or expired reset code.');
        }

        $user = $this->repo->findUserByEmail($email);
        if (! $user) {
            throw new Error('User not found.');
        }

        $this->repo->updatePassword($user, $password);
        $this->repo->deletePasswordResetToken($email);

        event(new PasswordReset($user));

        return 'Password has been reset successfully.';
    }

    // LOGOUT
    public function logout()
    {
        $user = Auth::user();

        if (! $user) {
            throw new Error('Not authenticated.');
        }

        $this->repo->deleteUserTokens($user);

        return true;
    }
}
