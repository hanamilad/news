<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\HasHumanCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes, HasHumanCreatedAt;

    protected $fillable = [
        'title',
        'image',
        'is_active',
        'start_date',
        'expiry_date',
        'user_id',
        'category_id',
    ];

    public $translatable = ['title'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');
        return $value ? $disk->url($value) : null;
    }

    public function scopeForPublic($query, $categoryId = null)
    {
        $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('expiry_date', '>=', now());

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderByDesc('created_at');
    }
}
