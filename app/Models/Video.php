<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Video extends Model
{
    use BelongsToTenant, SoftDeletes, HasTranslations, AutoTranslatableAttributes;
    protected $fillable = [
        'description',
        'video_path',
        'type',
        'is_active',
        'publish_date',
        'user_id',
    ];
    public $translatable = ['description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeForPublic($query)
    {
        return $query->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->orderBy('created_at', 'desc');
    }
}
