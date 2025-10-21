<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestedNews extends Model
{
    protected $fillable = [
        'original_news_id',
        'suggested_news_id',
    ];

    public function originalNews()
    {
        return $this->belongsTo(News::class, 'original_news_id');
    }

    public function suggestedNews()
    {
        return $this->belongsTo(News::class, 'suggested_news_id');
    }
}
