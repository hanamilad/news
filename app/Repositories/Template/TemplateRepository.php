<?php

namespace App\Repositories\Template;

use App\Models\Template;

class TemplateRepository
{
    public function findById(int $id): Template
    {
        return Template::findOrFail($id);
    }

    public function create(array $data): Template
    {
        $template = Template::create([
            'name' => $data['name'],
            'value' => $data['value'],
        ]);

        return $template;
    }

    public function update(Template $template, array $data): Template
    {
        $template->update([
            'name' => $data['name'] ?? $template->name,
            'value' => $data['value'] ?? $template->value,
        ]);

        return $template;
    }

    public function delete(Template $template): bool
    {
        return (bool) $template->delete();
    }
}
