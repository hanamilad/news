<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RefreshToken extends Model
{
    protected $fillable = ['user_id', 'token','revoked','expires_at'];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
        public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
