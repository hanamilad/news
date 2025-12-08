<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'title' => [$id ? 'nullable' : 'required', 'string', 'max:255'],
            'description' => 'nullable|string',
            'note' => 'nullable|string',
            'assign_to' => 'nullable|array',
            'assign_to.*' => 'integer|exists:users,id',
            'start_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'is_priority' => 'nullable|boolean',
        ];
    }
}
