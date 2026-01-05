<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function creationRules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.*' => 'nullable|string|max:255',

            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:255',

            'show_in_navbar' => 'nullable|boolean',
            'show_in_homepage' => 'nullable|boolean',
            'show_in_grid' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',

            'grid_order' => 'nullable|integer',

            'template_id' => ['required', 'exists:templates,id'],
        ];
    }

    public static function updateRules(): array
    {
        return [
            'name' => ['sometimes', 'array'],
            'name.*' => 'nullable|string|max:255',

            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:255',

            'show_in_navbar' => 'nullable|boolean',
            'show_in_homepage' => 'nullable|boolean',
            'show_in_grid' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',

            'grid_order' => 'nullable|integer',

            'template_id' => ['sometimes', 'exists:templates,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب.',
            'name.array' => 'يجب إرسال الاسم كمجموعة من الترجمات.',
            'name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'name.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',

            'description.array' => 'الوصف يجب أن يكون مجموعة ترجمات.',
            'description.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'description.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',

            'template_id.required' => 'معرف القالب مطلوب.',
            'template_id.exists' => 'القالب المحدد غير موجود.',
        ];
    }
}
