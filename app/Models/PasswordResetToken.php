<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class PasswordResetToken extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'password_reset_tokens';

    /**
     * Primary key configuration.
     */
    protected $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];


    protected $casts = [
        'created_at' => 'datetime',
    ];


    public static function makeToken(string $email, string $token, int $minutes = 15): self
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );
    }


    public function isExpired(int $minutes = 15): bool
    {
        return ! $this->created_at instanceof Carbon || $this->created_at->addMinutes($minutes)->isPast();
    }


    public function scopeByEmailAndToken($query, string $email, string $token)
    {
        return $query->where('email', $email)->where('token', $token);
    }
}
