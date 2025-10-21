<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Article extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations ,AutoTranslatableAttributes;
    protected $fillable = [
        'content',
        'author_name',
        'author_image',
        'is_active',
        'user_id',
    ];
    public $translatable = ['content', 'author_name'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
