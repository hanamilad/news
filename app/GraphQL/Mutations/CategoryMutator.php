<?php

namespace App\GraphQL\Mutations;

use App\Services\Category\CategoryService;
use App\Http\Requests\Category\CategoryRequest;
use Illuminate\Validation\ValidationException;

class CategoryMutator
{
    public function __construct(protected CategoryService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new CategoryRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int)$args['id'];
        $input = $args['input'] ?? [];
        $validator = validator($input, (new CategoryRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input);
    }

    public function delete($_, array $args)
    {
        $id = (int)$args['id'];
        return $this->service->delete($id);
    }
}
