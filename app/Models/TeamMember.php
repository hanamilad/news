<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class TeamMember extends Model
{
    use BelongsToTenant, HasTranslations, AutoTranslatableAttributes;
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
