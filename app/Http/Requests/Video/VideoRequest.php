<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'video_path' => 'required|string|max:255',
            'type' => 'required|string|in:short,long',
            'is_active' => 'nullable|boolean',
            'publish_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'وصف الفيديو  مطلوب.',
            'description.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'description.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'video_path.required' => 'مسار الفديدو مطلوب.',
            'video_path.string'   => 'مسار الفيديو يجب ان يكون نصا.',
            'video_path.max'      => 'مسار الفيديو طويل جدا .',
        ];
    }
}
