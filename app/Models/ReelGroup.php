<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ReelGroup extends Model
{
    use AutoTranslatableAttributes, BelongsToTenant, HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'is_active',
        'is_admin_approved',
        'sort_order',
    ];

    public $translatable = ['title'];

    protected $appends = ['cover_image'];

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    /**
     * Get the cover reel (first active reel with a path, ordered by sort_order).
     */
    public function coverReel()
    {
        return $this->hasOne(Reel::class, 'reel_group_id')
            ->where('is_active', true)
            ->ofMany('sort_order', 'min');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cover image URL for this reel group.
     * Returns the path from the first active reel.
     */
    public function getCoverImageAttribute(): ?string
    {
        $reel = $this->relationLoaded('coverReel') 
            ? $this->getRelation('coverReel') 
            : $this->coverReel;

        return $reel?->path;
    }
}
