<?php

namespace App\Http\Requests\Reel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'type' => ['required', Rule::in(['video', 'image'])],
            'is_active' => 'nullable|boolean',
        ];
        switch ($this->type) {
            case 'video':
                $rules['path'] = [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (!preg_match('/(youtube\.com|youtu\.be)/i', $value)) {
                            $fail('يجب أن يكون رابط الفيديو من يوتيوب.');
                        }
                    }
                ];
                break;
            case 'image':
                $rules['path'] = $this->hasFile('path')
                    ? ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048']
                    : ['required', 'string', 'url', 'max:255'];
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'description.required' => 'وصف الفيديو مطلوب.',
            'description.array' => 'يجب إرسال الوصف كمجموعة من الترجمات (مثلاً ar, en).',
            'description.*.string' => 'كل ترجمة يجب أن تكون نص.',
            'path.required' => 'مسار الفيديو أو الصورة مطلوب.',
            'path.regex' => 'يجب أن يكون رابط الفيديو من يوتيوب.',
            'path.url' => 'رابط الصورة غير صالح.',
            'path.mimes' => 'يجب أن تكون الصورة من نوع jpg أو jpeg أو png أو gif أو webp.',
            'path.max' => 'الملف أو الرابط لا يجب أن يتجاوز 2 ميغابايت أو 255 حرف.',
            'type.required' => 'نوع الوسائط مطلوب.',
            'type.in' => 'النوع يجب أن يكون إما video أو image.',
            'is_active.boolean' => 'حالة العنصر يجب أن تكون صحيحة أو خاطئة.',
        ];
    }
}
