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
        'template_id'
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
}
