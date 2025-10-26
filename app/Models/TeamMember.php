<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

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
}