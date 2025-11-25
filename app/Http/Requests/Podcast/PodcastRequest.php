<?php

namespace App\Http\Requests\Podcast;

use Illuminate\Foundation\Http\FormRequest;

class PodcastRequest extends FormRequest
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
            'host_name' => 'required|array',
            'host_name.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'publish_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان البودكاست مطلوب.',
            'title.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'title.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'title.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'host_name.required' => 'اسم الملقى  مطلوب.',
            'host_name.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'host_name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'host_name.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'description.required' => 'الوصف  مطلوب.',
            'description.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'description.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'is_active.boolean' => 'The active flag must be true or false.',
        ];
    }
}
