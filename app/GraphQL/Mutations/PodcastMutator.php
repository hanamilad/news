<?php

namespace App\GraphQL\Mutations;

use App\Services\Podcast\PodcastService;
use App\Http\Requests\Podcast\PodcastRequest;
use Illuminate\Validation\ValidationException;

class PodcastMutator
{
    public function __construct(protected PodcastService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new PodcastRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input, $args['audio'] ?? null);
    }

    public function update($_, array $args)
    {
        $id = (int)$args['id'];
        $input = $args['input'] ?? [];

        $validator = validator($input, (new PodcastRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input, $args['audio'] ?? null);
    }

    public function delete($_, array $args)
    {
        $id = (int)$args['id'];
        return $this->service->delete($id);
    }
}
