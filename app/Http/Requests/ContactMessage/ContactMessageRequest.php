<?php

namespace App\Http\Requests\ContactMessage;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصًا.',
            'name.max' => 'الاسم لا يجب أن يتجاوز 255 حرفًا.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح.',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرفًا.',

            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا.',
            'phone.max' => 'رقم الهاتف لا يجب أن يتجاوز 20 رقمًا.',

            'subject.string' => 'يجب أن يكون الموضوع نصًا.',
            'subject.max' => 'الموضوع لا يجب أن يتجاوز 255 حرفًا.',

            'message.required' => 'الرسالة مطلوبة.',
            'message.string' => 'يجب أن تكون الرسالة نصًا.',
            'message.max' => 'الرسالة لا يجب أن تتجاوز 5000 حرف.',
        ];
    }
}
