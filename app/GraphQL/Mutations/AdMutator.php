<?php

namespace App\GraphQL\Mutations;

use App\Services\Ad\AdService;
use App\Http\Requests\Ad\AdRequest;
use Illuminate\Validation\ValidationException;

class AdMutator
{
    public function __construct(protected AdService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new AdRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $this->service->create($input, $args['image'] ?? null);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];
        $validator = validator($input, (new AdRequest())->rules());
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

    public function changeStatus($_, array $args)
    {
        $id = (int) $args['id'];
        $isActive = (bool) $args['is_active'];
        return $this->service->changeStatus($id, $isActive);
    }
}