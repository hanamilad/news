<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\ClearsHomeCache;
use App\Traits\HasHumanCreatedAt;
use App\Traits\NotifiesAdminsForApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class News extends Model
{
    use AutoTranslatableAttributes, BelongsToTenant, ClearsHomeCache, HasHumanCreatedAt, HasTranslations, NotifiesAdminsForApproval, SoftDeletes;

    protected $fillable = [
        'title',
        'styled_description',
        'is_urgent',
        'is_active',
        'is_admin_approved',
        'is_main',
        'user_id',
        'category_id',
        'publish_date',
    ];

    public $translatable = ['title', 'styled_description'];

    protected $casts = [
        'is_urgent' => 'boolean',
        'is_active' => 'boolean',
        'is_admin_approved' => 'boolean',
        'is_main' => 'boolean',
        'publish_date' => 'datetime',
    ];



    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }

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

    public function scopePublishDate($query)
    {
        return $query->where('publish_date', '<=', now());
    }


    public function scopeForPublic($query, $categoryId = null, $filterUrgent = false, $filterMain = false)
    {
        return $query->where('is_active', true)
            ->where('is_admin_approved', true)
            ->publishDate()
            ->when($categoryId !== null, fn($q) => $q->where('category_id', $categoryId))
            ->when($filterUrgent, fn($q) => $q->where('is_urgent', true))
            ->when($filterMain, fn($q) => $q->where('is_main', true));
    }


    public function scopeFilterByCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        });
    }
}
