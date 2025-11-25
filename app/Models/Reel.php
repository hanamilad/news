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

    public function getPathAttribute($value)
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_contains($value, 'reel_images') || str_contains($value, 'reel_videos')) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk('spaces');

            return $storage->url($value);
        }

        return $value;
    }
}
