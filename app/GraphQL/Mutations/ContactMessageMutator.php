<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\ContactMessage\ContactMessageRequest;
use App\Services\ContactMessage\ContactMessageService;
use Illuminate\Validation\ValidationException;

class ContactMessageMutator
{
    public function __construct(protected ContactMessageService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new ContactMessageRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $message = $this->service->create($input);
        $this->service->sendEmailToAdmin($message);

        return $message;
    }

    public function setIsRead($_, array $args)
    {
        $id = (int) $args['id'];
        $is_read = (bool) $args['is_read'];

        return $this->service->setIsRead($id, $is_read);
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }
}
