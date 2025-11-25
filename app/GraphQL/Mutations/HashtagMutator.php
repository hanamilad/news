<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\Hashtag\HashtagRequest;
use App\Services\Hashtag\HashtagService;
use Illuminate\Validation\ValidationException;

class HashtagMutator
{
    public function __construct(protected HashtagService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new HashtagRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];
        $validator = validator($input, (new HashtagRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input);
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }
}
