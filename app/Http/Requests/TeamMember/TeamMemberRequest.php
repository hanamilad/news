<?php

namespace App\Http\Requests\TeamMember;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
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
            'position' => 'required|array',
            'position.*' => 'nullable|string|max:255',
            'bio' => 'nullable|array',
            'bio.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.array' => 'يجب إرسال الاسم كمجموعة من الترجمات (مثلاً ar, en).',
            'name.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'name.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'position.required' => 'اسم المنصب مطلوب.',
            'position.array' => 'يجب إرسال المنصب كمجموعة من الترجمات (مثلاً ar, en).',
            'position.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'position.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'bio.array' => 'يجب إرسال السيرة الذاتية كمجموعة من الترجمات (مثلاً ar, en).',
            'bio.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'bio.max' => 'كل ترجمة يجب ألا تتجاوز 255 حرف.',
            'is_active.boolean' => 'The active flag must be true or false.',
        ];
    }
}
