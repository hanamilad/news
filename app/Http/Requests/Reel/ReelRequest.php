<?php

namespace App\Http\Requests\Reel;

use Illuminate\Foundation\Http\FormRequest;

class ReelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'reel_group_id' => ['nullable', 'exists:reel_groups,id'],
            'description' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'description.ar' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'description.en' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'path' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'type' => [$isUpdate ? 'sometimes' : 'required', 'in:video,image'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'reel_group_id.exists' => 'المجموعة المحددة غير موجودة',
            'description.required' => 'الوصف مطلوب',
            'description.array' => 'يجب أن يكون الوصف مصفوفة',
            'path.required' => 'المسار مطلوب',
            'type.required' => 'نوع الملف مطلوب',
            'type.in' => 'نوع الملف يجب أن يكون صورة أو فيديو',
            'is_active.boolean' => 'يجب أن يكون الحالة النشطة أو غيرها',
            'sort_order.integer' => 'ترتيب الفرز يجب أن يكون رقمًا صحيحًا',
        ];
    }
}
