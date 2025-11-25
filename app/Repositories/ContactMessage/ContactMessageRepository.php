<?php

namespace App\Repositories\ContactMessage;

use App\Models\ContactMessage;

class ContactMessageRepository
{
    public function create(array $data)
    {
        return ContactMessage::create($data);
    }

    public function update(int $id, array $data)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update($data);

        return $message;
    }

    public function delete(int $id)
    {
        return ContactMessage::destroy($id);
    }
}
