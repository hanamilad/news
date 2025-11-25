<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HashtagNews extends Model
{
    protected $fillable = [
        'hashtag_id',
        'news_id',
    ];

    public function hashtag()
    {
        return $this->belongsTo(Hashtag::class);
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
