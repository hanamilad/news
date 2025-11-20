<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class Podcast extends Model
{
    use BelongsToTenant, HasTranslations, SoftDeletes, AutoTranslatableAttributes;
    protected $fillable = [
        'title',
        'host_name',
        'description',
        'audio_path',
        'is_active',
        'publish_date',
        'user_id',
    ];
    public $translatable = ['title', 'host_name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAudioPathAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');
        return $value ? $disk->url($value) : null;
    }

    public function scopeForPublic($query)
    {
        return $query->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->orderBy('created_at', 'desc');
    }
}
