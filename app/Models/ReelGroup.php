<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ReelGroup extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations ,AutoTranslatableAttributes;

    protected $fillable = [
        'user_id',
        'title',
        'is_active',
        'sort_order',
    ];

    public $translatable = ['title'];

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
