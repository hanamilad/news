<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class Reel extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes;
    protected $fillable = [
        'description',
        'path',
        'type',
        'is_active',
        'user_id',
    ];
    public $translatable = ['description'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPathAttribute($value)
    {
        if ($this->type === 'video') {
            return $value;
        }
        if ($this->type === 'image') {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk('public');
            return $storage->url($value);
        }
        return $value;
    }
}
