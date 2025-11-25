<?php

namespace App\Http\Requests\Hashtag;

use Illuminate\Foundation\Http\FormRequest;

class HashtagRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم الفئة مطلوب.',
            'name.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'name.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
        ];
    }
}
