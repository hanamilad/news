<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Hashtag extends Model
{
    use SoftDeletes,BelongsToTenant,HasTranslations,AutoTranslatableAttributes;
    protected $fillable = [
        'name',
    ];
    public $translatable = ['name'];
}