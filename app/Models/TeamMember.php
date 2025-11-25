<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TeamMember extends Model
{
    use AutoTranslatableAttributes, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'image',
        'is_active',
    ];

    public $translatable = ['name', 'position', 'bio'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function getImageAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');

        return $value ? $disk->url($value) : null;
    }
}
