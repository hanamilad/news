<?php

namespace App\Http\Requests\Ad;

use Illuminate\Foundation\Http\FormRequest;

class AdRequest extends FormRequest
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
            'category_id' => 'required|integer|exists:categories,id',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإعلان مطلوب.',
            'title.array' => 'يجب إرسال العنوان كمجموعة ترجمات.',
            'title.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'category_id.required' => 'تصنيف الإعلان مطلوب.',
            'category_id.integer' => 'رقم التصنيف يجب أن يكون عدداً صحيحاً.',
            'category_id.exists' => 'التصنيف المحدد غير موجود.',
            'start_date.required' => 'تاريخ البداية مطلوب.',
            'start_date.date' => 'صيغة تاريخ البداية يجب أن تكون Y-m-d.',
            'expiry_date.required' => 'تاريخ الانتهاء مطلوب.',
            'expiry_date.date' => 'صيغة تاريخ الانتهاء يجب أن تكون Y-m-d.',
            'expiry_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية.',
            'is_active.boolean' => 'قيمة الحالة يجب أن تكون صحيحة أو خاطئة.',
        ];
    }
}
