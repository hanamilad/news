<?php

namespace App\Http\Requests\ReelGroup;

use Illuminate\Foundation\Http\FormRequest;

class ReelGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المجموعة مطلوب',
            'title.array' => 'يجب أن يكون العنوان مصفوفة',
            'title.ar.required' => 'العنوان بالعربية مطلوب',
            'title.en.required' => 'العنوان بالإنجليزية مطلوب',
        ];
    }
}