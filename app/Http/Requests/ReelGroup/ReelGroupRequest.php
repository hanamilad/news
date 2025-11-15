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
            'sort_order' => ['sometimes', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المجموعة مطلوب',
            'title.array' => 'يجب أن يكون العنوان مصفوفة',
            'title.ar.required' => 'العنوان بالعربية مطلوب',
            'title.en.required' => 'العنوان بالإنجليزية مطلوب',
            'is_active.boolean' => 'يجب أن يكون الحالة النشطة أو غيرها',
            'sort_order.integer' => 'ترتيب الفرز يجب أن يكون رقمًا صحيحًا',
        ];
    }
}
