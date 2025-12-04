<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');
        return [
            'title' => [$id ? 'nullable' : 'required', 'array'],
            'title.*' => 'nullable|string|max:255',
            'styled_description' => [$id ? 'nullable' : 'required', 'array'],
            'styled_description.*' => 'nullable|string',
            'is_urgent' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_main' => 'nullable|boolean',
            'publish_date' => 'nullable|date',
            'category_id' => [$id ? 'nullable' : 'required', 'exists:categories,id'],
            'hashtag_ids' => 'nullable|array',
            'hashtag_ids.*' => 'exists:hashtags,id',
            'links' => 'nullable|array',
            'links.*.video_link' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الخبر مطلوب.',
            'title.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'title.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'title.*.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'styled_description.required' => 'وصف الخبر مطلوب.',
            'styled_description.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'styled_description.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'styled_description.string' => 'The styled description must be a string.',
            'is_urgent.boolean' => 'The urgent flag must be true or false.',
            'is_active.boolean' => 'The active flag must be true or false.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'hashtag_ids.array' => 'Hashtags must be provided as an array.',
            'hashtag_ids.*.exists' => 'One or more selected hashtags are invalid.',
            'links.array' => 'Links must be provided as an array.',
            'links.*.video_link.string' => 'Each video link must be a string.',
        ];
    }
}
