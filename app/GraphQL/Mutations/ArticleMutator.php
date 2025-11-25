<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\Article\ArticleRequest;
use App\Services\Article\ArticleService;
use Illuminate\Validation\ValidationException;

class ArticleMutator
{
    public function __construct(protected ArticleService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new ArticleRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input, $args['image'] ?? null);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];

        $validator = validator($input, (new ArticleRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input, $args['image'] ?? null);
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }

    public function searchByAuthor($_, array $args)
    {
        $name = (string) ($args['name'] ?? '');

        return $this->service->searchByAuthor($name);
    }
}
