<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NewsImage extends Model
{
    protected $fillable = [
        'image_path',
        'is_main',
        'news_id',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function getImagePathAttribute($value)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('spaces');

        return $value ? $disk->url($value) : null;
    }
}
