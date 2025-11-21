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

    public function coverReel()
    {
        // pick latest reel by sort_order that has a path
        return $this->hasOne(Reel::class)
            ->whereNotNull('path')
            ->latestOfMany('sort_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImageAttribute()
    {
        $reel = $this->relationLoaded('coverReel') ? $this->getRelation('coverReel') : $this->coverReel()->first();
        return $reel ? $reel->path : null;
    }
}
