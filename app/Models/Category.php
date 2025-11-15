<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes;
    protected $fillable = [
        'name',
        'show_in_navbar',
        'show_in_homepage',
        'template_id'
    ];
    protected $casts = [
        'show_in_navbar' => 'boolean',
        'show_in_homepage' => 'boolean',
    ];
    public $translatable = ['name'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
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
}
