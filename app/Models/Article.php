<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\ClearsHomeCache;
use App\Traits\HasHumanCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class Article extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes ,HasHumanCreatedAt,ClearsHomeCache;
    protected $fillable = [
        'title',
        'content',
        'author_name',
        'author_image',
        'is_active',
        'is_admin_approved',
        'publish_date',
        'user_id',
    ];
    public $translatable = ['title', 'content', 'author_name'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_admin_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getAuthorImageAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');
        return $value ? $disk->url($value) : null;
    }

    public function scopeForPublic($query)
    {
        return $query->where('is_active', true)
            ->where('is_admin_approved', true)
            ->where('publish_date', '<=', now())
            ->orderBy('created_at', 'desc');
    }
}
