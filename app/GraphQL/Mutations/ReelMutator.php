<?php

namespace App\GraphQL\Mutations;

use App\Services\Reel\ReelService;
use App\Http\Requests\Reel\ReelRequest;
use Illuminate\Validation\ValidationException;

class ReelMutator
{
    public function __construct(protected ReelService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new ReelRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int)$args['id'];
        $input = $args['input'] ?? [];

        $validator = validator($input, (new ReelRequest())->rules());
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
