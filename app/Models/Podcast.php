<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Podcast extends Model
{
    use BelongsToTenant,HasTranslations,SoftDeletes,AutoTranslatableAttributes;
    protected $fillable = [
        'title',
        'host_name',
        'description',
        'audio_path',
        'is_active',
        'user_id',
    ];
    public $translatable = ['title','host_name','description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}