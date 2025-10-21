<?php

namespace App\GraphQL\Queries;

use App\Services\News\NewsService;

class NewsQuery
{
    public function __construct(protected NewsService $service) {}

}
