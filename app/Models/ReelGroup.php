<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ReelGroup extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes;

    protected $fillable = [
        'user_id',
        'title',
        'is_active',
        'sort_order',
    ];

    public $translatable = ['title'];
    protected $appends = ['cover_image'];

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImageAttribute()
    {
        $lastReel = $this->reels()
            ->whereNotNull('path')
            ->orderBy('sort_order', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        return $lastReel ? $lastReel->path : null;
    }
}
