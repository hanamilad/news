<?php

namespace App\GraphQL\Mutations;

use App\Services\News\NewsService;
use App\Http\Requests\News\NewsRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class NewsMutator
{
    public function __construct(protected NewsService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $files = $this->extractFiles($args);

        $validator = validator($input, (new NewsRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input, $files);
    }

    public function update($_, array $args)
    {
        $id = (int)$args['id'];
        $input = $args['input'] ?? [];
        $files = $this->extractFiles($args);

        if ($id) {
            request()->route()->setParameter('id', $id);
        }

        $validator = validator($input, (new NewsRequest())->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input, $files);
    }

    public function delete($_, array $args)
    {
        $id = (int)$args['id'];
        return $this->service->delete($id);
    }

    public function search($_, array $args) {}


    protected function extractFiles(array $args): array
    {
        if (!empty($args['images']) && is_array($args['images'])) {
            return array_filter($args['images'], fn($f) => $f instanceof UploadedFile);
        }
        return [];
    }
}
