<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\ReelGroup\ReelGroupRequest;
use App\Services\ReelGroup\ReelGroupService;
use Illuminate\Validation\ValidationException;

class ReelGroupMutator
{
    public function __construct(protected ReelGroupService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];

        $validator = validator($input, (new ReelGroupRequest)->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];

        $validator = validator($input, (new ReelGroupRequest)->rules());

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
