<?php

namespace App\Models;

use App\Traits\HasHumanCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ActivityLog extends Model
{
    use BelongsToTenant,SoftDeletes,HasHumanCreatedAt;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }
}
