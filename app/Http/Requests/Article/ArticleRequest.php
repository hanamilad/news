<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:255',
            'content' => 'required|array',
            'content.*' => 'nullable|string',
            'author_name' => 'required|array',
            'author_name.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'publish_date' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المقالة مطلوب.',
            'title.array' => 'يجب إرسال العنوان كمجموعة من الترجمات (مثلاً ar, en).',
            'title.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'content.required' => 'محتوى المقالة مطلوب.',
            'content.array' => 'يجب إرسال المحتوى كمجموعة من الترجمات (مثلاً ar, en).',
            'content.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'author_name.required' => 'اسم المؤلف مطلوب.',
            'author_name.array' => 'يجب إرسال أسم المؤلف كمجموعة من الترجمات (مثلاً ar, en).',
            'author_name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'author_name.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'is_active.boolean' => 'The active flag must be true or false.',
        ];
    }
}
