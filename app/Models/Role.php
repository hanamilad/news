<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'name',
    ];
}