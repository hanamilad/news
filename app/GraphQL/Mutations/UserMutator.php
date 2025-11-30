<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\User\UserRequest;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserMutator
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new UserRequest)->rules(), (new UserRequest)->messages());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input, $args['logo'] ?? null);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];
        if ($id) {
            request()->route()->setParameter('id', $id);
        }

        $validator = validator($input, (new UserRequest)->rules(), (new UserRequest)->messages());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input, $args['logo'] ?? null);
    }

    public function deactivate($_, array $args)
    {
        $id = (int) $args['id'];
        $validator = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']], ['id.exists' => 'المستخدم غير موجود.']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->deactivate($id);
    }

    public function restore($_, array $args)
    {
        $id = (int) $args['id'];
        $validator = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']], ['id.exists' => 'المستخدم غير موجود.']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            return $this->service->restore($id);
        } catch (ModelNotFoundException) {
            throw new \Exception('لا يمكن الاستعادة لأن المستخدم غير معطل.');
        }
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];
        $validator = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']], ['id.exists' => 'المستخدم غير موجود.']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->delete($id);
    }

    public function manageAccess($_, array $args)
    {
        $data = [
            'user_id' => (int) $args['user_id'],
            'type' => (string) $args['type'],
            'access_id' => (int) $args['access_id'],
        ];

        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', 'in:ROLE,PERMISSION'],
            'access_id' => [
                'required',
                'integer',
                ($data['type'] ?? null) === 'ROLE'
                    ? Rule::exists('roles', 'id')
                    : Rule::exists('permissions', 'id'),
            ],
        ];

        $messages = [
            'user_id.exists' => 'المستخدم المحدد غير موجود.',
            'type.in' => 'نوع الوصول يجب أن يكون ROLE أو PERMISSION.',
            'access_id.exists' => $data['type'] === 'ROLE' ? 'الدور المحدد غير موجود.' : 'الصلاحية المحددة غير موجودة.',
        ];

        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->assignAccess($data['user_id'], $data['type'], $data['access_id']);
    }

    public function removeAccess($_, array $args)
    {
        $data = [
            'user_id' => (int) $args['user_id'],
            'type' => (string) $args['type'],
            'access_id' => (int) $args['access_id'],
        ];

        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', 'in:ROLE,PERMISSION'],
            'access_id' => [
                'required',
                'integer',
                ($data['type'] ?? null) === 'ROLE'
                    ? Rule::exists('roles', 'id')
                    : Rule::exists('permissions', 'id'),
            ],
        ];

        $messages = [
            'user_id.exists' => 'المستخدم المحدد غير موجود.',
            'type.in' => 'نوع الوصول يجب أن يكون ROLE أو PERMISSION.',
            'access_id.exists' => $data['type'] === 'ROLE' ? 'الدور المحدد غير موجود.' : 'الصلاحية المحددة غير موجودة.',
        ];

        $validator = validator($data, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->removeAccess($data['user_id'], $data['type'], $data['access_id']);
    }
}
