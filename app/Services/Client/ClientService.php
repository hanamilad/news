<?php

namespace App\Services\Client;

use App\Repositories\Client\ClientRepository;
use App\Services\RateLimiter\OtpRateLimiterService;
use App\Models\Client;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use GraphQL\Error\Error;

class ClientService
{
    use LogActivity;

    public function __construct(protected ClientRepository $repo, protected OtpRateLimiterService $limit) {}

    protected int $otpValidityMinutes = 10;

    public function requestOtp(string $phone): array
    {
        return DB::transaction(function () use ($phone) {
            $row = Client::where('phone', $phone)->first();
            if ($row->isVerified()) return ['message' => 'هذا الهاتف مسجل بالفعل .',];

            $result = $this->limit->check($phone);
            if ($result) {return ['message' => $result['message']];}

            $code = (string) random_int(100000, 999999);
            $row = $this->repo->createOrUpdate($phone, $code, $this->otpValidityMinutes, request()->ip(), (string) request()->userAgent(),);
            $this->log(null, 'طلب رمز تحقق لهاتف ', Client::class, $row->id, null, ['phone' => $phone]);
            return ['message' => 'تم إرسال رمز التحقق إلى رقم هاتفك.',];
        });
    }

    public function verifyOtp(string $phone, string $otp): string
    {
        return DB::transaction(function () use ($phone, $otp) {
            $row = $this->repo->findActiveByPhone($phone);
            if (! $row) {
                throw new Error('الرمز غير صالح أو منتهي الصلاحية.');
            }

            if (! Hash::check($otp, $row->otp_hash)) {
                throw new Error('رمز التحقق غير صحيح.');
            }

            $this->repo->markVerified($row);
            $this->log(null, 'تأكيد رقم هاتف', Client::class, $row->id, null, ['phone' => $phone]);
            return 'تم تأكيد رقم الهاتف وتسجيل بياناتك.';
        });
    }
}
