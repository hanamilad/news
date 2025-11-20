<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class News extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes;
    protected $fillable = [
        'title',
        'styled_description',
        'is_urgent',
        'is_active',
        'is_main',
        'user_id',
        'category_id',
        'publish_date'
    ];
    public $translatable = ['title', 'styled_description'];

    protected $casts = [
        'is_urgent' => 'boolean',
        'is_active'   => 'boolean',
        'is_main' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_news');
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }

    public function links()
    {
        return $this->hasMany(NewsLink::class);
    }

    public function originalSuggestions()
    {
        return $this->belongsToMany(
            News::class,
            'suggested_news',
            'suggested_news_id',
            'original_news_id'
        );
    }

    public function scopePublishDate($query)
    {
        return $query->where('publish_date', '<=', now());
    }

    public function scopeForPublic($query, $categoryId = null,$filterUrgent = false,$filterMain = false)
    {
        $query->where('is_active', true)
            ->where('publish_date', '<=', now());

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($filterUrgent) {
            $query->where('is_urgent', true);
        }

        if ($filterMain) {
            $query->where('is_main', true);
        }
        return $query->orderBy('created_at', 'desc');
    }
}
