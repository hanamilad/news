<?php

namespace App\Services\ContactMessage;

use App\Repositories\ContactMessage\ContactMessageRepository;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

class ContactMessageService
{
    public function __construct(protected ContactMessageRepository $repository) {}

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function setIsRead(int $id, bool $is_read)
    {
        return $this->repository->update($id, ['is_read' => $is_read]);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function sendEmailToAdmin($message)
    {
        $adminEmail = config('mail.admin_email', 'admin@example.com');
        Mail::to($adminEmail)->send(new ContactMessageMail($message));
    }
}
