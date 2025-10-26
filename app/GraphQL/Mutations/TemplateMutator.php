<?php

namespace App\GraphQL\Mutations;

use App\Services\Template\TemplateService;

class TemplateMutator
{
    public function __construct(protected TemplateService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int)$args['id'];
        $input = $args['input'] ?? [];
        return $this->service->update($id, $input);
    }

    public function delete($_, array $args)
    {
        $id = (int)$args['id'];
        return $this->service->delete($id);
    }
}
