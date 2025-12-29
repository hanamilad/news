<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\HasHumanCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Reel extends Model
{
    use AutoTranslatableAttributes, BelongsToTenant, HasHumanCreatedAt, HasTranslations,SoftDeletes;

    protected $fillable = [
        'reel_group_id',
        'description',
        'path',
        'type',
        'is_active',
        'user_id',
        'news_id',
        'sort_order',
    ];

    public $translatable = ['description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function group()
    {
        return $this->belongsTo(ReelGroup::class, 'reel_group_id');
    }

    /**
     * Get the path attribute.
     * Handles both direct paths and news-based paths.
     */
    public function getPathAttribute($value)
    {
        // Case 1: Reel linked to news (fetch path from news main image)
        if ($this->isNewsBasedReel($value)) {
            return $this->getNewsMainImagePath();
        }

        // Case 2: No path available
        if (!$value) {
            return null;
        }

        // Case 3: Format and return the path
        return $this->formatPath($value);
    }

    /**
     * Check if this is a news-based reel (has news_id but no direct path).
     */
    protected function isNewsBasedReel($pathValue): bool
    {
        return !$pathValue && $this->news_id;
    }

    /**
     * Get the main image path from the related news.
     */
    protected function getNewsMainImagePath(): ?string
    {
        $news = $this->relationLoaded('news') 
            ? $this->getRelation('news') 
            : $this->news;

        if (!$news) {
            return null;
        }

        $mainImage = $news->relationLoaded('images')
            ? $news->images->where('is_main', true)->first()
            : $news->images()->where('is_main', true)->first();

        return $mainImage?->image_path;
    }

    /**
     * Format the path (add full URL if needed).
     */
    protected function formatPath(string $path): string
    {
        // Already a full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Reel images/videos stored in cloud storage
        if (str_contains($path, 'reel_images') || str_contains($path, 'reel_videos')) {
            return Storage::disk('spaces')->url($path);
        }

        // Return as-is
        return $path;
    }
}
