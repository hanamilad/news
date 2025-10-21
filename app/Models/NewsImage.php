<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
    protected $fillable = [
        'image_path',
        'is_main',
        'news_id',
    ];

    protected $casts = [
        'is_main' => 'boolean'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function getImageAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}