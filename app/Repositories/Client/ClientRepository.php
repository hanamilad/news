<?php

namespace App\Repositories\Client;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ClientRepository
{
    public function createOrUpdate(string $phone, string $otpCode, int $validMinutes, string $ip, string $agent): Client
    {
        $row = Client::updateOrCreate(
            ['phone' => $phone],
            [
                'otp_hash' => Hash::make($otpCode),
                'otp_expires_at' => Carbon::now()->addMinutes($validMinutes),
                'ip_address' => $ip,
                'user_agent' => $agent,
                'verified_at' => null,
            ]
        );
        $row->increment('attempts');
        return $row;
    }

    public function findActiveByPhone(string $phone): ?Client
    {
        $row = Client::where('phone', $phone)->first();
        if (! $row) return null;
        if ($row->isVerified()) return null;
        if ($row->isExpired()) return null;
        return $row;
    }

    public function markVerified(Client $row): Client
    {
        $row->update(['verified_at' => Carbon::now()]);
        return $row;
    }
}