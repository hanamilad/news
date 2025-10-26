<?php

namespace App\Services\Template;

use App\Repositories\Template\TemplateRepository;
use App\Models\Template;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    use LogActivity;
    public function __construct(protected TemplateRepository $repo) {}

    public function create(array $input): Template
    {
        $user = auth('api')->user();
        $template = $this->repo->create($input);
        $this->log($user->id, 'create', Template::class, $template->id, null, $template->toArray());
        return $template;
    }

    public function update(int $id, array $input): Template
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $template = $this->repo->findById($id);
            $old = $template->toArray();
            $updated = $this->repo->update($template, $input);
            $this->log($user->id, 'update', Template::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $template = $this->repo->findById($id);
            $old = $template->toArray();
            $deleted = $this->repo->delete($template);
            $this->log($user->id, 'delete', Template::class, $template->id, $old, null);
            return $deleted;
        });
    }
}
