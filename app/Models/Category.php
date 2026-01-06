<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\ClearsHomeCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use AutoTranslatableAttributes, BelongsToTenant, ClearsHomeCache, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'show_in_navbar',
        'show_in_homepage',
        'show_in_grid',
        'show_title',
        'grid_order',
        'template_id',
    ];

    protected $casts = [
        'show_in_navbar' => 'boolean',
        'show_in_homepage' => 'boolean',
        'show_in_grid' => 'boolean',
        'show_title' => 'boolean',
    ];

    public $translatable = ['name', 'description'];

    protected static function booted()
    {
        static::addGlobalScope('grid_order', function ($query) {
            $query->orderBy('grid_order');
        });
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function ad()
    {
        return $this->hasOne(Ad::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function scopeShowInNavbar($query)
    {
        return $query->where('show_in_navbar', true);
    }

    public function scopeShowInHomepage($query)
    {
        return $query->where('show_in_homepage', true);
    }

    public function subCategories()
    {
        return $this->belongsToMany(Category::class, 'category_category', 'category_id', 'sub_category_id');
    }

    public function parentCategories()
    {
        return $this->belongsToMany(Category::class, 'category_category', 'sub_category_id', 'category_id');
    }

    public function mergedNews($limit = 10)
    {
        $allNews = collect($this->news);
        foreach ($this->subCategories as $sub) {
            $allNews = $allNews->merge($sub->news);
        }

        return $allNews
            ->sortByDesc(fn ($news) => $news->getRawOriginal('created_at'))
            ->take($limit)
            ->values();
    }
}
