<?php

namespace App\Services\Auth;

use App\Mail\OTPMail;
use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use App\Services\RateLimiter\OtpRateLimiterService;
use App\Traits\LogActivity;
use GraphQL\Error\Error;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use LogActivity;

    protected int $otpValidityMinutes = 15;

    public function __construct(protected AuthRepository $repo, protected OtpRateLimiterService $limit) {}

    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            if ($this->repo->findUserByEmail($data['email'])) {
                throw new Error('البريد الإلكتروني مسجل بالفعل.');
            }

            $user = $this->repo->createUser($data);
            $tokenRow = $this->repo->createPasswordResetToken($user->email, $this->otpValidityMinutes);
            $this->log($user->id, 'طلب انشاء حساب', User::class, $user->id, null, $user->toArray());

            Mail::to($user->email)->queue(new OTPMail($tokenRow->token, 'تأكيد البريد الإلكتروني'));

            return array_merge($user->toArray(), [
                'message' => 'تم إنشاء الحساب بنجاح. تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
            ]);
        });
    }

    public function verifyEmail(string $email, string $token)
    {
        return DB::transaction(function () use ($email, $token) {
            $row = $this->repo->findPasswordResetToken($email, $token);
            if (! $row) {
                throw new Error('رمز التحقق غير صحيح أو منتهي الصلاحية.');
            }

            $user = $this->repo->findUserByEmail($email);
            if (! $user) {
                throw new Error('المستخدم غير موجود.');
            }
            $this->log($user->id, 'تأكيد البريد الإلكتروني', User::class, $user->id, null, ['email' => $email]);

            $this->repo->markEmailVerified($user);
            $this->repo->deletePasswordResetToken($email);

            return 'تم تأكيد البريد الإلكتروني بنجاح.';
        });
    }

    public function login(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->repo->findUserByEmail($data['email']);
            if (! $user || ! Hash::check($data['password'], $user->password)) {
                throw new Error('بيانات الدخول غير صحيحة.');
            }
            if (is_null($user->email_verified_at)) {
                throw new Error('يجب تأكيد البريد الإلكتروني أولاً.');
            }
            $this->log($user->id, 'تسجيل دخول', User::class, $user->id, $user->toArray(), null);

            return $this->generateTokensResponse($user);
        });
    }

    protected function generateTokensResponse(User $user): array
    {
        $accessToken = $this->repo->createAccessToken($user);
        $refreshToken = $this->repo->createRefreshToken($user);

        return [
            'message' => 'تم تسجيل الدخول بنجاح.',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken->token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => $user,
        ];
    }

    public function refreshToken(string $token)
    {
        return DB::transaction(function () use ($token) {
            $refresh = $this->repo->findRefreshToken($token);
            if (! $refresh) {
                throw new Error('رمز التحديث غير صالح أو منتهي الصلاحية.');
            }

            $user = $refresh->user;

            $this->repo->revokeRefreshToken($refresh);

            return [
                'message' => 'تم تجديد الجلسة بنجاح.',
                'access_token' => $this->repo->createAccessToken($user),
                'refresh_token' => $this->repo->createRefreshToken($user)->token,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ];
        });
    }

    public function forgetPassword(string $email)
    {
        return DB::transaction(function () use ($email) {
            $user = $this->repo->findUserByEmail($email);
            if (! $user) {
                return 'إذا كان البريد موجوداً، تم إرسال رمز إعادة التعيين إليه.';
            }
            $result = $this->limit->check($email);
            if ($result) {
                return ['message' => $result['message']];
            }

            $tokenRow = $this->repo->createPasswordResetToken($email, $this->otpValidityMinutes);

            Mail::to($email)->queue(new OTPMail($tokenRow->token, 'إعادة تعيين كلمة المرور'));
            $this->log($user->id, 'طلب إعادة تعيين كلمة المرور', User::class, $user->id, null, ['email' => $email]);

            return 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني.';
        });
    }

    public function resetPassword(string $token, string $email, string $password)
    {
        return DB::transaction(function () use ($token, $email, $password) {
            $row = $this->repo->findPasswordResetToken($email, $token);
            if (! $row) {
                throw new Error('رمز إعادة التعيين غير صحيح أو منتهي الصلاحية.');
            }

            $user = $this->repo->findUserByEmail($email);
            if (! $user) {
                throw new Error('المستخدم غير موجود.');
            }

            $this->repo->updatePassword($user, $password);
            $this->repo->deletePasswordResetToken($email);
            $this->log($user->id, 'تغيير كلمة المرور', User::class, $user->id, null, ['email' => $email]);

            event(new PasswordReset($user));

            return 'تم تغيير كلمة المرور بنجاح.';
        });
    }

    public function verifyOTP(string $token, string $email)
    {
        return DB::transaction(function () use ($token, $email) {
            $row = $this->repo->findPasswordResetToken($email, $token);
            if (! $row) {
                return [
                    'status' => false,
                    'message' => 'رمز إعادة التعيين غير صحيح أو منتهي الصلاحية.',
                ];
            }

            $user = $this->repo->findUserByEmail($email);
            if (! $user) {
                return [
                    'status' => false,
                    'message' => 'المستخدم غير موجود.',
                ];
            }

            return [
                'status' => true,
                'message' => 'تم التحقق من الرمز بنجاح.',
            ];
        });
    }

    public function logout()
    {
        return DB::transaction(function () {
            $user = Auth::user();

            if (! $user) {
                throw new Error('لم يتم تسجيل الدخول.');
            }

            $token = method_exists($user, 'currentAccessToken') ? $user->currentAccessToken() : null;
            if ($token) {
                $token->delete();
            }
            $this->log($user->id, 'تسجيل خروج', User::class, $user->id, null, null);

            return 'تم تسجيل الخروج بنجاح.';
        });
    }

    public function changePassword(string $currentPassword, string $newPassword)
    {
        return DB::transaction(function () use ($currentPassword, $newPassword) {
            $user = Auth::user();
            if (! $user) {
                throw new Error('لم يتم تسجيل الدخول.');
            }

            if (! Hash::check($currentPassword, $user->password)) {
                throw new Error('كلمة المرور الحالية غير صحيحة.');
            }
            $this->log($user->id, 'تغيير كلمة المرور', User::class, $user->id, null, ['email' => $user->email]);
            $this->repo->updatePassword($user, $newPassword);

            return [
                'message' => 'تم تغيير كلمة المرور بنجاح.',
                'user' => $user,
            ];
        });
    }
}
