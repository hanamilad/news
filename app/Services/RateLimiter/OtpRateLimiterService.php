<?php

namespace App\Services\RateLimiter;

use Illuminate\Support\Facades\RateLimiter;

class OtpRateLimiterService
{
    protected $cooldownSeconds = 120; // 2 دقيقة

    protected $maxAttempts = 4;

    protected $blockSeconds = 86400; // 24 ساعة

    public function check(string $credintial): ?array
    {
        $credintial = preg_replace('/\D/', '', $credintial);
        $cooldownKey = 'otp_cooldown:'.$credintial;
        $blockKey = 'otp_block:'.$credintial;

        // تحقق من الحظر بسبب كثرة المحاولات
        if (RateLimiter::tooManyAttempts($blockKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($blockKey);

            return [
                'status' => false,
                'message' => 'لقد تجاوزت عدد المحاولات المسموح بها. الرجاء المحاولة بعد '.gmdate('H:i:s', $seconds),
                'code' => 429,
            ];
        }

        // تحقق من التكرار في وقت قصير
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);

            return [
                'status' => false,
                'message' => 'لقد قمت بطلب رمز مؤخرًا. الرجاء المحاولة بعد '.gmdate('i:s', $seconds),
                'code' => 429,
            ];
        }

        // سجل المحاولة في كلا المفتاحين
        RateLimiter::hit($cooldownKey, $this->cooldownSeconds);
        RateLimiter::hit($blockKey, $this->blockSeconds);

        return null;
    }
}
