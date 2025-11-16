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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            // ReelGroup rules
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],

            // Reels array rules
            'reels' => ['sometimes', 'array'],
            'reels.*.id' => ['sometimes', 'exists:reels,id'],
            'reels.*.description' => ['sometimes', 'array'],
            'reels.*.description.ar' => ['sometimes', 'string'],
            'reels.*.description.en' => ['sometimes', 'string'],
            'reels.*.path' => ['sometimes'],
            'reels.*.type' => [$isUpdate ? 'sometimes' : 'required', 'in:video,image,news'],
            'reels.*.news_id' => ['sometimes', 'exists:news,id'],
            'reels.*.is_active' => ['sometimes', 'boolean'],
            'reels.*.sort_order' => ['sometimes', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            // ReelGroup messages
            'title.required' => 'عنوان المجموعة مطلوب',
            'title.array' => 'يجب أن يكون العنوان مصفوفة',
            'title.ar.required' => 'العنوان بالعربية مطلوب',
            'title.en.required' => 'العنوان بالإنجليزية مطلوب',
            'is_active.boolean' => 'يجب أن تكون الحالة نشطة أو غير نشطة',
            'sort_order.integer' => 'ترتيب الفرز يجب أن يكون رقمًا صحيحًا',

            // Reels messages
            'reels.array' => 'يجب أن تكون الريلز مصفوفة',
            'reels.*.id.exists' => 'الريل المحدد غير موجود',
            'reels.*.description.array' => 'يجب أن يكون الوصف مصفوفة',
            'reels.*.type.required' => 'نوع الملف مطلوب',
            'reels.*.type.in' => 'نوع الملف يجب أن يكون صورة أو فيديو',
            'reels.*.is_active.boolean' => 'يجب أن تكون الحالة نشطة أو غير نشطة',
            'reels.*.sort_order.integer' => 'ترتيب الفرز يجب أن يكون رقمًا صحيحًا',
        ];
    }
}
