<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\Video\VideoRequest;
use App\Services\Video\VideoService;
use Illuminate\Validation\ValidationException;

class VideoMutator
{
    public function __construct(protected VideoService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new VideoRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input, $args['video'] ?? null);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];

        $validator = validator($input, (new VideoRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input, $args['video'] ?? null);
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }
}
