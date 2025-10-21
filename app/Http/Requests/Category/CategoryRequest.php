<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'nullable|string|max:255',
            'template_id' =>'required|exists:templates,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب.',
            'name.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'name.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'template_id.required' => 'معرف القالب مطلوب.',
            'template_id.exists' => 'القالب المحدد غير موجود.',
        ];
    }
}
