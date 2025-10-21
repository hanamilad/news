<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'content' => 'required|array',
            'content.*' => 'nullable|string',
            'author_name' => 'required|array',
            'author_name.*' => 'nullable|string|max:255',
            'author_image' => 'nullable|image',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'محتوى المقالة مطلوب.',
            'content.array' => 'يجب إرسال المحتوى كمجموعة من الترجمات (مثلاً ar, en).',
            'content.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'author_name.required' => 'اسم المؤلف مطلوب.',
            'author_name.array' => 'يجب إرسال أسم المؤلف كمجموعة من الترجمات (مثلاً ar, en).',
            'author_name.*.string'   => 'كل ترجمة يجب أن تكون نص.',
            'author_name.max'      => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'author_image.image'   => 'صورة المؤلف يجب ان تكون صورة ',
            'is_active.boolean' => 'The active flag must be true or false.',
        ];
    }
}
