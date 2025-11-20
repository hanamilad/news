<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasHumanCreatedAt
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->locale('ar')
            ->diffForHumans();
    }
}
