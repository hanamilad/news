<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsLink extends Model
{
        protected $fillable = [
        'video_link',
        'news_id',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

}