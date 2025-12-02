<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->input('id');
        return [
            'name' => [$userId ? 'nullable' : 'required', 'string', 'max:255'],
            'email' => [
                $userId ? 'nullable' : 'required',
                'email',
                $userId
                    ? Rule::unique('users', 'email')->ignore((int) $userId)
                    : Rule::unique('users', 'email'),
            ],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نصًا.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفًا.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',

            'phone_number.string' => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone_number.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',

            'job_title.string' => 'المسمى الوظيفي يجب أن يكون نصًا.',
            'job_title.max' => 'المسمى الوظيفي يجب ألا يتجاوز 255 حرفًا.',

            'role_ids.array' => 'يجب إرسال الأدوار كمصفوفة.',
            'role_ids.*.integer' => 'معرف الدور يجب أن يكون رقمًا صحيحًا.',
            'role_ids.*.exists' => 'الدور المحدد غير موجود.',

            'permissions.array' => 'يجب إرسال الصلاحيات كمصفوفة.',
            'permissions.*.integer' => 'معرف الصلاحية يجب أن يكون رقمًا صحيحًا.',
            'permissions.*.exists' => 'الصلاحية المحددة غير موجودة.',
        ];
    }
}
