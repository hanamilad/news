<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable, HasRoles, BelongsToTenant;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'phone_number',
        'logo',
        'job_title',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function activity_logs() {
        return $this->hasMany(ActivityLog::class);
    }


    public function scopeApplyTrashedFilter($query, $args)
    {
        if (!empty($args['onlyTrashed']) && $args['onlyTrashed']) {
            return $query->onlyTrashed();
        }

        if (!empty($args['withTrashed']) && $args['withTrashed']) {
            return $query->withTrashed();
        }

        return $query;
    }

    public function getLogoAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');
        return $value ? $disk->url($value) : null;
    }
}
