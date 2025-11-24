<?php

namespace App\GraphQL\Queries;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Builder;

class AdQuery
{
    public function adsForPublicBuilder($_, array $args): Builder
    {
        return Ad::forPublic($args['category_id'] ?? null);
    }
}