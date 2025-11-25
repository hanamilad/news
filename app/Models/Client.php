<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Client extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'phone',
        'otp_hash',
        'otp_expires_at',
        'verified_at',
        'ip_address',
        'user_agent',
        'attempts',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function isExpired(): bool
    {
        if (! $this->otp_expires_at instanceof Carbon) {
            return true;
        }

        return $this->otp_expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return (bool) $this->verified_at;
    }
}
