<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Task extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'note',
        'start_date',
        'delivery_date',
        'is_priority',
        'status',
    ];

    protected $casts = [
        'is_priority' => 'boolean',
        'start_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withTimestamps();
    }
}
